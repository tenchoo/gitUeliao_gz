<?php
/**
 * 待采购订单队列
 * Created by PhpStorm.
 * User: yagas
 * Date: 2015/12/6
 * Time: 0:17
 * @package CActiveRecord
 */

class tbOrderPurchase2 extends CActiveRecord {
    public $purchaseId;
    public $state;
    public $userId;
    public $source;
    public $productId;
    public $orderId;
    public $orderProId;
    public $createTime;
    public $deliveryTime;
    public $productCode;
    public $color;
    public $quantity;
    public $comment;
    public $isAssign;

    //正常状态
    const STATE_NORMAL = 0;
    //选中状态
    const STATE_CHOOSE = 1;
    //已生成采购单
    const STATE_DONE = 2;

	//取消采购
    const STATE_CANCLE = 10;

    //订单已匹配
    const STATE_ASSIGN = 1;

    //订单未匹配
    const STATE_UNASSIGN = 0;



    //来源：内部请购
    const FROM_REQUEST = 0;
    //来源：低安全库存
    const FROM_LOWER   = 1;
    //来源：请购订单
    const FROM_ORDER   = 2;

    public function init() {
        $this->state = self::STATE_NORMAL;
        $this->createTime = date('Y-m-d H:i:s');
        $this->source = self::FROM_ORDER;
        $this->isAssign = 0;
    }

    public static function model( $className = __CLASS__ ) {
        return parent::model( $className );
    }

    public function tableName() {
        return '{{order_purchase2}}';
    }

    public function primaryKey() {
        return 'purchaseId';
    }

    public function rules() {
        return array(
            array('orderId,productId,productCode,quantity,orderProId', 'required'),
            array('orderId,productId,quantity,orderProId','numerical'),
			array('orderProId','checkExists','on'=>'insert')

        );
    }


	/**
	* 检查是否存在，防止订单重复提交采购,rules 规格
	*/
	public function checkExists( $attribute,$params ){
		if( empty( $this->$attribute ) ){
			return;
		}

		$criteria = new CDbCriteria;
		$criteria->compare($attribute,$this->$attribute);
		$model = self::model()->find( $criteria );
		if( $model ){
			$label = $this->getAttributeLabel($attribute);
			$this->addError($attribute,Yii::t('base','{attribute} already exists',array('{attribute}'=>$label)));
		}
	}


	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels() {
		return array(
			'orderProId' => '订单产品',
		);
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
        $this->state = self::STATE_DONE;
        if($this->save()) {
            return true;
        }
        return false;
    }

    /**
     * 客户采购订单列表
     * @param CDbCriteria $criteria
     * @return array|static[]
     */
    public function findAllByOrder( CDbCriteria $criteria ) {
        $result = $this->findAll( $criteria );
        if( $result ) {
            $ids = array_map( function($r){
                return $r->orderId;
            }, $result );

            return tbOrder::model()->findAllByPk($ids);

        }
        return array();
    }

    /**
     * 内部采购订单列表
     * @param CDbCriteria $criteria
     * @return array|static[]
     */
    public function findAllByRequest( CDbCriteria $criteria ) {
        $result = $this->findAll( $criteria );
        if( $result ) {
            $ids = array_map( function($r){
                return $r->orderId;
            }, $result );

            return tbRequestbuy::model()->findAllByPk($ids);

        }
        return array();
    }

    /**
     * 获取未匹配的产品列表
     * @param string $productCode 产品单品ID,只匹配订单，低安全库存采购和内部请购不需要匹配
     * @return array<tbOrderPurchase2>
     */
    public function findUnAssignProduct($productCode) {
    	return $this->findAllByAttributes(array(
    		'productCode' => $productCode,
    		'isAssign' => 0,
			'source'=>self::FROM_ORDER,
    	));
    }


	/**
	* 订单取消或单品取消
	* @param integer $orderId  订单ID
	* @param integer $orderProId 订单明细ID
	*/
	public function cancleOrder( $orderId,$orderProId ='' ){
		if( empty($orderId) ) return false;

		$condition = 'orderId = :orderId and state = :s';
		$params = array( ':orderId'=>$orderId ,':s'=>self::STATE_NORMAL);

		if( $orderProId ){
			$condition .= ' and orderProId = :orderProId';
			$params[':orderProId'] = $orderProId;
		}

		return $this->updateAll( array( 'state'=>self::STATE_CANCLE ),$condition,$params );
	}


	/**
	* 订单购买数量更改
	* @param integer $orderId		订单ID
	* @param integer $orderProId	订单明细ID
	* @param integer $quantity		采购数量
	*/
	public function changeQuantity( $orderId,$orderProId,$quantity ){
		if( empty($orderId) ||  empty($orderProId) ||  empty($quantity) ) return false;

		$condition = 'orderId = :orderId and state = :s and orderProId = :orderProId';
		$params = array( ':orderId'=>$orderId ,':s'=>self::STATE_NORMAL,':orderProId'=>$orderProId );
		return $this->updateAll( array( 'quantity'=>$quantity ),$condition,$params );
	}

