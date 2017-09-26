<?php
/**
 * 物流消息表
 *
 * @property integer	$logisticsMessageId
 * @property integer	$deliveryId		发货单ID
 * @property integer	$orderId		订单号
 * @property integer	$state			当前物流状态:1已发货,2已签收
 * @property integer	$isDel			是否删除:0正常,1删除
 * @property timestamp	$createTime		
 *
 * 目前只有两处需要提醒：写于2015-10-10
 *	1.产品已发货：消息提示为“您的产品已经发货，请注意查收”点击查看，则可跳转到查看物流页面
 *	2.产品已确认收货：消息提示为“您已确认收货”点击查看，则可跳转到查看物流页面
 */

class tbLogisticsMessage extends CActiveRecord
{
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
		return '{{logistics_message}}';
	}
	
	public function relations(){
		return array(
			'product'=>array(self::HAS_ONE, 'tbOrderProduct', '','on'=>'t.orderId = product.orderId', 'select'=>'mainPic'),
		);
	}

	/**
	 * @return array 模型验证规则.
	 */
	public function rules()
	{
		return array(
			array('state,orderId', 'required'),
			array('state','in','range'=>array(1,2)),
			array('isDel','in','range'=>array(0,1)),
			array('orderId,deliveryId', "numerical","integerOnly"=>true),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'state' => '物流状态',
			'isDel'=>'消息状态',
			'orderId'=>'订单ID',
			'deliveryId'=>'发货单ID',
		);
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
}