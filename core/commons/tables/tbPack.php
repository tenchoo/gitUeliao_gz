<?php
/**
 * 分拣信息表
 * @author liang
 * @version 0.2
 *
 * @property int    $orderProductId   订单产品ID
 * @property int    $state  		  分拣状态
 * @property int    $orderId  		  订单ID
 * @property int    $productId		  产品ID
 * @property int    $warehouseId  	  分拣仓库ID
 * @property int    $positionId  	  分区ID
 * @property number $num 			  需分拣数量
 * @property string $singleNumber	  单品编码
 */

class tbPack extends CActiveRecord {
	const STATE_WAIT = 0;//待分拣
	const STATE_DONE = 1;//已分拣
	const STATE_MERGE = 2;//已归单
	const STATE_CANCLE = 10;//已取消分拣


	public static function model($className=__CLASS__) {
		return parent::model( $className );
	}

	public function tableName() {
		return '{{pack}}';
	}

	public function relations() {
		return array(
			'detail' => array(self::HAS_MANY,'tbPackDetail','orderProductId'),
		);
	}
}
