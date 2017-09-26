<?php
/**
 * 签约表单校验对象
 * @author yagas
 * @package CFormModel
 * @subpackage SignForm
 */
class SettingForm extends CFormModel {

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
	public $groupId;
	
	/**
	 * @var string
	 */
	public $userId;
	
	/**
	 * @var string
	 */
	public $level;
	
	/**
	 * @var string
	 */
	public $payModel;
	
	/**
	 * @var string
	 */
	public $monthlyType;
	
	/**
	 * @var int
	 */
	public $priceType;
	
	
	/**
	 * 表单校验规则
	 * @see CModel::rules()
	 */
	public function rules() {
		return array(
			array('memberId', 'required', 'message'=>'{filed} mush be fill'),
			//array('sex,username,birthdate,face,groupId,userId,level,payModel,monthlyType','safe'),
			array('groupId,userId,level,priceType,payModel,monthlyType','safe','on'=>'setting')
		);
	}
	/**
	 * 数据存储入库
	 * @return boolean
	 */
	public function save() {
		$action = $this->getScenario ();
		$action = "do" . ucfirst ( $action );
		if (! method_exists ( $this, $action )) {
			throw new CHttpException ( 500, 'Not found method ' . $action );
		}
		return call_user_func ( array ( $this, $action ) );
	}
	/**
	 * 更新配置信息
	 * @return boolean
	 */
	public function doSetting() {
		//首先假定更新会失败
		$result = false;
		$user = tbMember::model ()->find ( "memberId=:memberId", array (
				':memberId' => $this->memberId
		) );
		if ($user instanceof tbMember) {
			$user->memberId     = $this->memberId;
			$user->groupId      = $this->groupId;
			$user->userId       = $this->userId;
			$user->priceType     = $this->priceType;
			$user->level        = $this->level;
			$user->payModel     = $this->payModel;			
			$user->monthlyType  = $this->monthlyType;
			$result = $user->save();
		}
		return $result;
	}
	

}