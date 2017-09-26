<?php
/**
 * 支付方式配置信息表
 * @author liang
 * @version 0.1
 *
 * @property int    $paymentId
 * @property int    $type			支付类型，0为支付类型，非0为所属支付类型ID,自连接
 * @property int    $termType		使用终端类型：0无，1：PC端，2微信端，3：PC端和微信端
 * @property int    $available		是否启用
 * @property string $paymentTitle 支付方式标题
 * @property string $logo		支付方式LOGO
 * @property string $paymentSet	配置信息
 */
class tbPayMent extends CActiveRecord {
	//接口类名称
	public $class_name;
	//账号
	public $payment_id;
	//用户名
	public $payment_user;
	//密匙
	public $payment_key;

	public static function model($className=__CLASS__) {
		return parent::model( $className );
	}

	public function tableName() {
		return '{{payment}}';
	}

	public function rules() {
		return array(
			array('paymentTitle', 'required'),
			array('available','in','range'=>array(0,1)),
			array('termType','in','range'=>array(0,1,2,3)),
			array('type', "numerical","integerOnly"=>true),
			array('paymentTitle,paymentSet,logo', 'safe')
		);
	}

	public function attributeLabels(){
		return array(
			'paymentTitle' => '支付名称',
			'type' => '所属支付类型',
			'logo' => '支付方式LOGO',
			'paymentSet' => '配置信息',
			'termType'=>'终端类型'
		);
	}

	/**
	* 取得支付配置信息
	* @param integer $type 类型，若为空则是取得全部。否则按type值取
	*/
	public function getPayMents( $type = '' ){
		$c = new CDbCriteria;
		if(  is_numeric( $type ) ){
			$c->compare('type',$type);
		}

		$model = $this->findAll( $c );
		$result = array();
		 foreach ( $model as $val ){
			$result[$val->paymentId] = $val->attributes;
		}
		return $result;
	}

	/**
	* 取得PC端支付方式
	*/
	public function getPcPayment(){
		$c = new CDbCriteria;
		$c->compare('available','1');
		$c->compare('termType',array(1,3));
		$data = tbPayMent::model()->findAll( $c );
		$result  = array();
		foreach ( $data as $val ){
			if( $val->type == '0'){
				$result[$val->paymentId] = $val->attributes;
			}else{
				if( isset($result[$val->type]) ){
					$result[$val->type]['methods'][] = $val->attributes;
				}
			}
		}
		return $result;
	}

	/**
	* 取得微信端支付方式
	*/
	public function getWXPayment(){
		$c = new CDbCriteria;
		$c->compare('available','1');
		$c->compare('termType',array(2,3));
		$data = tbPayMent::model()->findAll( $c );

		$result  = array('6'=>array('paymentId'=>'6','paymentTitle' => '微信支付'));
		foreach ( $data as $val ){
			if( $val->type == '0'){
				$result[$val->paymentId] = $val->getAttributes(array('paymentId','paymentTitle'));

			}else if( $val->type == '3' ){
				if( isset($result[$val->type]) ){
					$method =  $val->getAttributes( array('paymentId','paymentTitle'));
					$method['payment_user'] =  $val->paymentSet['payment_user'];
					$method['payment_id'] =  $val->paymentSet['payment_id'];
					$result[$val->type]['methods'][] = $method;
				}
			}
		}
		return $result;
	}

	/**
	* 取得微信端支付方式
	*/
	public function getAppPayment(){
		$data = tbPayMent::model()->findAll( 'available = 1' );

		$onlinemark = array('8'=>'alipay','9'=>'weixin');
		$result = array();
		foreach ( $data as $val ){
			if( $val->type == '0'){
				if( !in_array($val->paymentId,array('4','5'))) continue;
				$result[$val->paymentId] = $val->getAttributes(array('paymentId','paymentTitle'));
				$result[$val->paymentId]['isonline'] = 0;

			}else{
				if( array_key_exists ($val->paymentId ,$onlinemark) ){
					$result['5']['isonline'] = 1;
					$result['5']['methods'][] = array('paymentId'=>$val->paymentId,'paymentTitle'=>$val->paymentTitle,'logo'=>$val->logo,'mark'=>$onlinemark[$val->paymentId]);
				}
			}
		}
		krsort($result);
		return $result;
	}
	/**
	 * 读取数据后的操作，将配置信息取出并反系列化
	 */
	protected function afterFind(){
		if($this->paymentSet){
			$payment = unserialize($this->paymentSet);
			$this->class_name = $payment['class_name'];
			$this->payment_user = $payment['payment_user'];
			$this->payment_id = $payment['payment_id'];
			$this->payment_key = $payment['payment_key'];
			$this->paymentSet = $payment;
		}
		return true;
	}
}