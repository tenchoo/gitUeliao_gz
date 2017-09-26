<?php
/**
 * 签约表单校验对象
 * @author yagas
 * @package CFormModel
 * @subpackage SignForm
 */
class SignForm extends CFormModel {
	
	/**
	 *
	 * @var string 会员ID
	 */
	public $memberId;
	
	/**
	 *
	 * @var string 手机号码
	 */
	public $phone;
	
	/**
	 *
	 * @var string 邮箱
	 */
	public $email;
	
	/**
	 *
	 * @var string 登陆密码
	 */
	public $password;
	
	/**
	 *
	 * @var string
	 */
	public $repassword;
	
	/**
	 *
	 * @var string 会员头像
	 */
	public $face;
	
	/**
	 *
	 * @var string 会员匿称
	 */
	public $nickName;
	
	/**
	 *
	 * @var string
	 */
	public $qq;
	
	/**
	 *
	 * @var string 真实姓名
	 */
	public $username;
	
	/**
	 *
	 * @var string 性别
	 */
	public $sex;
	
	/**
	 *
	 * @var string 生日
	 */
	public $birthdate;
	/**
	 *
	 * @var string 公司名称
	 */
	public $companyname;
	
	/**
	 *
	 * @var string 省市ID
	 */
	public $areaId;
	
	/**
	 *
	 * @var string 地址
	 */
	public $address;
	
	/**
	 *
	 * @var string 企业法人
	 */
	public $corporate;
	
	/**
	 *
	 * @var string 电话
	 */
	public $tel;
	
	/**
	 *
	 * @var string 公司性质
	 */
	public $companytype;
	
	/**
	 *
	 * @var string 销售区域
	 */
	public $saleregion;
	
	/**
	 *
	 * @var string 主营产品
	 */
	public $mainproduct;
	
	/**
	 *
	 * @var string 生产人数
	 */
	public $peoplenumber;
	
	/**
	 *
	 * @var string 年产出
	 */
	public $outputvalue;
	
	/**
	 *
	 * @var string 品牌
	 */
	public $brand;
	
	/**
	 *
	 * @var string 有无档口
	 */
	public $stalls;
	
	/**
	 *
	 * @var string 档口地址
	 */
	public $stallsaddress;
	
	/**
	 *
	 * @var string 有无工厂
	 */
	public $factory;
	
	/**
	 *
	 * @var string 工厂属性
	 */
	public $factoryatt;
	
	/**
	 *
	 * @var string 总经理
	 */
	public $gm;
	
	/**
	 *
	 * @var string 采购经理
	 */
	public $pdm;

	/**
	 *
	 * @var string 设计人员
	 */
	public $designers;
	
	/**
	 *
	 * @var string 财务经理
	 */
	public $cfo;
	

