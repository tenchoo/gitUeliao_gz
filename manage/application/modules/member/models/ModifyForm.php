<?php
/**
 * 签约表单校验对象
 * @author yagas
 * @package CFormModel
 * @subpackage SignForm
 */
class ModifyForm extends CFormModel {

	/**
	 * @var string
	 */
	public $face;

	/**
	 * @var string
	 */
	public $nickName;

	/**
	 * @var string
	 */
	public $qq;

	/**
	 * @var string
	 */
	public $username;

	/**
	 * @var string
	 */
	public $sex;

	/**
	 * @var string
	 */
	public $birthdate;

	/**
	 * @var string
	 */
	public $phone;

	/**
	 * @var string
	 */
	public $ip;

	/**
	 * @var string
	 */
	public $memberId;

 /**
	 * @var string
	 */
	public $sortingWarehouseId;
	/**
	 * 表单校验规则
	 * @see CModel::rules()
	 */
	public function rules() {
		return array(
			array('nickName,qq,memberId,sortingWarehouseId', 'required', 'message'=>'{filed} mush be fill'),
			array('sex,username,birthdate,face,sortingWarehouseId','safe'),
		);
	}


	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签
	*/
	public function attributeLabels(){
		return array(
			'sortingWarehouseId' => '仓库ID',

		);
	}

	/**
	 * 数据存储入库
	 * @return boolean
	 */
	public function save() {
		$user = tbMember::model()->find("memberId=:id",array(":id"=>$this->memberId));
		if( is_null($user) ) {
			return false;
		}
		$user->nickName = $this->nickName;

		if( $user->save() ) {
			$profile = tbProfile::model()->find("memberId=:id",array(":id"=>$this->memberId));
			if( is_null($profile) ) {
				return false;
			}
			$profile->memberId  = $user->memberId;
			$profile->sex       = $this->sex;
			$profile->username  = $this->username;
			$profile->icon      = $this->face;
			$profile->qq        = $this->qq;
			$profile->birthdate = $this->birthdate;
			$profile->sortingWarehouseId = $this->sortingWarehouseId;
			if( $profile->save() ) {
				return true;
			}
			else {
// 				$user->delete();
			}
		}
		return false;
	}

	/**
	 *
	 * @param unknown $memberId
	 */
	public function readProfile( $memberId ) {

	}
}