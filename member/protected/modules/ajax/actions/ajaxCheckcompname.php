<?php
/**
 * ajax 注册--检查公司名称是否已被注册
 * @author liang
 * @version 0.1
 * @param string companyname 需检查的公司名称
 * @package CAction
 */
class ajaxCheckcompname extends CAction {
	public function run() {
		$companyname = Yii::app()->request->getQuery('companyname');
		$state = false;
		$message = $data = null;


		if( empty( $companyname ) ){
			$message = Yii::t ( 'msg', '{attribute} can not be empty',array( '{attribute}'=>'公司名称' ) );
			goto end;
		}

		$length = mb_strlen($companyname ,'utf-8');
		if(  $length < 4 || $length > 80 ){
			$message = Yii::t ( 'reg', 'Companyname length of 4-80' );
			goto end;
		}

		$flag = tbProfileDetail::model()->exists(' companyname =:companyname ',array(':companyname'=> $companyname) );
		if( $flag ){
			$message = Yii::t ( 'reg', 'The companyname already exists' );
			goto end;
		}

		$state = true;

		end:
		if( $message ){
			$data['companyname'][0] = $message;
		}
		$json = new AjaxData($state,$message,$data);
		echo $json->toJson();
		Yii::app()->end();
	}
}