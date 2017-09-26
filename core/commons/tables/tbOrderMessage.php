<?php
/**
 * 订单追踪信息表
 *
 * @property integer	$messageId
 * @property integer	$inside			否内部信息，若是，只在后台显示
 * @property integer	$orderId		订单号
 * @property timestamp	$createTime		记录时间
 * @property string		$subject		消息
 * @property string		$message 		消息补充说明--只在内部显示
 *
 */

class tbOrderMessage extends CActiveRecord
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
		return '{{order_message}}';
	}


	/**
	 * @return array 模型验证规则.
	 */
	public function rules()
	{
		return array(
			array('subject,orderId', 'required'),
			array('orderId', "numerical","integerOnly"=>true),
			array('subject', 'safe'),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'subject' => '消息内容',
			'orderId'=>'订单ID',
		);
	}

	/**
	* 添加订单追踪信息
	* @param integer $orderId  订单ID
	* @param string $type     消息类型
	*/
	public static function addMessage( $orderId,$type,$source ='' ){
		if( empty( $orderId ) || empty( $type )  ) return false;

		$m = new tbOrderMessage();
		$m->orderId = $orderId;
		$m->subject = $m->subjects( $type,$source );
		$m->inside = 0;
		$m->message = '';
		return $m->save();
	}


	/**
	* 添加订单追踪信息
	* @param integer $orderId  订单ID
	* @param string $subject   消息内容
	* @param string $message   消息补充说明--只在内部显示
	* @param boolean $inside   是否内部私有信息
	*/
	public static function addMessage2( $orderId,$subject,$message,$inside = false  ){
		if( empty( $orderId ) || empty( $subject )  ) return false;

		$m = new tbOrderMessage();
		$m->orderId = $orderId;
		$m->subject = $subject;
		$m->message = $message;
		$m->inside = ($inside)?1:0;
		return $m->save();
	}


	public function subjects( $type,$source = '' ){
		if( in_array( $type,array('member_add','saleman_add') ) ){
			switch( $source ){
				case 'web':
					$source = '网站';break;
				case 'wx':
					$source = '微网站';break;
			}
		}

		$arr = array(
				'member_add'=>'客户通过'.$source.'平台下单',
				'saleman_add'=>'业务员通过'.$source.'平台下单',
				'keep_to_buy'=>'留货订单确定购买',
				'check_success'=>'订单信息审核通过',
				'to_purchase'=>'订单开始采购，采购中',
				'has_purchase'=>'采购完成',
				'to_warehouse'=>'订单已通知配货',
				'has_distribution'=>'订单配货完成,备货中',
				'has_packing'=>'备货完成',
				'has_settlement'=>'订单已出结算单',
				'has_delivery'=>'仓库已发货',
				'has_confirm'=>'已确认收货',
				'cancle'=>'取消订单',
				'applyCancle'=>'客户申请取消订单',
				'check_applyCancle_ok'=>'业务员审核客户的取消申请，审核通过',
				'check_applyCancle_fail'=>'业务员审核客户的取消申请，审核不通过',
				'check_applyChange_ok'=>'业务员审核客户的修改申请，审核通过',
				'check_applyChange_fail'=>'业务员审核客户的修改申请，审核不通过',
				'member_applyChange'=>'客户申请修改订单',
				'saleman_applyChange'=>'业务员申请修改订单',
			);



		return array_key_exists( $type,$arr )?$arr[$type]:$type;
	}

	/**
	淘宝：
	您的订单开始处理
	您的订单信息审核通过
	您的订单已通知配货
	您的订单待配货
	您的订单已打物流单
	您的订单已打发货单
	您的订单已出库
	商家正通知快递公司揽件
	*/

	/**
	 * 保存前的操作
	 */
	protected function beforeSave(){
		if($this->isNewRecord){
			$this->createTime = new CDbExpression('NOW()');
		}
		return true;
	}

	/**
	* 根据订单ID取得订单追踪信息
	* @param integer $orderId  订单ID
	* @param boolean $inside   是否内部私有信息，前端不显示内部私有信息
	*/
	public static function getList( $orderId,$inside = false ){
		if( !is_numeric( $orderId ) ) return array();

		$criteria = new CDbCriteria;
		$criteria->compare( 'orderId', $orderId );
		if( $inside === true ){
			$criteria->select = 'createTime,subject,message';
		}else{
			$criteria->select = 'createTime,subject';
			$criteria->compare( 'inside', '0' );
		}

		$criteria->order = 'createTime desc';

		$trace = tbOrderMessage::model()->findAll( $criteria );

		$trace = array_map( function ( $i ){
					if( !empty( $i->message ) ) {
						$i->subject = $i->subject.';'.$i->message;
					}
					return $i->getAttributes( array('createTime','subject') );
				},$trace );

		return $trace;
	}
}