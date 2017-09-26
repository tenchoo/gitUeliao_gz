<?php
/**
 * 产品异动报表
 *
 */

class ExportController extends Controller
{

    public function init()
    {
        parent::init();
        Yii::import('libs.vendors.phpexcel.*');
    }

    public function actionIndex()
    {
        $start  = Yii::app()->request->getQuery('start');
		$end  = Yii::app()->request->getQuery('end',date('Y-m-d'));
        $product = Yii::app()->request->getQuery('product');
		$warehouseId = Yii::app()->request->getQuery('warehouseId');

		$warehouse = tbWarehouseInfo::model()->getAll();
		$data = $this->getList( $warehouseId,$product,$start,$end );
		if( empty( $data ) ){
			$data['data_list'] = false;
		}

        $this->render('index', array_merge( [
            'product'   => $product,
            'start'     => $start,
            'end'       => $end,
			'warehouse' => $warehouse,
			'warehouseId'=>$warehouseId,
        ],$data));
    }


	/**
	* 取得异动报表明细
	* @param integer $warehouseId 仓库ID
	* @param string $serial 单品编码
	* @param data $month 月份
	* @return array
	*/
	private function getList( $warehouseId,$serial,$start,$end ){
		if( !is_numeric( $warehouseId ) || empty( $serial ) || empty( $start ) ) return ;

		$t1 = date('Y-m-d', strtotime($start));
		$t2 = date('Y-m-d', strtotime($end));
		if( $t1 > $t2 ){
			$this->setError( array('开始时间不能大于结束时间') );
			return ;
		}

		if( $t2 > date('Y-m-d') ){
			$this->setError( array('不能大于当前日期') );
			return ;
		}

		if ( !preg_match("/^[a-zA-Z0-9\-]{6,20}$/", $serial) ) {
			$this->setError( array('产品编码不正确') );
			return ;
		}

		$cacheName = implode('_',array( $warehouseId,$serial,$start,$end ));
		$data = json_decode( Yii::app()->cache->get( $cacheName ),true );//获取缓存
		if( empty($data) ){
			$data = $this->fetchData( $t1,$t2,$warehouseId,$serial );
			Yii::app()->cache->set( $cacheName ,CJSON::encode( $data  ),600 );
		}
		return $data;
	}

