<?php
/**
 * 发货订单明细
 * @package CActiveRecord
 */
class tbOrderPost2Product extends CActiveRecord {

	public $postProId;	//发货单明细单号
	public $source;	//订单来源
	public $isAssign;	//订单匹配
	public $postId;	//发货单号
	public $purchaseId;	//采购单编号
	public $purchaseProId;	//采购单明细编号
	public $postTotal;	//发货数量
	public $comment;	//备注

	const STATE_ASSIGN   = 1;
	const STATE_UNASSIGN = 0;
	
	public static function model( $className=__CLASS__) {
		return parent::model( $className );
	}
	
	public function tableName() {
		return '{{order_post2_product}}';
	}
	
	public function primaryKey() {
		return 'postProId';
	}
	
	public function init() {
		parent::init();
		$this->source   = tbOrderPurchase2::FROM_REQUEST;
		$this->isAssign = self::STATE_UNASSIGN;
	}

	public function rules() {
		return array(
			array('postId,postTotal,purchaseId,purchaseProId','required'),
			array('comment,source','safe')
		);
	}

	public function relations() {
		return array(
			'details' => array(self::BELONGS_TO,'tbOrderPurchasingProduct','purchaseProId')
		);
	}

	public function getProductCode() {
		if(!isset($this->details) || is_null($this->details)) {
			throw new CException('Not found method');
		}
		return $this->details->productCode;
	}

	public function getQuantity() {
		if(!isset($this->details) || is_null($this->details)) {
			throw new CException('Not found method');
		}
		return $this->details->quantity;
	}

	/**
	 * 订单已匹配
	 * @return bool
	 * @throws CException
	 */
	public function toAssign() {
		if($this->isNewRecord) {
			throw new CException('New record cannot perform this action');
		}
		$this->isAssign = self::STATE_ASSIGN;
		if($this->save()) {
			$post = tbOrderPost2::model()->findByPk($this->postId);
			if(is_null($post)) {
				throw new CException('Not found post record');
			}

			return $post->toAssign();
		}
		return false;
	}

	/**
	 * 获取客户订单可商品列表
	 * @return array
	 * @throws CDbException
	 * @throws CHttpException
	 */
	public function getAssignProduct() {
		$tPurshace = tbOrderPurchasing::model()->tableName();
		$tPurshacePro = tbOrderPurchasingProduct::model()->tableName();
		$tPurshaceDetail = tbOrderPurchasingDetail::model()->tableName();
		$tbPostPro = tbOrderPost2Product::model()->tableName();
		$tbPostAssign = tbOrderPost2Assign::model()->tableName();

		$productInfo  = tbOrderPurchasingProduct::model()->findByPk( $this->purchaseProId );
		if( is_null($productInfo) ) {
			throw new CHttpException( 404, Yii::t('order','Not found record') );
		}

		$tOrder    = tbOrder::model()->tableName();
		$tOrderPro = tbOrderProduct::model()->tableName();

//		$sql       = "SELECT orderId,orderProductId as orderProId,num as quantity,singleNumber as productCode,color from {$tOrder} O right join {$tOrderPro} P USING(orderId) where orderType=:orderType and singleNumber=:serial AND O.state=:state";

		$sql = "SELECT purchase.orderId,purchase.orderProId,pro.productCode,pro.color,pro.quantity FROM {$tPurshaceDetail} AS purchase
LEFT JOIN {$tPurshace} AS purch ON purch.purchaseId=purchase.purchaseId
LEFT JOIN {$tPurshacePro} AS pro ON pro.purchaseId=purchase.purchaseId
WHERE NOT EXISTS(SELECT 1 FROM {$tbPostAssign} AS assing WHERE purchase.orderProId=assing.orderProId )
AND (purch.state=2 OR purch.state=3)
AND pro.productCode=:serial
ORDER BY purchase.detailId";

		$cmd = $this->getDbConnection()->createCommand( $sql );
		$cmd->bindValue(':serial',$productInfo->productCode,PDO::PARAM_STR);

		$result = $cmd->queryAll();
		return $result;
	}

	/**
	 * 获取已匹配客户订单产品列表
	 * @return array
	 * @throws CDbException
	 * @throws CHttpException
	 */
	public function getAssigned() {
		if($this->isNewRecord) {
			return false;
		}
		
		$tbOrderAssign  = tbOrderPost2Assign::model()->tableName();
		$tbOrderProduct = tbOrderPurchase2::model()->tableName();
		$sql            = "SELECT A.purchaseId, A.isAssign, A.productCode, A.quantity, A.orderId,B.userId,B.createTime FROM {$tbOrderProduct} AS A LEFT JOIN {$tbOrderAssign} AS B USING(purchaseId) WHERE B.postProId=:postProId";
		$cmd            = $this->getDbConnection()->createCommand( $sql );
		$cmd->bindValue(':postProId',$this->postProId,PDO::PARAM_STR);
		$result         = $cmd->queryAll();
		return $result;
	}

	/**
	 * 获取采购单明细信息
	 * @return tbOrderPurchasingProduct
	 * @throws CHttpException
	 */
	public function getPurchaseInfo() {
		if( !$this->isNewRecord && !empty($this->postProId) ) {
			$purchasing = tbOrderPurchasingProduct::model()->findByPk( $this->purchaseProId );
			if( !is_null($purchasing) ) {
				return $purchasing;
			}
		}
		throw new CHttpException( 500, Yii::t('order','Not found record') );
	}
}
