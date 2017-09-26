<?php
/**
* 定时任务
* 1.发货后，自动确认收货
* 2.下单后，超时未支付订单，系统自动关闭。
*  初拟定是每分钟执行一次。
*
*/
	include(dirname(__FILE__).'/db.php');

	$log = updateMSettle( $conn );

	$config = mysql_query( "SELECT `value`  FROM `db_config` where `key`='order_comfirm' limit 1" ,$conn);
	$row = mysql_fetch_array($config);
	if( $row && $row['value'] >=1 ){
		$t = time();
		$t = date( 'Y-m-d H:i:s', $t - $row['value']*3600*24 );

		//1.生成订单跟踪记录
		$insertSql = "INSERT INTO `db_order_message`  ( `orderId`,`createTime`,`subject`)
						SELECT `orderId` , now( ) , '系统自动确认收货' from `db_order` WHERE `state` =4
						AND EXISTS (
							SELECT NULL
							FROM `db_order_delivery` od
							WHERE od.`orderId` = `db_order`.`orderId` AND od.`state` =0 AND od.`createTime` <= '$t' )";
		mysql_query( $insertSql,$conn );
		$num = mysql_affected_rows();
		if( $num>0 ){
			$log[] = 'INSERT INTO `db_order_message` for '.$num.' records of auto comfirm order.';
		}


		//发货后，自动确认收货
		$sql = "update `db_order` o,`db_order_delivery` d set o.`state`=6 ,o.`dealTime` = now() ,d.`receivedType` = 2,d.`receivedTime` = now(),d.`state`=1 where o.`orderId` = d.`orderId` and  o.`state` = 4  and d.`state`=0 and d.`createTime` <= '$t' ";
		mysql_query( $sql,$conn );
		$num = mysql_affected_rows();
		if( $num>0 ){
			$log[] = 'update `db_order` ,`db_order_delivery` for '.$num.' records of auto comfirm order.';
		}
	}else{
		$log[] = 'Could not config the order_comfirm. ';
	}

	//查找系统设置里的订单待付款时间,单位为小时
	$config = mysql_query( "SELECT `value`  FROM `db_config` where `key`='pay_save_time' limit 1" ,$conn);
	$row = mysql_fetch_array($config);
	if( !$row ){
		die('Could not config the pay_save_time. ');
	}


	$t = time();
	$t = date( 'Y-m-d H:i:s', $t - $row['value']*3600 );

	//需操作的订单的查找条件，这句非常重要。
	$orderWhere = "`createTime`<= '$t' and `payModel` = 0 and `orderType` !='2' and state =0 ";

 	//1.新增取消记录
	$insertSql = "INSERT IGNORE INTO `db_order_close`  ( `orderId`, `opId`,`opType` ,`createTime`,`reason`)
				SELECT `orderId`,'0','3', now(),'超时未支付，系统自动关闭' from `db_order` WHERE $orderWhere";
	mysql_query( $insertSql ,$conn);
	$num = mysql_affected_rows();
	if( $num>0 ){
		$log[] = 'INSERT INTO `db_order_close` for '.$num.' records.';
	}


	//2.生成订单跟踪记录
	$insertSql = "INSERT INTO `db_order_message`  ( `orderId`,`createTime`,`subject`)
					SELECT `orderId`, now(),'超时未支付，系统自动关闭' from `db_order` WHERE $orderWhere";
	mysql_query( $insertSql,$conn );
	$num = mysql_affected_rows();
	if( $num>0 ){
		$log[] = 'INSERT INTO `db_order_message` for '.$num.' records.';
	}

	//3.更改订单状态为已关闭
	$sql = "UPDATE `db_order` SET state = '7' WHERE $orderWhere ";
	mysql_query( $sql,$conn );
	$num = mysql_affected_rows();
	if( $num>0 ){
		$log[] = 'UPDATE `db_order` for '.$num.' records.';
	}

	//4.释放可销售量
	$lockSql = "DELETE FROM `db_storage_lock` WHERE exists (select null from  `db_order` where `db_order`.orderId = `db_storage_lock`.`orderId` and `db_order`.state>=4 )";
	mysql_query( $lockSql,$conn );
	$num = mysql_affected_rows();
	if( $num>0 ){
		$log[] = 'delete `db_storage_lock` for '.$num.' records.';
	}

	//释放仓库库存
	$sql = 'DELETE FROM `db_warehouse_lock`
 WHERE EXISTS ( SELECT NULL FROM `db_order_close` WHERE `db_order_close`.`orderId` = `db_warehouse_lock`.`orderId` AND `db_order_close`.`createTime` >=DATE_SUB(NOW(), INTERVAL 2 MINUTE ) ) AND `type`=4 ';
	mysql_query( $sql,$conn );
	$num = mysql_affected_rows();
	if( $num>0 ){
		$log[] = 'delete `db_warehouse_lock` for '.$num.' records.';
	}

	//5.记录操作日志
	echo date('Y-m-d H:i:s'),"\n";
	print_r($log);

	mysql_close($conn);


	function updateMSettle( $conn ){
		$t = date( 'Y-m' );

		$t1 = date( 'Y-m-d',strtotime( $t ) );
		$t = strtotime( $t1 );
		$t2 = date( 'Y-m-d',strtotime( '+1 months',$t ) );

		//INSERT INTO `db_order_settlement_month` (`month`,`memberId`,`userId`,`payments`)
		$sql = "SELECT DATE_FORMAT(S.`createTime`,'%Y-%m-01') AS `month`,O.`memberId`,O.`userId`,
			SUM(S.`productPayments`) AS `payments`
FROM db_order_settlement S , db_order O WHERE O.`orderId` = S.`orderId` and S.`createTime`>='$t1' AND S.`createTime`<'$t2' and O.`payModel` = '1' GROUP BY O.`memberId` ORDER BY O.createTime";
		$query = mysql_query( $sql ,$conn);
		$log = array();
		while( $row = mysql_fetch_array( $query ) ){
			$ssql = "select * from `db_order_settlement_month` where `month`='".$row['month']."' and `memberId` = '".$row['memberId']."'";
			$squery = mysql_query( $ssql ,$conn);
			$_row = mysql_fetch_array( $squery );
			if( $_row ){
				if( $row['payments']!= $_row['payments']  ){
					$sql = " update `db_order_settlement_month` set `isDone` = 0 ,`payments`='".$row['payments']."' where id = '".$_row['id']."'";
					mysql_query( $sql,$conn );
					$log[] = 'UPDATE `db_order_settlement_month` for month: '.$row['month'].' memberId:'.$row['memberId'];
				}
			}else{
				$sql = "INSERT INTO `db_order_settlement_month` (`month`,`memberId`,`userId`,`payments`)
					values( '".$row['month']."','".$row['memberId']."','".$row['userId']."','".$row['payments']."' )";
				mysql_query( $sql,$conn );
				$log[] = 'INSERT `db_order_settlement_month` for month: '.$row['month'].' memberId:'.$row['memberId'];
			}
		}
		return $log;
	}
?>