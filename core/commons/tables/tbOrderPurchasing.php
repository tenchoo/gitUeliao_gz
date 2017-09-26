<?php
/**
 * 采购订单
 * User: yagas
 * Date: 2015/12/6
 * Time: 0:17
 * @package CActiveRecord
 */

class tbOrderPurchasing extends CActiveRecord {
    public $purchaseId;	        //采购单编号
    public $state;	            //订单状态
    public $userId;             //创建订单人员
    public $supplierId;	        //供应商编号
    public $createTime;	        //订单创建时间
    public $supplierName;	    //供应商名称
    public $supplierSerial;	    //供应商编码
    public $supplierContact;	//联系人
    public $supplierPhone;	    //联系电话
    public $address;	        //收货地址
    public $comment;	        //订单备注

    //0:生产中 1:删除 2:已审核 3:已完成 4:已发货 10:已关闭
    const STATE_NORMAL   = 0;
    const STATE_DELETE   = 1;
    const STATE_CHECKED  = 2;
    const STATE_FINISHED = 3;
    const STATE_POST     = 4;
    const STATE_CLOSE    = 10;

    public function init() {
        $this->state      = self::STATE_NORMAL;
        $this->createTime = time();
        $this->supplierId = 0;
    }

    public function attributeLabels()
    {
        return array(
            'supplierName' => '工厂名称',
            'supplierSerial' => '工厂编号',
            'supplierContact' => '联系人',
            'supplierPhone' => '联系电话',
            'address' => '收货地址'
        );
    }

	public function stateTitles(){
		return array(
			'0'=>'生产中',
			'3'=>'已发货',
			'10'=>'已取消',
		);
	}

    public static function model( $className = __CLASS__ ) {
        return parent::model( $className );
    }

    public function tableName() {
        return '{{order_purchasing}}';
    }

    public function primaryKey() {
        return 'purchaseId';
    }

    public function rules() {
        return array(
            array('supplierName,supplierSerial,supplierContact,supplierPhone,address', 'required'),
            array('comment,supplierId','safe')
        );
    }

    public function getProducts() {
        $result = tbOrderPurchasingProduct::model()->findAllByAttributes(array('purchaseId'=>$this->purchaseId));
        return $result;
    }

    public function getDetails() {
        $result = tbOrderPurchasingDetail::model()->findAllByAttributes(array('purchaseId'=>$this->purchaseId));
        return $result;
    }

    /**
     * 工厂发货，变更采购订单状态
     * 级联变更原订单状态
     * 新建数据记录无法执行些方法
     * @throws CException
     */
    public function stateToDone() {
    	if($this->isNewRecord ||is_null($this->purchaseId)) {
    		throw new CException("new record not support this method");
    	}

    	$this->state = self::STATE_FINISHED;
    	if($this->save()) {
    		$products = $result = tbOrderPurchasingDetail::model()->findAllByAttributes(
    			array(
    				'purchaseId'=>$this->purchaseId,
    				'source'=>tbOrderPurchase2::FROM_REQUEST
    			)
    		);

    		foreach($products as $product) {
    			if($product->source == tbOrderPurchase2::FROM_REQUEST) {
    				$instance = tbRequestbuyProduct::model()->findByPk($product->orderProId);
    				if($instance instanceof CActiveRecord) {
    					$instance->stateToDone();
    				}
    			}
    		}
    		return true;
    	}
    	return false;
    }
}