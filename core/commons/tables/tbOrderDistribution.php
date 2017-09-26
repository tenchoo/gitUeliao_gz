<?php
/**
 * 待分配分拣订单
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$orderId			订单ID
 * @property integer	$state				状态：０待分配，1已分配,10 订单已取消,12 订单已退货
 * @property integer	$userId				分配操作userId
 * @property date		$createTime			申请时间
 * @property date		$opTime				分配时间
 *
 */

 class tbOrderDistribution extends CActiveRecord {

	//待分配
    const STATE_NORMAL = 0;

    //已分配
    const STATE_DONE = 1;

	//订单已取消
    const STATE_CANCLE = 10;

	//订单已取消
    const STATE_REFUND = 12;


	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{order_distribution}}";
	}

	public function relations(){
		return array(
			'order'=>array(self::BELONGS_TO,'tbOrder','orderId'),
		);
	}

	public function rules() {
		return array(
			array('orderId','required'),
			array('state','in','range'=>array(0,1,10,12)),
			array('orderId,', "numerical","integerOnly"=>true),
			array('orderId','unique'),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'orderId' => '订单ID',
		);
	}


	/**
	 * 添加需分配订单
	 * @param integer $orderId
	 * @return bool
	 */
	public static function addOne( $orderId ) {
		$model = new self();
		$model->orderId = $orderId;
		if( !$model->save() ) {
			return false;
		}
		else {
			return true;
		}
	}


	/**
	 * 查询订单是否已分配
	 * @param integer $orderId
	 * @return bool
	 */
	public function hasDistribution( $orderId ) {
		return $this->exists( 'orderId = :orderId and state = :s ',array(':orderId'=>$orderId,':s'=>self::STATE_DONE) );
	}


	/**
	* 订单取消
	* @param integer $orderId  订单ID
	*/
	public function cancleOrder( $orderId ){
		if( empty($orderId) ) return false;

		$condition = 'orderId = :orderId and state = :s';
		$params = array( ':orderId'=>$orderId ,':s'=>self::STATE_NORMAL);

		return $this->updateAll( array( 'state'=>self::STATE_CANCLE ),$condition,$params );
	}


	/**
	 * 保存前的操作
	 */
	protected function beforeSave(){
		if($this->isNewRecord){
			$this->createTime = new CDbExpression('NOW()');
		}
		return true;
	}




	protected function afterSave(){
		if($this->isNewRecord){
			tbOrderMessage::addMessage( $this->orderId,'to_warehouse' );
		}
	}
}