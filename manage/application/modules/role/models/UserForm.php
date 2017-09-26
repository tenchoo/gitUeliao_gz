<?php
/**
 * 员工管理，新增编辑员工，员工列表，搜索
 * @author liang
 * @version 0.1
 * @package CFormModel
 */
class UserForm extends CFormModel {

	public $username,$account,$userId,$printerId;

	public $gender;

	public $isAdmin;

	public $departmentId,$depPositionId,$repassword,$password;

	public $updatePasswd;

	private $_model,$_nowRoleids;

	/**
	 * on = [create:创建, put:全部更新, patch:更新部份内容]
	 * @see CModel::rules()
	 */
	public function rules()	{
		return array(
			/** 必填项 */
			array('username,departmentId,depPositionId,account,isAdmin','required'),
			array('password,repassword','required','on'=>'create,put'),
			/** 密码检验 */
			array('isAdmin','in','range'=>array(0,1)),
			array('updatePasswd','in','on'=>'put','range'=>array(0,1)),
			array('password','length','on'=>'create,put', 'min'=>6, 'max'=>16,'tooLong'=>Yii::t('base','Password length of 6-16, must contain data and letters')),
			array('repassword','compare','on'=>'create,put','compareAttribute'=>'password','message'=>Yii::t('base','The two passwords not match')),
			array('gender','in','range'=>array(0,1)),
			array('userId,departmentId,depPositionId', "numerical","integerOnly"=>true ),
			array('account', 'match', 'pattern'=>Regexp::$mobile, 'message'=>Yii::t('base','Mobile phone number format is not correct'),'on'=>'phoneaccount'),
			/** 其它 */
			array('username,remark,products,roleids,account,updatePasswd,password,repassword,printerId','safe'),
			array('account','checkAccount'),
		);
	}

	/**
	* 验证码 rule 规则
	*/
	public function checkAccount($attribute,$params){
		if( !$this->hasErrors() ) {
			if( $this->isAdmin != '1' && !preg_match( Regexp::$mobile,$this->account ) ) {
				$this->addError('account',Yii::t('base','Mobile phone number format is not correct'));
				return true;
			}
		}
	}

	public function attributeLabels() {
		return array(
			'username' => '员工姓名',
			'gender' => '性别',
			'departmentId'=>'所属部门',
			'depPositionId'=>'所在职位',
			'password' => '登录密码',
			'repassword' =>'确认密码',
			'account'=>'手机号码/账号',
			'updatePasswd' => '修改密码',
			'isAdmin'=>'是否超级管理员',
		);
	}

	/**
	* 根据userId取得用户资料
	* @param integer $userId
	*/
	public function setModel( $userId ){
		if( empty( $userId ) ){
			return false;
		}
		$this->_model = tbUser::model()->findByPk( $userId );
		if( $this->_model ){
			$this->attributes = $this->_model->getAttributes(array('userId','username','departmentId','depPositionId','account','gender','isAdmin','printerId'));
			return true;
		}
		return false;
	}

	/**
	* 保存员工
	*/
	public function save(){
		if( !$this->validate() ) {
			return false ;
		}

		/** 开始事务处理 */
		$transaction = Yii::app()->db->beginTransaction();
		try {
			if($this->scenario === 'create') {
				$this->_model = new tbUser();
			}

			/** 需要写入数据库的字段 */
			$attributes = array('username','gender','departmentId','depPositionId','account','printerId');
			if($this->scenario === 'create' || $this->scenario === 'put') {
				array_push($attributes, 'password');
				$this->_model->isUpdatePassword();
			}

			$this->_model->attributes = $this->getAttributes($attributes);
			$this->_model->isAdmin = $this->isAdmin;
			if(!$this->_model->save()){
				$this->addErrors( $this->_model->getErrors() );
				return false;
			}

			/** 提交事务处理 */
			$transaction->commit();
			return true;
		}
		catch (Exception $e) {
			/** 事物处理回滚 */
			$transaction->rollback();
			throw new CHttpException(503,$e);
			return false;
		}
	}

	public function search( $condition = array(),$perSize = 1 ){
		$criteria = new CDbCriteria;
		if( is_array($condition) ){
			foreach ( $condition as $key=>$val ){
				if( $val=='' ) continue;
				$criteria->compare('t.'.$key,$val);
			}
		}
		$criteria->with = array('position','roles');
		$model = new CActiveDataProvider('tbUser', array(
			'criteria'=>$criteria,
			'pagination'=>array('pageSize'=>$perSize,'pageVar'=>'page'),
		));

		$result['list'] = $model->getData();
		$result['pages'] = $model->getPagination();

		foreach ( $result['list'] as &$val ){
			if( $val->isAdmin == '1' ){
				$roles = '超级管理员';
			}else{
				$roles = array_map(function ($i){
					return $i->roleName;
				},$val->roles);
				$roles = implode(',',$roles);
			}

			$positionName = ($val->position)?$val->position->positionName:'';
			$val = $val->attributes;
			$val['positionName'] = $positionName;
			$val['roles'] = $roles;
		}
		return $result;
	}
}