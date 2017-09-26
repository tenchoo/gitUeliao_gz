<?php
/**
 * 采购订单明细
 * Created by PhpStorm.
 * User: yagas
 * Date: 2015/12/6
 * Time: 0:17
 * @package CActiveRecord
 */

class tbOrderPurchasingProduct extends CActiveRecord {
    public $purchaseProId;	//明细编号
    public $purchaseId;	    //采购单编号
    public $quantity;	    //采购数量
    public $deliveryDate;	//交货时间
    public $supplierCode;	//革厂产品编号
    public $productCode;	//产品单品编码
    public $color;	        //产品颜色
    public $comment;	    //备注

    public function init() {

    }

    public static function model( $className = __CLASS__ ) {
        return parent::model( $className );
    }

    public function tableName() {
        return '{{order_purchasing_product}}';
    }

    public function primaryKey() {
        return 'purchaseProId';
    }

    public function rules() {
        return array(
            array('purchaseId,quantity,deliveryDate,supplierCode', 'required'),
            array('productCode,color,comment','safe')
        );
    }

    public function setPurchaseIds( $value ) {
        $ids = explode( ':', $value );
        $id = array_shift($ids);
        $info = tbOrderPurchase2::model()->findByPk($id);
        if( $info ) {
            $this->setAttributes( $info->getAttributes(array('productCode','color')) );
        }
    }

    public function getProducts() {
        $result = tbOrderPurchasingDetail::model()->findAllByAttributes(array('purchaseProId'=>$this->purchaseProId));
        return $result;
    }

    /**
     * 获取采购订单详细信息
     * @return bool|tbOrderPurchasingDetail
     */
    public function getPurchaseDetail() {
        if($this->isNewRecord||empty($this->purchaseProId)) {
            $this->addError('checkOrderAssign', Yii::t('order','Not execute method in new record'));
            return false;
        }

        $detail = tbOrderPurchasingDetail::model()->findByAttributes(array('purchaseProId'=>$this->purchaseProId));
        if(!is_null($detail)) {
            return $detail;
        }
        return false;
    }

    public function relations() {
        return array(
            'detail'=>array(self::BELONGS_TO,'tbOrderPurchasingDetail','purchaseProId')
        );
    }
    
    /**
     * 临时的获取产品ID接口
     * @return number
     */
    public function getProductId() {
    	$stock = tbProductStock::model()->findByAttributes(array('serialNumber'=>$this->productCode));
    	if( $stock instanceof tbProductStock ) {
    		return intval($stock->productId);
    	}
    	return 0;
    }

    /**
     * 判断客户订单下所有产品是否匹配完成
     * @param integer $orderId 源采购订单编号
     * @return bool
     */
    public function checkOrderAssign($orderId) {
        if($this->isNewRecord||empty($this->purchaseProId)) {
            $this->addError('checkOrderAssign', Yii::t('order','Not execute method in new record'));
        }
        $detail        = $this->getPurchaseDetail();

        $orderProduct  = tbOrderProduct::model()->countByAttributes(array('orderId'=>$orderId));
        $assignProduct = tbStorageLock::model()->countByAttributes(array('orderId'=>$orderId));

        if($orderProduct === $assignProduct) {
            return true;
        }
        return false;
    }
}