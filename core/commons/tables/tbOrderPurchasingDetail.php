<?php
/**
 * 采购明细单产品关联信息
 * Created by PhpStorm.
 * User: yagas
 * Date: 2015/12/6
 * Time: 0:17
 * @package CActiveRecord
 */

class tbOrderPurchasingDetail extends CActiveRecord {
    public $detailId;	    //明细编号
    public $purchaseId;	    //采购单编号
    public $purchaseProId;	//采购单明细记录编号
    public $source;	        //订单来源
    public $orderId;	    //来源订单编号
    public $orderProId;	    //来源订单明细编号
    public $quantity;	    //采购数量
    public $comment;	    //来源订单备注

    public function init() {

    }

    public static function model( $className = __CLASS__ ) {
        return parent::model( $className );
    }

    public function tableName() {
        return '{{order_purchasing_detail}}';
    }

    public function primaryKey() {
        return 'detailId';
    }

    public function rules() {
        return array(
            array('purchaseId,purchaseProId,source,orderId,orderProId', 'required'),
            array('quantity,comment','safe')
        );
    }

    public function import( tbOrderPurchase2 $ar ) {
        $this->setAttributes( $ar->getAttributes(array('source','orderId','orderProId','quantity','comment')));
    }
    
    public function getProductCode() {
    	$product = tbOrderPurchasingProduct::model()->findByPk($this->purchaseProId);
    	if( is_null($product) ) {
    		throw new CHttpException( 500, Yii::t('order','Not found record') );
    	}
    	return $product->productCode;
    }
}