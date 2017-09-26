<?php
/**
 * 签约表单校验对象
 * @author yagas
 * @package CFormModel
 * @subpackage SignForm
 */
class DetailForm extends CFormModel {
	
	/**
	 *
	 * @var string 会员ID
	 */
	public $memberId;
	
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
			return array (
					array('companyname,areaId,address,corporate,tel,companytype,saleregion,mainproduct,peoplenumber,outputvalue,brand,stalls,stallsaddress,factory,factoryatt,gm,pdm,designers,cfo,memberId','safe'),
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
	
}