	/**
	 * 表单校验规则
	 * 
	 * @see CModel::rules()
	 */
	public function rules() {
		if ($this->getScenario () == 'addnew') {
			return array (
					array (
							'email,phone,password,nickname,qq,username,nickname,qq',
							'required',
							'message' => '{filed} mush be fill' 
					),
					array (
							'password',
							'compare',
							'operator' => '!=',
							'compareAttribute' => 'repassword',
							'message' => 'passwords not match' 
					),
					array (
							'email',
							'email',
							'message' => 'email format has been error' 
					),
					array (
							'phone',
							'numerical',
							'message' => 'phone format has been error' 
					),
					array (
							'email',
							'unique',
							'className' => 'Member',
							'attributeName' => 'email',
							'message' => 'The accout already exists' 
					),
					array (
							'phone',
							'unique',
							'className' => 'Member',
							'attributeName' => 'phone',
							'message' => 'The accout already exists' 
					),
					array('birthdate,face,username,sex','safe'),
			);
		} elseif ($this->getScenario () == 'modify') {
			return array (
					array('sex,nickName,password,qq,username,birthdate,face,memberId','safe'),
			);
		} elseif ($this->getScenario () == 'modifydetail') {
			return array (
					array('companyname,areaId,address,corporate,tel,companytype,saleregion,mainproduct,peoplenumber,outputvalue,brand,stalls,stallsaddress,factory,factoryatt,gm,pdm,designers,cfo,memberId','safe'),
			);
		}
		else {
			return array (
					array (
							'nickname,qq,memberId',
							'required',
							'message' => '{filed} mush be fill' 
					) 
			);
		}
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
	 * 读取会员信息
	 * @param int $memberId    
	 * @return array|false
	 */
	public function readProfile($memberId) {
		//读取会员帐号信息
		$User = tbMember::model ()->find ( "memberId=:memberId", array ( ":memberId" => $memberId ) );
		
		if ($User instanceof tbMember) {
			//读取会员基本信息
			$info = $User->getAttributes ();
			$Profile = Profile::model ()->find ( "memberId=:memberId", array ( ":memberId" => $memberId ) );
			
			if (($Profile instanceof Profile) == true) {
				$info = array_merge ( $info, $Profile->getAttributes () );
				return $info;
			}
		}
		return false;
	}
	
	/**
	 * 添加会员
	 * @return boolean
	 */
	public function doAddNew() {
		$user           = new tbMember ();
		$user->email    = $this->email;
		$user->phone    = $this->phone;
		$user->code     = $user->setrandomCode ();
		$user->password = $user->passwordEncode ( $this->password );
		$user->nickName = $this->nickname;
		$user->ip       = Yii::app ()->request->userHostAddress;
		
		if ( $user->save () ) {
			$profile = new Profile ();
			$profile->memberId  = $user->memberId;
			$profile->sex       = $this->sex;
			$profile->username  = $this->username;
			$profile->icon      = $this->face;
			$profile->qq        = $this->qq;
			$profile->birthdate = $this->birthdate;
			if ( $profile->save() ) {
				return true;
			} else {
				$this->addErrors( $profile->getErrors() );
				// $user->delete();
			}
		}
		return false;
	}
	
	/**
	 * 更新会员信息
	 * @return boolean
	 */
	public function doModify() {
		//首先假定更新会失败
		$result = false;
		$profile = tbProfile::model ()->find ( "memberId=:memberId", array (
				':memberId' => $this->memberId 
		) );
		if ($profile instanceof tbProfile) {
			$profile->sex       = $this->sex;
			$profile->username  = $this->username;
			$profile->icon      = $this->face;
			$profile->qq        = $this->qq;
			$profile->birthdate = $this->birthdate;
			$result = $profile->save();
		}
		return $result;
	}
	
	/**
	 * 更新会员详细资料
	 * @return boolean
	 */
	public function doModifyDetail() {
		//首先假定更新会失败
		$result = false;
		$profiledetail = tbProfileDetail::model ()->find ( "memberId=:memberId", array (
				':memberId' => $this->memberId
		) );
		if ($profiledetail instanceof tbProfileDetail) {
			$profiledetail->memberId       = $this->memberId;
			$profiledetail->companyname    = $this->companyname;
			$profiledetail->areaId         = $this->areaId;
			$profiledetail->address        = $this->address;
			$profiledetail->corporate      = $this->corporate;
			$profiledetail->tel            = $this->tel;
			$profiledetail->companytype    = $this->companytype;
			$profiledetail->saleregion     = $this->saleregion;
			$profiledetail->mainproduct    = $this->mainproduct;
			$profiledetail->peoplenumber   = $this->peoplenumber;
			$profiledetail->outputvalue    = $this->outputvalue;
			$profiledetail->brand          = $this->brand;
			$profiledetail->stalls         = $this->stalls;
			$profiledetail->stallsaddress  = $this->stallsaddress;
			$profiledetail->factory        = $this->factory;
			$profiledetail->factoryatt     = $this->factoryatt;
			$profiledetail->gm             = $this->gm;
			$profiledetail->pdm            = $this->pdm;
			$profiledetail->designers      = $this->designers;
			$profiledetail->cfo            = $this->cfo;
			$result = $profiledetail->save();
		}
		return $result;
	}
	
	/**
	 * 会员搜索条件组合
	 * @param array $data 搜索数据
	 * @return int or string 返回用户收数据类型是否为邮箱或手机号码，都不是返回错误字符串
	 */
	public static function regularVerify($data) {
		if ( empty ( $data ) ) {
			return false ;
		}
		$phoneEmail = trim($data['phoneEmail']);
		$validator = new CEmailValidator;
		$validator->allowEmpty=false;
	
		$res['fields'] = '1';
		if ( $validator->validateValue( $phoneEmail ) ) {
			$res['fields'] .= " AND email='{$phoneEmail}' ";
		}
		elseif( preg_match( Regexp::$mobile, $phoneEmail ) ){
			$res['fields'] .= " AND phone={$phoneEmail} ";
		}
		else {
			return false;
		}
	
		return $res;
	
	}
}