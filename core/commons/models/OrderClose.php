<?php
/**
 * 订单取消管理
 * @author liang
 * @version 0.1
 * @package CFormModel
 */
class OrderClose extends CFormModel {

	public $state;

	public $remark;


	/**
	* Declares the validation rules.
	* The rules state that account and password are required,
	* and password needs to be authenticated.
	*/
	public function rules()	{
		return array(
			array('state', 'required'),
			array('state', "in","range"=>array(1,2)),
			array('remark','safe'),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签
	*/
	public function attributeLabels() {
		return array(
			'state' => '审核结果',
			'remark' => '审核反馈',
		);
	}


	/**
	* 订单取消的审核
	*/
	public function check( $applyClose,$model ){
		if( !$this->validate() ) {
			return false ;
		}

		if( $this->state != '1' && empty( $this->remark )){
			$this->addError( 'remark',Yii::t('order','Please fill in the audit results') );
			return false;

		}

		//使用事务处理
		$transaction = Yii::app()->db->beginTransaction();
		try {
			if( $this->state == '1' ){
				$track = 'check_applyCancle_ok';
				$m = '通过，订单取消。';
			}else{
				$track = 'check_applyCancle_fail';
				$m = '不通过，审核反馈信息：'.$this->remark;
			}

			//1.记录订单追踪信息
			tbOrderMessage::addMessage( $model->orderId,$track );


			//2.如果审核通过，关闭订单
			if( $this->state == '1' ){
				if( !$model->closeOrder( '0',$applyClose->reason,Yii::app()->user->id ) ){
					$this->addErrors( $model->errors );
					return false;
				}
			}else{
				//审核不通过，取消通知取消挂起。
				tbWarehouseMessage::cancleHoldon( $model->orderId );
			}

			//3.保存审核信息
			$applyClose->state = $this->state;
			$applyClose->remark = $this->remark;
			if(!$applyClose->save()){
				$this->addErrors( $applyClose->errors );
				return false;
			}

			//4.发送站内信通知客户
			$tbMessage = new tbMessage();
			$tbMessage->title = '订单取消申请审核通知';
			$tbMessage->content = '您的订单号为：'.$model->orderId.' 的订单取消申请已经审核，审核'.$m;
			$tbMessage->memberId = $model->memberId;
			if( !$tbMessage->save() ){
				$this->addErrors( $tbMessage->getErrors() );
				return false;
			}

			$transaction->commit();
			return true;
		} catch (Exception $e) {
			$transaction->rollback(); //如果操作失败, 数据回滚
			$this->addError( 'state','发生系统错误' );
			return false;
		}
	}

	/**
	 * 取消订单列表
	 * @param  array   $condition 查找条件
	 * @param  integer $pageSize  每页显示条数
	 */
	public static function search( $condition = array(),$pageSize = 10 ){

		$criteria = new CDbCriteria;
		if(!empty($condition)){
			foreach ($condition as $key=>$val){
				if( is_string($val)) $val = trim($val);
				if( $val == '' ){
					continue ;
				}

				switch($key){
					case 'createTime1':
						$criteria->addCondition("o.createTime>='$val'");
						$criteria->join = 'left join {{order}}  o on (t.orderId = o.orderId)';
						break;
					case 'createTime2':
						$createTime2 = date("Y-m-d H:i:s",strtotime( $val )+86400 ) ; //包含选择的当天
						$criteria->addCondition("o.createTime<'$createTime2'");
						$criteria->join = 'left join {{order}}  o on (t.orderId = o.orderId)';
						break;
					case 'userId':
					case 'memberId':
						$criteria->compare('o.'.$key,$val);
						$criteria->join = 'left join {{order}}  o on (t.orderId = o.orderId)';
						break;
					default:
						$criteria->compare('t.'.$key,$val);
						break;

				}
			}
		}

		if( Yii::app()->user->getState('usertype') == tbMember::UTYPE_SALEMAN ){
			$criteria->join = 'left join {{order}}  o on (t.orderId = o.orderId)';
			$userId[] =  Yii::app()->user->id;
			if( tbConfig::model()->get( 'default_saleman_id' ) == Yii::app()->user->id ){
				$userId[] = 0;
			}
			$userId = implode(',',$userId);
			$criteria->join .= ' left join {{member}}  m on (m.memberId = o.memberId and m.userId in ('.$userId.') )';
			$criteria->addCondition("m.memberId is not null ");
		}

		$criteria->order = "field(t.state,0) DESC ,t.createTime DESC";
		$model = new CActiveDataProvider('tbOrderApplyclose', array(
			'criteria'=>$criteria,
			'pagination'=>array('pageSize'=>$pageSize,'pageVar'=>'page'),
		));

		$result['list'] = $result['units'] = array();
		$result['pages'] = $model->getPagination();

		$data = $model->getData();
		if(empty($data)) return $result;

		//取得orderId,以取得订单详情
		$orderIds = array_map( function( $i ){ return $i->orderId;},$data );
		$criteria = new CDbCriteria;
		$criteria->compare('t.orderId',array_unique( $orderIds ) );
		$orderModels = tbOrder::model()->with('products','user')->findAll( $criteria );

		$order = new Order;
		$ordersInfo = $productIds =  array();
		foreach( $orderModels as $val ){
			$val->createTime = substr( $val->createTime,0,10);
			$info = $val->getAttributes(array('orderId','orderType','state','realPayment','freight','createTime'));
			$info['oType'] = $val->orderType ;
			$info['orderType'] =  $order->orderType( $val->orderType );
			$info['username'] = ( $val->user )?$val->user->username:'';
			$info['member'] = $order->getMemberDetial( $val->memberId );

			foreach( $val->products as $pval ){
				$productIds[$pval->productId] = $pval->productId;
				$info['products'][] = $pval->attributes;
			}
			$ordersInfo[$val->orderId] = $info;
		}

		foreach ( $data as &$val ){
			if( !isset($ordersInfo[$val->orderId]) ) continue;
			$list = $ordersInfo[$val->orderId];
			$list['cState'] = $val->state;
			$list['id'] = $val->id;
			$result['list'][] = $list;
		}

		//取得产品单位
		$result['units'] = tbProduct::model()->getUnitConversion( $productIds );
		return $result;
	}
}
?>
