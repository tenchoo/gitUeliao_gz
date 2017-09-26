<?php
/**
 * 留货单审核延期申请
 * @author liang
 * @package CActiveRecord
 *
 * @property integer	$id
 * @property integer	$orderId			订单ID
 * @property integer	$state				状态 0:待审核 1:审核通过 2:审核不通过
 * @property integer	$userId 			审核者ID
 * @property timestamp	$createTime			申请延期时间
 * @property timestamp	$checkTime			审核时间
 * @property string		$reason				审核理由
 */

class tbOrderKeepDelay extends CActiveRecord {


	public function tableName() {
		return "{{order_keep_delay}}";
	}

	public static function model($className = __CLASS__) {
		return parent::model ( $className );
	}

	public function rules(){
       return array(
			array('orderId,state','required'),
			array('state','in','range'=>array(0,1,2)),
			array('reason', 'safe')
		);
    }

	public function attributeLabels(){
		return array(
			'orderId' => '订单ID',
			'state' => '审核结果',
			'reason' => '审核原因',
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



	/**
	* 新增延期申请
	* @param integer $orderId 申请延期的订单ID
	* @param string $msg 错误提示信息
	*/
	public static function delay( $orderId,&$msg ){
		if( empty( $orderId ) ) return false;

		$model = tbOrderKeep::model()->find(
					'orderId = :orderId and buyState = 0 and state!=2',
					array('orderId'=>$orderId));
		if( !$model ){
			$msg = Yii::t('msg','NO Data');
			return false;
		}

		$t = time();
		//到期最后一天才可以申请延期
		if( $t > $model->expireTime || $t < ( $model->expireTime - 86400 ) ){
			$msg = Yii::t('order','Due to the last genius can apply for an extension');
			return false;
		}

		//检查是否有未审核的延期申请
		$model = new tbOrderKeepDelay();
		$hasApply = $model->exists('orderId = :orderId and state = :state ',array(':orderId'=>$orderId,':state'=>0));
		if($hasApply){
			$msg = '您已申请延期，请等待审核';
			return false;
		}

		$model->orderId = $orderId;
		if( $model->save() ){
			$msg = 'success';
			return true;
		}else{
			$error = $model->getErrors();
			$msg = current($error);
			if(is_array($msg)) $msg = current($msg);
			return false;
		}




	}
}
