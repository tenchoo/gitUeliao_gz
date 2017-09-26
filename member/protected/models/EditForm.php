<?php

/**
 * EditForm class. 编辑客户资料
 */
class EditForm extends CFormModel
{
	public $icon,$qq;
	public $nickName;
	public $username;
	public $sex = 0;
	public $birthdate;
	public $memberId;
	public $isCheck; //审核状态：0未审，1审核通过，2审核不通过
	public $checkReason; //审核理由
	private $_model;


	/**
	* Declares the validation rules.
	* The rules state that account and password are required,
	* and password needs to be authenticated.
	*/
	public function rules()
	{
		return array(
			array('memberId,nickName,qq', 'required'),
			array('memberId,nickName,qq,username,sex,birthdate,icon','safe')
		);
	}

	/**
	* 验证码 rule 规则
	*/
	public function getInfo( $memberId,$userId = '' ){
		if( $userId ){
			$condition = 'userId = '.Yii::app()->user->id ;
		}else{
			$condition = '';
		}
		$model = tbMember::model()->with('profile')->findbypk( $memberId ,$condition );
		if( !$model ){
			return ;
		}
		if( !empty( $model->profile ) ){
			$this->attributes =  $model->profile->attributes;
		}
		$this->memberId = $model->memberId;
		$this->nickName = $model->nickName;

		$this->isCheck = $model->isCheck ;
		if( $model->isCheck =='2' ){
			$this->checkReason = '审核不通过原因';
		}
		$this->_model = $model;
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels()
	{
		return array(
			'nickName' => '呢称',
			'qq' => 'qq号码',
			'username' => '真实姓名',
			'birthdate'=>'生日',
		);
	}

	/**
	* 保存编辑内容
	* @return boolean whether login is successful
	*/
	public function save(){
		if( !$this->validate() ){
			return false;
		}

		if( empty($this->_model->profile) ){
			$this->_model->profile =  new tbProfile();
			$this->_model->profile->memberId = $this->memberId;
		}

		$this->_model->profile->birthdate = $this->birthdate;
		$this->_model->profile->qq = $this->qq;
		$this->_model->profile->username = $this->username;
		$this->_model->profile->icon = $this->icon;
		$this->_model->profile->sex = $this->sex;

		if( !$this->_model->profile->save() ){
			$this->addErrors ( $this->_model->profile->getErrors() );
			return false;
		}

		$this->_model->nickName = $this->nickName;
		if( !$this->_model->save() ){
			$this->addErrors ( $this->_model->getErrors() );
			return false;
		}

		//呢称更新后，更新登录的session中的呢称,请勿删除。
		if( $this->_model->memberId == Yii::app()->user->id ){
			Yii::app()->user->setstate('nickName',$this->nickName);
		}
	
		return true;
	}



}