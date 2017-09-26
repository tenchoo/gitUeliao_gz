<?php

/**
 * 订单短信通知
 */
class OrderSms {

	/**
	* 订单生效-短信需发送到下单客户的手机号码和收货人的手机号码，若是两手机号码不同，则是发送两条短信。
	* 客户下单是业务员审核通过，业务员下单是直接生效
	* @param integer $paymemtId
	*/
	public static function effective( $model ){
		return self::notify( $model,'addorder' );
	}
	/**
	* 已发送通知
	*/
	public static function deliveryNotify( $model ){
		return self::notify( $model,'delivery' );
	}

	/**
	* 发送短信
	*/
	public static function notify( $model,$type ){
		$member = tbMember::model()->find( array(
										'select'	=>	'phone',
										'condition'	=>	'memberId=:id',
										'params'	=>	array( ':id'=>$model->memberId )
										) );
		$phone = array( $model->tel,$member->phone );

		$classorder = new Order();
		$deliveryMethod = $classorder->deliveryMethod( $model->deliveryMethod );
		$params = array( '客户',$model->orderId,$deliveryMethod );
		if( $type == 'addorder' ){
			//备货时长
			$t = ( $model->orderType == tbOrder::TYPE_BOOKING )? 2:1;
			$conf = tbConfig::model()->find( "`key`=:name", array(':name'=>'order_readyTime_'.$t) );
			if( $conf  ){
				$params[] = $conf->value.$conf->unit;
			}else{
				$params[] = '';
			}
		}

		$state = self::send( $phone,$type,$params );

		$cacheName = 'deliveryCode_'.$model->orderId;
		Yii::app()->cache->set( $cacheName,null );
	}


	/**
	* 发送提货码
	*/
	public static function deliveryCode( $orderId ,&$msg ){
		$criteria = new CDbCriteria;
		$criteria->select = 't.phone';

		$criteria->addCondition(" exists (select null from {{order}} t2 where t.memberId = t2.memberId and  state = 3 and payState>=2 and orderId=:id  )");
		$criteria->params[':id'] = $orderId;
		$model = tbMember::model()->find( $criteria );

		if( !$model ){
			$msg = Yii::t('order','this order is not need to send sms');
			return false;
		}

		$code = rand(100000,999999);

		$params = array( $orderId,$code );

		$phone = array( $model->phone );

		$state = self::send( $phone,'takecode',$params );
		if( $state ){
			$msg = Yii::t('order','delevery code has send');
			$cacheName = 'deliveryCode_'.$orderId;
			Yii::app()->cache->set( $cacheName,$code,1800 );
		}
		return $state;
	}

	public static function getDeliveryCode( $orderId ){
		$cacheName = 'deliveryCode_'.$orderId;
		return Yii::app()->cache->get( $cacheName );
	}

	public static function send( $phone,$type,$params ){
		$body = tbConfig::model()->get( 'sms_'.$type );

		foreach ( $params as $key=>$val ){
			$k = ($key>0)?$key+1:'';
			$body = str_replace('{code'.$k.'}', $val, $body);
		}

		$server = new PhoneCode();
		$phone = array_unique( $phone );
		foreach ( $phone as $tel ){
			$server->send( $tel, $body );
		}

		return true;
	}


}