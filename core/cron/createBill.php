<?php
/**
*  定时任务
*  每月月初生成月账单。
*  初拟定是每个月执行一次。
*
*/
	include(dirname(__FILE__).'/db.php');

	$log = createMSettle( $conn );
	echo date('Y-m-d H:i:s'),"\n";
	print_r($log);

	$log = createBill( $conn );
	echo date('Y-m-d H:i:s'),"\n";
	print_r($log);

	mysql_close($conn);
	exit;

	function createBill( $conn ){
		$limit = 2;//一次执行生成多少张账单
		$table = '`db_member_credit_detail`';

		//只执行上个月的如要执行到具体日期，改为 date( 'Y-m-d')
		$t = date( 'Y-m');
		$where = "createTime< '$t' and state = 0 and isCheck= 1";

	//	$sql = "select memberId ,sum(amount) as amount from $table where $where group by memberId limit 0,$limit";
		$sql = "select memberId ,sum(amount) as amount from $table where $where group by memberId";
		$fetch = mysql_query( $sql,$conn );
		$log = array();
		while ( $row = mysql_fetch_array( $fetch ) ){
			$memberId = $row['0'];
			$credit = $row['1'];
			//生成账单
			$insertbill = "insert into  `db_member_bill` (`memberId`, `credit`,`createTime`) values ('$memberId','$credit',now())";
			mysql_query( $insertbill,$conn );

			$billId = mysql_insert_id($conn);

			//账单明细
			$where2 = $where." and memberId ='$memberId'";
			$insertSql = "INSERT INTO `db_member_bill_detail`  (`billId`,`amount`, `orderId`,`createTime`,`mark`)
						SELECT $billId,`amount`,`orderId`, `createTime`,`mark` from $table WHERE $where2";
			mysql_query( $insertSql,$conn );

			//更改状态为已入账
			$updateSql = "update $table  set state = 1 where $where2 ";
			mysql_query( $updateSql,$conn );

			//插入一条账单明细记录
			$mark = date('Y-m').'账单';
			$insertSql = "insert into  $table (`memberId`, `amount`,`createTime`,`orderId`,`state`,`isCheck`,`mark`)
							values ('$memberId','$credit',now(),'0','0','1','$mark')";
			mysql_query( $insertSql,$conn );

			$log[] = 'create bill of memberId: '.$memberId.' of '.date('Y-m');
		}

		return $log;
	}

	function createMSettle( $conn ){
		$t = date( 'Y-m' );
		$t1 = date( 'Y-m-d',strtotime( $t ) );

		$sql = "update `db_order_settlement_month` set `isDone` = 1 where `month`<'$t1' and `receipt` = `payments`";
		mysql_query( $sql,$conn );
		$num = mysql_affected_rows();
		$log[] = 'update `db_order_settlement_month` for '.$num.' records.';
		return $log;
	}

?>