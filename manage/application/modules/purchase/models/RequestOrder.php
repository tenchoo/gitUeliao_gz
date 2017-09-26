<?php
/**
 * 请购单
 * @author yagas
 * @package CFormModel
 */
class RequestOrder extends CModel {

	private $order;
	private $_products = array();

	public $userName;
	public $cause;
	public $comment;
	public $typeId;
	public $orderId;
	public $serial;
	public $state;
	public $createTime;
	public $updateTime;
	public $userId;
	public $closeCause;

	public function __construct( $orderId=null ) {
		if( is_null($orderId) ) {
			$this->order = new tbRequestbuy();
			$this->order->typeId = tbRequestbuy::FORM_COMPANY;
		}
		else {
			$this->readOrder( $orderId );
			$this->setAttributes( $this->order->getAttributes(
				array('userName','cause','comment','typeId','closeCause'))
			);
		}
	}

	public function rules() {
		return array(
			array('userName,cause,typeId','required'),
			array('comment,orderId,serial,state,userId,createTime,updateTime,closeCause','safe'),
			array('comment','length','max'=>'80')
		);
	}

	public function attributeLabels() {
		return array(
			'userName' => '请购人',
			'cause' => '请购原因',
			'typeId'=>'类型',
			'comment'=>'备注',
		);
	}


	public function stateTitle( $state ) {
		switch( $state ) {
			case tbRequestbuy::STATE_CLOSE:
				return Yii::t('order', 'REQUEST_BUY_CLOSE');

			case tbRequestbuy::STATE_NORMAL:
				return Yii::t('order', 'REQUEST_BUY_NORMAL');

			case tbRequestbuy::STATE_WAITING:
				return Yii::t('order', 'REQUEST_BUY_WAITING');

			case tbRequestbuy::STATE_PROCCESSING:
				return Yii::t('order', 'REQUEST_BUY_PROCCESSING');

			case tbRequestbuy::STATE_FINISHED:
				return Yii::t('order', 'REQUEST_BUY_FINISHED');

			case tbRequestbuy::STATE_CHECKED:
				return Yii::t('order', 'REQUEST_BUY_CHECKED');
		}
		return;
	}

	/**
	 * 加载已存在的请购单信息
	 * @param integer $orderId
	 */
	private function readOrder( $orderId ) {
		$this->order = tbRequestbuy::model()->findByPk( $orderId );
		if( $this->order instanceof CActiveRecord ) {
			$this->_products = tbRequestbuyProduct::model()->findAll( "orderId=:id", array(':id'=>$orderId) );

			if( $this->order->state == tbRequestbuy::STATE_DELETE ) {
				$this->order = null;
				return false;
			}
		}

		if( is_null($this->order) ) {
			throw new CHttpException(404,'not found data');
		}

		$this->setAttributes( $this->order->getAttributes() );
	}

	public function setState( $state ) {
		if( $this->order->getScenario() == 'insert' ) {
			throw new CHttpException(404,'not found data');
		}

		$this->order->state = $state;

		$transaction = Yii::app()->getDb()->beginTransaction();
		if( $this->order->save() ) {
			foreach ( $this->_products as $item ) {
				$item->state = $state;
				if( !$item->save() ) {
					$this->addError('state', 'Not change state by id:'.$item->id);
				}
			}
		}

		if( $this->hasErrors() ) {
			$transaction->rollback();
		}
		else {
			$transaction->commit();
			return true;
		}
		return false;
	}

