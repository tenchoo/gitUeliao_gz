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
				if( !$model->closeOrder( '0',$applyClose->reason) ){
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
		/* $sql = "SELECT t.orderId, t.state as orderstate, t.realPayment, t.freight,a.state
				FROM `db_order` t  left join  `db_order_applyclose` a on t.orderId = a.orderId
				WHERE  t.`state` =7 or a.orderId IS NOT NULL
				order by t.createTime DESC,t.orderId DESC";
		$cmd = Yii::app()->db->createCommand( $sql );
		// $result = $cmd->queryAll();
		// return $result; */


		$criteria=new CDbCriteria;
		//join 表的字段，必须ad成原表有的字段名，否则会被过滤掉。
		$criteria->select = 't.orderId,t.state,t.realPayment,t.freight,a.state as payState';
		$criteria->join = 'left join {{order_applyclose}}  a on (t.orderId = a.orderId)';
		$criteria->addCondition("t.state ='7' or a.orderId IS NOT NULL");

		if( Yii::app()->user->getState('usertype') == tbMember::UTYPE_SALEMAN ){
			$userId[] =  Yii::app()->user->id;
			if( tbConfig::model()->get( 'default_saleman_id' ) == Yii::app()->user->id ){
				$userId[] = 0;
			}
			$userId = implode(',',$userId);
			$criteria->join .= ' left join {{member}}  m on (m.memberId = t.memberId and m.userId in ('.$userId.') )';
			$criteria->addCondition("m.memberId is not null ");
		}else {
			$condition['memberId'] = Yii::app()->user->id;
		}

		if(!empty($condition)){
			foreach ($condition as $key=>$val){
				if( is_string($val)) $val = trim($val);
				if( $val == '' ){
					continue ;
				}

				if( $key =='createTime1' ){
					$criteria->addCondition("t.createTime>'$val'");
				}else if( $key =='createTime2' ){
					$createTime2 = date("Y-m-d H:i:s",strtotime( $val )+86400 ) ; //包含选择的当天
					$criteria->addCondition("t.createTime<'$createTime2'");
				}else{
					$criteria->compare('t.'.$key,$val);
				}
			}
		}

		$criteria->with = array('products');
		$criteria->order = "t.createTime DESC,t.orderId DESC";
		$model = new CActiveDataProvider('tbOrder', array(
			'criteria'=>$criteria,
			'pagination'=>array('pageSize'=>$pageSize,'pageVar'=>'page'),
		));

		$data = $model->getData();
		$result['list'] = array();
		$result['pages'] = $model->getPagination();

		if(empty($data)) return $result;

		foreach ( $data as $key => $val ){
			$result['list'][$key] = $val->getAttributes(array('orderId','state','realPayment','freight'));
			$result['list'][$key]['cState'] = $val->payState;
			foreach( $val->products as $pval ){
				$result['list'][$key]['products'][] = $pval->attributes;
			}
		}
		return $result;
	}
}
?>