	private function fetchData( $t1,$t2,$warehouseId,$serial ){
		$criteria = new CDbCriteria;
		$criteria->select = 't.createTime';//默认*

		$criteria->addCondition(" exists ( select null from {{product_stock}} s where s.`productId` = t.`productId` and s.`singleNumber` ='$serial')");

		$product = tbProduct::model()->find( $criteria );
		if( !$product ){
			$this->setError( array('产品编码不存在') );
			return ;
		}


		$t2 = date('Y-m-d', strtotime("+1 day",strtotime($t2) ));
		if( $t2 < $product->createTime ){
			$this->setError( array( $end.'还未增加此产品，无库存数据') );
			return ;
		}
		if ( $t1 > $product->createTime  ){
			//计算结余
			$surplus = $this->getSurplus( $warehouseId,$serial,$t1 );
		}else{
			//结余,如果开始计算时间小于产品发布时间，那么结余为0
			$surplus = 0;
		}

		$data['surplus'] = $surplus ;


		$sql ="SELECT * FROM
			( ( SELECT 1 AS `type`,d.`warrantId` AS id,SUM( d.`num`) AS `num`,w.`createTime`,w.`operator`,w.`source`,w.`postId` AS `sourceId`
      FROM {{warehouse_warrant_detail}} d
      LEFT JOIN {{warehouse_warrant}} w ON d.`warrantId` = w.`warrantId`  AND w.`state` =0
      LEFT JOIN {{warehouse_position}} p ON p.`positionId` = d.`positionId`
			WHERE d.`warrantId` = w.`warrantId`
				  AND d.`singleNumber` = '$serial'
				  AND p.`warehouseId` = '$warehouseId'
				  AND w.`createTime` >= '$t1' AND w.`createTime` < '$t2'
				  AND w.`state` =0
			 GROUP BY d.`warrantId`
				)
			 UNION ALL
			 ( SELECT 2 AS `type`,d.`outboundId` AS id,SUM( d.`num`) AS `num`,w.`createTime`,w.`operator`,w.`source`,w.`sourceId`
			 FROM {{warehouse_outbound_detail}} d ,{{warehouse_outbound}} w
			WHERE d.`outboundId` = w.`outboundId`
				  AND d.`singleNumber` = '$serial'
				  AND w.`warehouseId` = '$warehouseId'
				  AND w.`createTime` >= '$t1' AND w.`createTime` < '$t2'
			 GROUP BY d.`outboundId`
				   )
			  UNION ALL
			  (
			 SELECT 3 AS `type`, sc.`stocktakingId` AS id,sc.`num`, sc.`createTime`,s.`checkUser` AS `operator` , 0 AS `source`, 0 AS `sourceId`
			 FROM {{stocktaking_count}} sc,{{stocktaking}} s
			 WHERE sc.`stocktakingId` =s.`stocktakingId`
				AND sc.`singleNumber` = '$serial'
				AND sc.`createTime` >= '$t1' AND sc.`createTime` < '$t2'
				AND sc.`warehouseId` = '$warehouseId'
			  )
			 ) AS detail
			 ORDER BY `createTime` ASC;";

		$cmd = Yii::app()->db->createCommand( $sql );
		$list = $cmd->queryAll();

		$totalIn = $totalOut = array();
		foreach ( $list as &$val ){
			if( $val['type'] == '2' ){
				$val['in'] = '';
				$val['out']  = $val['num'];
				$totalOut[] = $val['num'];
				$surplus = bcsub( $surplus,$val['num'],2 );
			}else{
				$val['in']  = $val['num'];
				$val['out']   = '';
				$totalIn[] = $val['num'];

				if( $val['type'] == '3' || ( $val['type'] == '1' && $val['source'] == '2' ) ){
					$surplus = $val['num'];
				}else{
					$surplus = bcadd( $surplus,$val['num'],2 );
				}
			}

			$val['surplus'] = $surplus;
			$val['class'] = $this->setClass( $val['type'],$val['source'] );

			//这一行一定要放在最后，因为source从ID转变成中文了。
			$val['source'] = $this->sourceType( $val['type'],$val['source'] );

		}

		$this->saveSurplus( $t2,$warehouseId,$serial,$val['surplus'] );

		$data['totalIn'] = array_sum( $totalIn );
		$data['totalOut'] = array_sum( $totalOut );
		$data['data_list'] = $list ;
		return $data;

	}

	/**
	* 导出excel
	*/
    public function actionExcel()
    {
        $start  = Yii::app()->request->getQuery('start');
		$end  = Yii::app()->request->getQuery('end',date('Y-m-d'));
        $product = Yii::app()->request->getQuery('product');
		$warehouseId = Yii::app()->request->getQuery('warehouseId');

		$warehouse = tbWarehouseInfo::model()->getAll();
		$data = $this->getList( $warehouseId,$product,$start,$end );
		if( empty( $data ) ){
			return ;
		}

		$ware = isset($warehouse[$warehouseId])?$warehouse[$warehouseId]:'';

		$lists = array(
						array('所属仓库：',$ware,'产品：', $product,'报表区间:', $start . '--' . $end  ),
						array(),
						array('时间', '单号', '来源', '来源单号', '操作人', '入库', '出库', '结余'),
						array('上期结余', '', '', '', '', '', '', $data['surplus']),

					);

		foreach ( $data['data_list'] as $val ){
			$lists[] = array($val['createTime'], $val['id'],$val['source'],$val['sourceId'],$val['operator'],$val['in'],$val['out'],$val['surplus']);

		}

		$lists[] = array('本期结余', '', '', '', '', $data['totalIn'], $data['totalOut'], $val['surplus']);
		$excel = new ExcelFile('产品异动报表');
        $excel->createExl($lists);
        Yii::app()->end();
    }

	/**
	* 单据类型
	*/
    private function sourceType( $type,$source )
    {
		if( $type == '3') return '盘点单';

		$arr = array(
			'1' => array( '0'=>'采购入库','1'=>'调拨入库','2'=>'盘点单','3'=>'入库单','4'=>'退货入库单','5'=>'调整单' ),
			'2' => array( '0'=>'发货出货','1'=>'调拨出库','2'=>'调整出库' )
			);
		return isset( $arr[$type][$source] )?$arr[$type][$source]:'';
    }

