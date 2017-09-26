<?php
/**
 * 邮箱手机验证码的发送和校验
 */
class OpCaptcha {

	/**
	* 发送验证码
	* @param $receiver 接收对象，可为email 或手机号码
	* @param $username 接收方称呼
	* @param $type 发送类型，决定发送的内容
	*/
	public static function sendCaptcha( $type,$receiver,$username='' ) {
			$captcha = rand(100000,999999);
			$arr=self::sendContent($type,$captcha);
			$send = new SendCaptcha($arr['body'],$arr['title']);
			$result = $send->send($receiver);

			//保存验证码到会话
			Yii::app()->user->setState('receiver',$receiver);
			Yii::app()->user->setState('SendCaptcha',$captcha);
			Yii::app()->user->setState('CaptchaDeadline',120+time());  //有效时间为2分钟

			return $result;
	}


	private static function sendContent($type,$captcha){
		$c = new CDbCriteria;
		$c->compare('t.`key`',array('sms_'.$type,'sms_default'));
		$model = tbConfig::model()->findAll( $c );
		foreach ( $model as $item ){
			$bodys[$item->key] = $item->value;
		}

		$body = !empty($bodys['sms_'.$type])?$bodys['sms_'.$type]:$bodys['sms_default'];
		$arr=array(
			'title'=>'操作验证码',
			'body'=>str_replace('{code}', $captcha, $body),
			);
		return $arr;
	}

	/**
	* 验证验证码
	* @param $receiver 验证码接收的对象
	* @param $captcha 发送的验证码
	* @param $isreset 是否清空会话保存的验证码
	*/
	public static function checkCaptcha($receiver,$captcha,$isreset='1'){
		if( empty($captcha) ){
			return false;
		}
		$deadline = Yii::app()->user->getState('CaptchaDeadline');
		if( $deadline > time() ){
			$setreceiver = Yii::app()->user->getState('receiver');
			$SendCaptcha = Yii::app()->user->getState('SendCaptcha');
			if( $captcha != $SendCaptcha || $receiver!=$setreceiver ){
				return false;
			}else {
				if( $isreset=='1' ){
					Yii::app()->user->setState('receiver',null);
					Yii::app()->user->setState('SendCaptcha',null);
					Yii::app()->user->setState('CaptchaDeadline',null);
				}
				return true;
			}
		}
		return false;
	}
}
?>