	/**
	 * 生成产品请购单
	 */
	public function save() {
		if( !$this->validate() ) {
			return false ;
		}
		$products = Yii::app()->request->getPost("product");
		if( !$products ) {
			$this->addError( 'product',Yii::t("order","Choose product,please") );
			return false;
		}

		$this->products( $products );


		if( count($this->_products) ==0 ) {
			$this->addError('product', Yii::t('order','Choose product,please'));
		}

		$this->order->setAttributes( $this->getAttributes(array('userId','userName','cause','comment')) );

		//开始事务
		$transaction = Yii::app()->getDb()->beginTransaction();
		if( !is_null($this->orderId) ) {
			tbRequestbuyProduct::model()->deleteAll( "orderId=:id", array(":id"=>$this->orderId) );
		}

		$code = $this->order->getScenario();
		if( $this->order->save() ) {
			//存储请购单产品信息
			foreach( $this->_products as $item ) {
				$item->orderId = $this->order->orderId;
				$item->from = $this->typeId;
				if( !$item->save() ) {
					$transaction->rollback();
					$this->addErrors( $item->getErrors() );					
					return false;
					//$this->addError( 'product',Yii::t('order','Unable to write data "{serialNumber}"',array( '{serialNumber}' => $item->singleNumber ) ));
					break;
				}
			}

			//添加操作日志
			tbRequestbuyOp::addOp( $this->order->orderId,$code );
		}
		else {
			$transaction->rollback();
			$this->addErrors( $this->order->getErrors() );
			return false;
		}

		//判断错误以执行事件提交或回滚
		if( !$this->hasErrors() ) {
			$transaction->commit();
			return true;
		}
		else {
			$transaction->rollback();
			return false;
		}
	}

	/**
	 * 关闭订单
	 * @return bool
	 */
	public function close() {
		$this->order->state = $this->state = tbRequestbuy::STATE_CLOSE;
		$transaction = Yii::app()->getDb()->beginTransaction();

		if( $this->order->save() ) {
			//添加操作日志
			tbRequestbuyOp::addOp( $this->orderId,'close',$this->closeCause );

			$alterRows = tbRequestbuyProduct::model()->updateAll( array('state'=>$this->state), "orderId=:id", array(':id'=>$this->orderId));
			if( $alterRows != tbRequestbuyProduct::model()->countByAttributes(array("orderId"=>$this->orderId)) ) {
				Yii::log("clolse request buy has product not change state", CLogger::LEVEL_WARNING, __CLASS__.'::'.__FUNCTION__);
			}
			$transaction->commit();
			return true;
		}
		else {
			$this->addErrors( $this->order->getErrors() );
		}
		$transaction->rollback();
		return false;
	}

	/**
	 * 添加请购单产品
	 * @param array $data 产品信息,example: array('singleNumber'=>'K45-234','total'=>30,'dealTime'=>35679)
	 */
	public function pushProduct( $data ) {
		if( array_key_exists('id', $data) ) {
			$new = tbRequestbuyProduct::model()->findByPk( $data['id'] );
			if( is_null($new) ) {
				return false;
			}
		}
		else {
			$new = new tbRequestbuyProduct();
		}

		if( isset($data['dealTime']) && !empty($data['dealTime']) ) {
			$data['dealTime'] = strtotime( str_replace('/', '-', $data['dealTime']));
		}

		$new->setAttributes( $data );
		array_push( $this->_products, $new );
		return true;
	}

	/**
	 * 清除包含的产品明细
	 * @param integer $ids
	 */
	public function pushClean() {
		$this->_products = array();
	}

	/**
	 * 设置/获取订单产品明细信息
	 * @param string $data
	 */
	public function products( $data=null ) {
		if( is_null($data) ) {
			return $this->_products;
		}

		if( is_array($data) ) {
			foreach( $data as $item ) {
				$this->pushProduct( $item );
			}
		}
	}

	/**
	 * 获取订单产品信息
	 */
	public function getProducts() {
		return $this->_products;
	}

	/**
	 * (non-PHPdoc)
	 * @see CModel::attributeNames()
	 */
	public function attributeNames() {
		$pro = new ReflectionClass(__CLASS__);
		$propertys = $pro->getProperties(ReflectionProperty::IS_PUBLIC);
		$groups = array();
		foreach( $propertys as $item ) {
			array_push($groups, $item->name);
		}
		return $groups;
	}
}