	/**
	* 根据类型显示不同的底色
	*/
	private function setClass( $type,$source ){
		$class = array('1'=>'alert-success','2'=>'alert-info','3'=>'alert-danger' );

		if( $type == '1' && $source == '2' ) return $class['3'];

		return isset( $class[$type] )?$class[$type]:'';
	}


	/**
	* 计算之前结余
	*/
    private function getSurplus($warehouseId,$serial,$t1 ) {
		//不统计2016-06-04之前的
		if( $t1 < '2016-06-04' ){
			return 0;
		}

		$start = $num = null;

		$surplus = new tbWarehouseSurplus();

		//查找最靠近$t1时间节点的记录，若无节余数据，实时计算
		$model = $surplus->find(	array(
										'condition'=>'singleNumber=:s and warehouseId=:w  and date <=:t1',
										'params'=>array( ':s'=>$serial,':w'=>$warehouseId ,':t1'=>$t1 ),
										'order'=>'date desc'
								)	);
		if( $model ){
			$num 	= $model->total;
			$start 	= $model->date;

			if( $t1 == $start ){
				return $num;
			}
		}

		//查找从 $start - $t1时间段内有没有盘点单，若有盘点单，以盘点时间为节点，并以盘点数量为初始数量
		$condition = 'singleNumber=:s and warehouseId=:w and createTime<:time ';
		$params = array( ':s'=>$serial,':w'=>$warehouseId  ,':time'=>$t1 );
			if( $start  ){
			$condition .= 'and createTime>=:start';
			$params[':start'] = $start;
		}

		$model = tbStocktakingCount::model()->find(	array(
										'condition'=>$condition,
										'params'=>$params,
										'order'=>'createTime desc'
										)	);
		if( $model ){
			$num = $model->num;
			$start = $model->createTime;
		}


		$twhere = '';
		if( $start  ){
			$twhere = " AND w.`createTime` >= '$start' ";
		}

		//查找 $start - $t1 的异动数据
		$sql ="( SELECT 1 AS `type`,SUM( d.`num`) AS `num`
				FROM {{warehouse_warrant_detail}} d
				LEFT JOIN {{warehouse_warrant}} w ON d.`warrantId` = w.`warrantId`  AND w.`state` =0
				LEFT JOIN {{warehouse_position}} p ON p.`positionId` = d.`positionId`
				WHERE d.`warrantId` = w.`warrantId`
				  AND d.`singleNumber` = '$serial'
				  AND p.`warehouseId` = '$warehouseId'
				  $twhere AND w.`createTime` < '$t1'
				  AND w.`state` =0
				)
			 UNION ALL
			 ( SELECT 2 AS `type`,SUM( d.`num`) AS `num`
				FROM {{warehouse_outbound_detail}} d
				WHERE exists (
					select null from {{warehouse_outbound}} w
					where d.`outboundId` = w.`outboundId`
						AND w.`warehouseId` = '$warehouseId'
						$twhere
						AND w.`createTime` < '$t1'
				)
				  AND d.`singleNumber` = '$serial'
				) ";

		$cmd = Yii::app()->db->createCommand( $sql );
		$list = $cmd->queryAll();

		$num = (int)$num + (int)$list['0']['num'] - (int)$list['1']['num'];
		$this->saveSurplus( $t1,$warehouseId,$serial,$num );
		return $num;
    }

	/**
	* 把此结余数据写入数据库记录，时间节点为两个月，
	* 比如当前时间为10月，则9月份的数据可能波动，先不写入结余记录，但8月份的可以写入结余记录。
	*
	*/
	private function saveSurplus( $t1,$warehouseId,$serial,$num ){
		if( $t1 < date('Y-m-01', strtotime("-1 month")) ){
			$surplus = new tbWarehouseSurplus();
			$model = $surplus->find(	array(
										'condition'=>'singleNumber=:s and warehouseId=:w  and date =:t1',
										'params'=>array( ':s'=>$serial,':w'=>$warehouseId ,':t1'=>$t1 ),
										'order'=>'date desc'
								)	);
			if( $model ){
				if( $model->total != $num ){
					$model->total = $num;
					$model->save();
				}
			}else{
				$surplus->warehouseId = $warehouseId;
				$surplus->date = $t1;
				$surplus->singleNumber = $serial;
				$surplus->total = $num;
				$surplus->save();
			}
		}
	}
}
