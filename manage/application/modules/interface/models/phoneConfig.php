<?php
/**
 * 手机接口配置
 * @author liang
 * @version 0.1
 * @package CFormModel
 *
 */

class phoneConfig extends CFormModel {

	public $Host;

	public $Port;

	public $md5;

	public $password;

	public $type;

	public $username;


	/**
	* Declares the validation rules.
	* The rules state that account and password are required,
	* and password needs to be authenticated.
	*/
	public function rules()	{
		return array(
			array('Host,Port,md5,password,type,username','required'),
			array('Port', "numerical","integerOnly"=>true,'min'=>'1'),
			array('md5', 'in','range'=>array('0','1')),
			array('type', 'in','range'=>array('1','2','3')),
			array('Host,password,username','safe'),

		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签
	*/
	public function attributeLabels() {
		return array(
			'Host' => '接口地址',
			'Port' => '端口',
			'username' => '账号',
			'password' => '密码',
			'type' => '手机接口商',
		);
	}

	/**
	* 保存
	* @param obj $model
	*/
	public function save( $model ){
		if(!$this->validate()){
			return false;
		}

		if( $this->md5 =='1' ){
			$this->md5 = true;
		}else{
			$this->md5 = false;
		}
		$model->value = serialize($this->attributes);
		if( !$model->save() ){
			$this->setErrors ( $model->getErrors() ) ;
			return false;
		}
		return true;
	}

}