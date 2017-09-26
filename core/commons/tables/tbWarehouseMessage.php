<?php
/**
 *  仓库/采购系统消息表
 *
 * @property integer	$messageId
 * @property integer	$type			消息类型：0为公共信息，1为仓库信息，2为采购信息
 * @property integer	$warehouseId	仓库ID，为0时为公共信息。
 * @property integer	$orderId		订单号
 * @property integer	$opstate		建议操作:0无,1关闭,2修改,3挂起
 * @property timestamp	$createTime		记录时间
 * @property string		$title			标题
 * @property string		$content		消息内容
 *
 */

class tbWarehouseMessage extends CActiveRecord
{
	//建议操作，无
	const OP_NORMAL = 0;

	//建议操作，关闭此操作订单
	const OP_CLOSE = 1;

	//建议操作，修改此操作订单
	const OP_MODIFY = 2;

	//建议操作，挂起此操作订单,即暂停操作。
	const OP_HOLDON = 3;


	/**
	 * 返回基于自身的AR实例
	 * @param string $className 类名
	 * @return CActiveRecord 实例
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string 返回表名
	 */
	public function tableName()
	{
		return '{{warehouse_message}}';
	}


	/**
	 * @return array 模型验证规则.
	 */
	public function rules()
	{
		return array(
			array('warehouseId,orderId,opstate,title', 'required'),
			array('warehouseId,orderId,opstate', "numerical","integerOnly"=>true),
			array('title,content', 'safe'),
		);
	}


	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'title' => '消息标题',
			'orderId'=>'订单ID',
		);
	}


	/**
	 * 初始化
	 */
	public function init(){
		$this->createTime = new CDbExpression('NOW()');
		$this->content = '';
		$this->warehouseId = 0;
	}


	/**
	* 订单挂起，暂停操作。只有客户申请取消，但业务员还未审核时，订单挂起。
	* @param integer $orderId  订单ID
	*/
	public static function holdon( $orderId ){
		$m = new self;
		$m->warehouseId = 0;
		$m->orderId = $orderId;
		$m->opstate = self::OP_HOLDON;
		$m->title   = '客户申请取消,订单挂起';
		return $m->save();
	}

	/**
	* 订单取消挂起。只有客户申请取消，但业务员审核不通过时，订单取消挂起，继续按原流程操作。
	* @param integer $orderId  订单ID
	*/
	public static function cancleHoldon( $orderId ){
		$m = new self;
		$m->warehouseId = 0;
		$m->orderId = $orderId;
		$m->opstate = self::OP_NORMAL;
		$m->title   = '客户申请取消审核不通过,订单取消挂起';
		return $m->save();
	}

	/**
	* 订单取消，发系统消息通知
	* @param integer $orderId  订单ID
	*/
	public static function cancle( $orderId ){
		$m = new self;
		$m->warehouseId = 0;
		$m->orderId = $orderId;
		$m->opstate = self::OP_CLOSE;
		$m->title   = '订单已经取消';
		return $m->save();
	}

	/**
	* 客户更改订单，根据分配情况来通知仓库作相应处理。
	* @param integer $warehouseId
	* @param integer $orderId  订单ID
	* @param integer $opstate
	* @param string  $content  消息内容
	*/
	public static function callWarehouse( $warehouseId,$orderId,$opstate,$content ){
		$m = new self;
		$m->warehouseId = $warehouseId;
		$m->orderId = $orderId;
		$m->opstate = $opstate;
		$m->title   = '订单数量被修改';
		$m->content = $content;
		return $m->save();
	}

	/**
	* 客户更改订单，通知采购。
	* @param integer $orderId  订单ID
	* @param string  $content  消息内容 purchasing
	*/
	public static function callPurchase( $orderId,$content ){
		$m = new self;
		$m->orderId = $orderId;
		$m->type = 2;
		$m->opstate = self::OP_MODIFY;
		$m->title   = '订单更改';
		$m->content = $content;
		return $m->save();
	}

	public function getWMessage( $orderId,$warehouseId ){
		if( empty($orderId) || empty($warehouseId) ) return ;

		$criteria = new CDbCriteria;
		$criteria->compare( 'warehouseId',array(0,$warehouseId));
		$criteria->compare( 'orderId',$orderId);
		$criteria->compare( 'type',array(0,1));
		$criteria->order = 'createTime desc';
		$model = $this->find( $criteria );
		if( $model && $model->opstate != self::OP_NORMAL ){
			return $model->attributes;
		}
		return ;
	}



}