	/**
	 * 统计待匹配订单数量
	 * @return integer
	 */
	public function unAssignCount() {
		$sql = "select count(distinct orderId) from {$this->tableName()} where isAssign=0 and state != ".self::STATE_CANCLE ;
		$cmd = $this->getDbConnection()->createCommand($sql);
		$result = $cmd->queryScalar();
		return intval($result);
	}

	public function unAssignList($page=0, $pageSize=20) {
		$offset = $page * $pageSize;
		$sql = "SELECT DISTINCT P.orderId, O.createTime FROM {$this->tableName()} P LEFT JOIN {{order}} O USING(orderId) WHERE isAssign=0 limit {$offset},{$pageSize}";
		$cmd = $this->getDbConnection()->createCommand($sql);
		$result = $cmd->queryAll();
		$result = array_map(function($item){
			$item['products'] = SELF::model()->findAllByAttributes( array('isAssign'=>0,'orderId'=>$item['orderId']) );
			return $item;
		}, $result);
		return $result;
	}


    /**
     * 导入订单数据
     * @param CActiveRecord $ar 订单对象
     * @return mixed
     */
    public static function importOrder( CActiveRecord $ar ) {
        $className = get_class( $ar );
        $funcName  = '_imp'.ucfirst( $className );
        $result    = call_user_func( array(__CLASS__,$funcName),$ar );
        return $result;
    }

    /**
     * 导入客户订购单产品信息
     * @param tbOrder $ac 客户订单对象
     * @return bool
     */
    private static function _impTbOrder( tbOrder $ac ) {
        $rows = $ac->getProducts();
        foreach( $rows as $item ) {
            $pro               = new tbOrderPurchase2;
            $pro->source       = self::FROM_ORDER;
            $pro->productId    = $item->productId;
            $pro->orderId      = $ac->orderId;
            $pro->orderProId   = $item->orderProductId;
            $pro->productCode  = $item->singleNumber;
            $pro->deliveryTime = date('Y-m-d H:i:s',$item->dealTime);
            $pro->color        = $item->color;
            $pro->quantity     = $item->num;
            $pro->comment      = $item->remark;

            if( !$pro->save() ) {
                Yii::log(
                    Yii::t('order','Faild to import product by:{serial}',
                        array('{serial}'=>$item->singleNumber)),
                    CLogger::LEVEL_WARNING,
                    __CLASS__.'::_impTbOrder'
                );
                return false;
            }

        }
		//订单进入采购，待采购。加追踪信息。
		tbOrderMessage::addMessage( $ac->orderId,'to_purchase' );
        return true;
    }

    /**
     * 导入内部请购单产品信息
     * @param tbRequestbuy $ac 内部请购单对象
     * @return bool
     */
    private static function _impTbRequestbuy( tbRequestbuy $ac ) {
        $rows = $ac->getProducts();
        foreach( $rows as $item ) {
            $pro               = new tbOrderPurchase2;
            $pro->source       = self::FROM_REQUEST;
            $pro->productId    = $item->productId;
            $pro->orderId      = $ac->orderId;
            $pro->orderProId   = $item->requestProductId;
            $pro->productCode  = $item->singleNumber;
            $pro->deliveryTime = date('Y-m-d H:i:s',$item->dealTime);
            $pro->color        = $item->color;
            $pro->quantity     = $item->total;
            $pro->comment      = $item->comment;
            if( !$pro->save() ) {
                Yii::log(
                    Yii::t('order','Faild to import product by:{serial}',
                        array('{serial}'=>$item->singleNumber)),
                    CLogger::LEVEL_WARNING,
                    __CLASS__.'::_impTbRequestbuy'
                );
                return false;
            }

        }
        return true;
    }

    /**
     * 导入低安全采购单产品信息
     * @param tbRequestbuy $ac 内部请购单对象
     * @return bool
     */
    private static function _impTbRequestlower( tbRequestlower $ac ) {
        $product = tbProductStock::model()->findByAttributes(array('singleNumber'=>$ac->singleNumber));
        if( !$product ) {
            return false;
        }

        $pro               = new tbOrderPurchase2;
        $pro->source       = self::FROM_LOWER;
        $pro->productId    = $product->productId;
        $pro->orderId      = 0;
        $pro->orderProId   = 0;
        $pro->productCode  = $ac->singleNumber;
        $pro->deliveryTime = date('Y-m-d', strtotime('+7 days',time()));
        $pro->color        = $ac->color;
        $pro->quantity     = $ac->buyTotal;
        $pro->comment      = '';
        if( !$pro->save() ) {
            Yii::log(
                Yii::t('order','Faild to import product by:{serial}',
                    array('{serial}'=>$item->singleNumber)),
                CLogger::LEVEL_WARNING,
                __CLASS__.'::_impTbOrder'
            );
            return false;
        }
        return true;
    }
}