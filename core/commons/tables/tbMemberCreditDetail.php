
<?php
/**
 * 会员额度使用明细表
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$memberId			客户ID
 * @property integer	$state				是否已入账：0未入账，1已入账，2已取消
 * @property integer	$isCheck			审核状态：0未审，1已审，2已取消
 * @property decimal	$amount				金额
 * @property timestamp	$createTime			记录时间
 * @property timestamp	$updateTime			更新时间
 * @property string		$mark				说明
 *
 */

 class tbMemberCreditDetail extends CActiveRecord {

	const NO_CHECK = 0;
	CONST HAS_CHECK = 1;
	CONST HAS_CANCLE = 2;

	public $mark = '';

	public $state = 0;

	 /**
	 * 当前可用信用额度
	 */
	 public $validCredit = 0;

	public function tableName() {
		return "{{member_credit_detail}}";
	}

	public static function model($className = __CLASS__) {
        return parent::model($className);
    }

	public function rules() {
		return array(
			array('memberId,amount','required'),
			array('memberId', "numerical","integerOnly"=>true),
			array('amount','numerical'),
			array('amount', 'compare', 'compareValue'=>'0','operator'=>'>','on'=>'repayment'),
			array('mark','safe'),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'memberId' => '客户ID ',
			'mark' => '说明',
			'amount' => '金额',

		);
	}

	/**
	 * 保存前的操作
	 */
	protected function beforeSave(){
		if($this->isNewRecord){
			$this->createTime = new CDbExpression('NOW()');
			if( $this->scenario == 'repayment' ){
				$this->amount = - $this->amount;
			}
		}else{
			$this->updateTime = new CDbExpression('NOW()');
		}

		return parent::beforeSave();
	}

	/**
	* 月结客户当前未入账记录金额
	* @param integer $memberId
	* @param string $userType 操作者用户类型,如果是额度获取，需包含非审的使用信息，若是非额度获取，只取已审核的记录
	*/
	public static function usedCredit( $memberId,$userType ){
		if( empty ( $memberId ) ) return 0;

		if( $userType == tbMember::UTYPE_MEMBER ){
			$isCheck = 'isCheck != '.self::HAS_CANCLE;
		}else{
			$isCheck = 'isCheck = '.self::HAS_CHECK;
		}

		$sql = "select sum(amount) from {{member_credit_detail}} where memberId=:memberId and state = 0 and $isCheck";
		$cmd = Yii::app()->db->createCommand( $sql );
		$cmd->bindValue(':memberId',$memberId,PDO::PARAM_STR);
		$result = $cmd->queryScalar();
		return floatval($result);
	}

	/**
	* 月结客户客户还款,设置还款参数
	* @param number amount
	*/
	public function repayment( $amount ){
		$this->scenario ='repayment';
		$this->mark     =  '客户还款';
		$this->isCheck  =  self::HAS_CHECK;
		$this->orderId  =  0;
		$this->amount = $amount ;
	}


	/**
	* 月结客户订单取消时同时变更使用额度信息，以更新可用额度
	* @param CActiveRecord tborder
	*/
	public function cancleCredit( $order ){
		if( $order->state != '7' ){
			return false;
		}

		//查找订单的信用额度使用记录
		$details = $this->findAll('orderId=:orderId',array(':orderId'=>$order->orderId));
		if( !$details ){
			return true; //无记录，不需操作。
		}

		$creditAmount = 0;
		foreach ( $details as $val ){
				if( $val->state >0 ){
					//已入账的金额需相应减去
					$creditAmount = bcsub( $creditAmount,$val->amount);
				}else{
					$val->isCheck = self::HAS_CANCLE;
					$val->state = self::HAS_CANCLE;
					$val->save();
				}

		}

		if ( $creditAmount != 0 ){
			$creditDetail = new tbMemberCreditDetail();
			$creditDetail->mark = '订单取消';
			$creditDetail->memberId =  $order->memberId;
			$creditDetail->orderId =  $order->orderId;
			$creditDetail->isCheck = self::HAS_CHECK;
			$creditDetail->amount = $creditAmount ;
			if( !$creditDetail->save() ){
				$this->addErrors( $creditDetail->getErrors() );
				return false;
			}
		}

		return true;
	}

	/**
	* 月结客户订单变更时同时变更使用额度信息，以更新可用额度
	* @param CActiveRecord tborder
	*/
	public function changeCredit( $order ){
		if( $order->state == '7' ){
			return $this->cancleCredit( $order );
		}

		$creditAmount =  $order->realPayment;
		if( $order->orderType == tbOrder::TYPE_BOOKING ){
			//查找订金信息
			$deposit = $order->deposit;
			if( $deposit && $deposit->amount>0  ){
				$creditAmount = bcsub( $creditAmount,$deposit->amount);
			}
		}

		//查找当前已添加的信息信息
		$details = $this->findAll('orderId=:orderId',array(':orderId'=>$order->orderId));
		$creditDetail = new tbMemberCreditDetail();
		if( $details ){
			foreach ( $details as $val ){
				$creditAmount = bcsub( $creditAmount,$val->amount);
				if( $order->state>0 ){
					$val->isCheck = self::HAS_CHECK;
					$val->save();
				}
			}
			$creditDetail->mark = '订单修改';
		}

		if ( $creditAmount != 0 ){
			$creditDetail->memberId =  $order->memberId;
			$creditDetail->orderId =  $order->orderId;
			$creditDetail->isCheck = ($order->state>0)?'1':'0';
			$creditDetail->amount = $creditAmount ;
			if( !$creditDetail->save() ){
				$this->addErrors( $creditDetail->getErrors() );
				return false;
			}

		}

		return true;
	}

}