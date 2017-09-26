<?php
/**
 * 会员管理模块控制器
 * @access 会员管理
 * @author yagas
 * @package Controller
 */
class DefaultController extends Controller {

	/**
	 * 显示会员列表
	 * @access 会员列表
	 */
	public function actionIndex() {
		$keyword = Yii::app()->request->getQuery('keyword');
		$companyname = Yii::app()->request->getQuery('companyname');
		$data =  Member::memberList( $keyword ,$companyname );

		//客户等级信息
		$levelList = tbLevel::model()->getLevels();
		$this->render('index',array('users' =>$data['list'], 'pages' => $data['pages'], 'keyword' => $keyword, 'companyname' => $companyname, 'levelList' => $levelList));
	}

	/**
	 * 待审核客户
	 * @access 待审核客户
	 */
	public function actionChecklist() {
		$keyword = Yii::app()->request->getQuery('keyword');
		$data = Member::checkList( $keyword );
		$this->render('checklist',array('users' =>$data['list'], 'pages' => $data['pages'], 'keyword' => $keyword));
	}

	/**
	 * 客户分配
	 * @access 客户分配
	 */
	public function actionDistribution() {
		$keyword = Yii::app()->request->getQuery('keyword');
		$companyname = Yii::app()->request->getQuery('companyname');
		$data =  Member::memberList( $keyword,$companyname );
		$saleList = tbMember::model()->getSaleman();
		$action = 'distribution';

		//客户等级信息
		$levelList = tbLevel::model()->getLevels();

		$this->render('index',array('users' =>$data['list'], 'pages' => $data['pages'], 'keyword' => $keyword, 'saleList' => $saleList, 'action' => $action, 'levelList' => $levelList, 'companyname' => $companyname));
	}

	/**
	 * 分配业务员
	 * @access 分配业务员
	 * @param integer $userId 指定的业务员ID
	 * @param array $memberId 需指定业务员的客户ID
	 */
	public function actionDodistribution() {
		$userId = Yii::app()->request->getPost('userId');
		$memberId = Yii::app()->request->getPost('memberId');
		if(!is_array($memberId)){
			$memberId = explode(',',$memberId );
		}
		if( is_numeric($userId) && $userId >0 && !empty($memberId) ){
			tbMember::model()->updateByPk( $memberId, array('userId'=>$userId) );

			//同步批发价格申请的业务员ID
			tbMemberApplyPrice::model()->updateAll( array('salemanId'=>$userId), 'isDel = 0 and memberId in( '.implode(',',$memberId ).')');
		}

		$this->dealSuccess( Yii::app()->request->urlReferrer );
	}

	/**
	 * 添加会员
	 * @access 添加会员
	 */
	/**
	public function actionAddnew() {
		if( Yii::app()->request->isPostRequest ) {
			$data = Yii::app()->request->getRestParams();
			$user = new SignForm('addnew');
			$user->setAttributes( $data );

			if( $user->validate() === true && $user->save() ) {
				$this->render('successfully');
				Yii::app()->end(200);
			}
			else {
				$this->setError( $user->getErrors() );
			}
		}
		$this->render( 'addnew' );
	}
	*/

	/**
	 * 修改会员基本资料
	 * @access 修改会员基本资料
	 * @param int $memberId 会员id
	 */
	public function actionModify() {
		$memberId = Yii::app()->request->getQuery('memberId');
		$infos = tbMember::model()->with('profile')->findByPk($memberId);
		if( is_null($infos) ) {
			$this->forward('notice/notfound');
			Yii::app()->end( 200 );
		}
		if( is_null($infos->profile) ) {
			$infos->profile = tbProfile::model();
		}

		if( Yii::app()->request->isPostRequest ) {
			$data = Yii::app()->request->getRestParams();
			if( isset($data['file'])) unset($data['file']);
			$user = new ModifyForm();
			$user->setAttributes( $data );
			if( $user->validate() === true && $user->save() ) {
				$this->dealSuccess( Yii::app()->request->urlReferrer );
			}
			else {
				$this->setError( $user->getErrors() );
			}
		}
		//获取仓库
		$warehouseList =tbWarehouseInfo::model()->getInfoAll();

		$this->render( 'modify' , array('infos' => $infos,'type'=>$warehouseList));
	}

	/**
	 * 修改会员详细资料
	 * @access 修改会员详细资料
	 * @param int $memberId 会员id
	 */
	public function actionModifyDetail() {
		$memberId = Yii::app()->request->getQuery('memberId');

		$infos = tbProfileDetail::model()->findByPk($memberId);
		if( !$infos ){
			$infos = new tbProfileDetail();
			$infos -> memberId =$memberId;

		}

		if( Yii::app()->request->isPostRequest ) {
			$data = Yii::app()->request->getRestParams();

			if( isset( $data['mainproduct'] ) ){
				$data['mainproduct'] = serialize($data['mainproduct']);
			}
			$data['gm'] = serialize($data['gm']);
			$data['pdm'] = serialize($data['pdm']);
			$data['designers'] = serialize($data['designers']);
			$data['cfo'] = serialize($data['cfo']);

			$infos->attributes = $data;
			$infos->scenario = 'modify';
			if( $infos->save() ){
				$this->dealSuccess( Yii::app()->request->urlReferrer );
			}else{
				$this->setError( $infos->getErrors() );
			}
		}

		if( isset( $infos['mainproduct'] ) ){
			$infos['mainproduct'] = unserialize($infos['mainproduct']);
		}
		$infos['gm'] = unserialize($infos['gm']);
		$infos['pdm'] = unserialize($infos['pdm']);
		$infos['designers'] = unserialize($infos['designers']);
		$infos['cfo'] = unserialize($infos['cfo']);
		$this->render( 'modifydetail' , array('infos' => $infos));
	}

	/**
	 * 查看会员信息
	 * @access 查看会员信息
	 * @param int $memberId 会员ID
	 */
	public function actionView() {
		$memberId = Yii::app()->request->getQuery('memberId');
		$infos = tbMember::model()->with('profile')->findByPk($memberId);
		if( is_null($infos->profile) ) {
			$infos->profile = tbProfile::model();
		}
		$this->render( 'view' , array('infos' => $infos) );
	}

	/**
	 * 查看会员详细资料
	 * @access 查看会员详细资料
	 * @param int $memberId 会员ID
	 */
	public function actionViewDetail() {
		$memberId = Yii::app()->request->getQuery('memberId');
		$infos = tbProfileDetail::model()->findByPk($memberId);
		if( !$infos ) {
			$infos = new tbProfileDetail();
			$infos -> memberId =$memberId;
		}
		if( isset( $infos['mainproduct'] ) ){
			$infos['mainproduct'] = unserialize($infos['mainproduct']);
		}
		$infos['gm'] = unserialize($infos['gm']);
		$infos['pdm'] = unserialize($infos['pdm']);
		$infos['designers'] = unserialize($infos['designers']);
		$infos['cfo'] = unserialize($infos['cfo']);
		$this->render( 'viewdetail' , array('infos' => $infos) );
	}

	/**
	 * 查看会员配置
	 * @access 查看会员配置
	 * @param int $memberId 会员ID
	 */
	public function actionViewSetting() {
		$this->actionSetting( 'view' );
	}

	/**
	 * 修改会员配置信息
	 * @access 修改会员配置信息
	 * @param int $memberId 会员id
	 */
	public function actionSetting( $action = 'edit' ) {
		$memberId = Yii::app()->request->getQuery('memberId');
		$model = tbMember::model()->findByPk($memberId);
		if( is_null($model) ) {
			$this->forward('notice/notfound');
			Yii::app()->end( 200 );
		}

		$mPay = tbMemberCredit::model()->findByPk( $memberId );

		if( Yii::app()->request->isPostRequest ) {
			$data = Yii::app()->request->getRestParams();

			if( array_key_exists ('monthlyPay',$data ) && $data['monthlyPay'] =='1' ){
				if( !$mPay ){
					$mPay = new tbMemberCredit();
					$mPay->memberId = $memberId;
				}
				$mPay->state = tbMemberCredit::STATE_NORMARL;
				$mPay->billingCycle = $data['billingCycle'];
				$mPay->credit = $data['credit'];
				if( !$mPay->save()){
					$this->dealError( $mPay->getErrors() );
					goto showPage;
				}
			}else{
				if( $mPay ){
					$mPay->state = tbMemberCredit::STATE_DEL;
					$mPay->save();
				}
			}

			/* if( isset( $data['payModel'] ) ){
				$model->payModel  = serialize($data['payModel']);
				$model->monthlyType  = $data['monthlyType'];
				$model->isMonthlyPay  = 1;
			}else{
				$model->payModel  = '';
				$model->monthlyType  = '';
				$model->isMonthlyPay  = 0;
			} */

			$groupId = (int)$data['groupId'];
			if( $groupId> 1 ){
				$model->groupId  = $groupId;
			}

			$level = (int)$data['level'];
			if( $groupId> 1 ){
				$model->level  = $level;
			}

			$priceType = $data['priceType'];
			if( in_array( $priceType,array('0','1') ) ){
				$model->priceType  = $priceType;
			}

			if( $model->save() ) {
				$this->dealSuccess( Yii::app()->request->urlReferrer );
			} else {
				$this->dealError( $user->getErrors() );
			}
		}

		showPage:

		if( is_null( $mPay ) || $mPay->state == tbMemberCredit::STATE_DEL  ){
			$monthlyPay = $credit = $billingCycle = null;
		}else{
			$monthlyPay = true;
			$credit = $mPay->credit;
			$billingCycle = $mPay->billingCycle;
		}


		$saleName = '';
		if( $model->userId ){
			$saleModel = tbProfile::model()->findByPk( $model->userId );
			if( $saleModel ){
				$saleName = $saleModel->username;
			}
		}

		$saleList = $model->getSaleman();
		$levelList = tbLevel::model()->getLevels();
		foreach( $levelList as &$val){
			$val = $val['title'];
		}
		$groupList = tbGroup::model()->getList();
		$this->render( 'setting' ,array('infos' => $model,
										'saleName' => $saleName,
										'groupList' => $groupList,
										'levelList' => $levelList,
										'action' => $action,
										'monthlyPay'=>$monthlyPay,
										'credit'=>$credit,
										'billingCycle'=>$billingCycle,
										));
	}

	/**
	 * 冻结会员
	 * @access 冻结会员
	 * @param int or array $memberId 会员ID字符串或数组
	 */
	public function actionRemove() {
		$memberId = Yii::app()->request->getQuery('id');
		Member::frozen( $memberId );
		$this->dealSuccess( Yii::app()->request->urlReferrer );
	}

	/**
	 * 解冻会员
	 * @access 解冻会员
	 * @param int or array $memberId 会员ID字符串或数组
	 */
	public function actionThaw() {
		$memberId = Yii::app()->request->getQuery('id');
		Member::thaw( $memberId );
		$this->dealSuccess( Yii::app()->request->urlReferrer );
	}


	/**
	 * 重置会员密码
	 * @access 重置密码
	 */
	public function actionResetpassword() {
		$memberId = Yii::app()->request->getQuery('id');
		Member::Resetpassword( $memberId );
		$this->dealSuccess( Yii::app()->request->urlReferrer );
	}

	/**
	 * 审核客户
	 * @access 审核客户
	 */
	public function actionCheck(){
		$memberId = Yii::app()->request->getQuery('memberId');
		$type = Yii::app()->request->getQuery('type');
		switch($type){
			case 'info':
				$this->actionView();
				Yii::app()->end();
				break;
			case 'detail':
				$this->actionViewDetail();
				Yii::app()->end();
				break;
			case 'setting':
				$this->actionSetting( 'check' );
				Yii::app()->end();
				break;
		}

		$member = tbMember::model()->findByPk($memberId);

		$model = new CheckForm();
		$model->memberId = $memberId;

		if( Yii::app()->request->isPostRequest && $member->isCheck != '1' ) {

			$model->state = Yii::app()->request->getPost('state');
			$model->reason = Yii::app()->request->getPost('reason');
			if( $model->check( $member ) ){
				Yii::app()->session->add('alertSuccess',true);
				$this->redirect( $this->createUrl('checklist') );
// 				$this->dealSuccess( Yii::app()->request->url );
			}else{
				$errors = $model->getErrors();
				$this->dealError( $errors );
			}
		}else if( $member->isCheck != '0' ){
			$model->setReason();
			$model->state = $member->isCheck;
			$model->hasCheck = true;
		}
		$this->render('docheck',array( 'data'=>$model->attributes ));
	}



	/**
	* 查看/编辑/审核 tabs
	*/
	protected function tabs(){
		$memberId = Yii::app()->request->getQuery('memberId');
		$type = Yii::app()->request->getQuery('type','op');
		$action = Yii::app()->getController()->getAction()->id;
		switch( $action ){
			case 'modify':
			case 'modifydetail':
			case 'setting':
				$tabs = array(
					'modify'=>array('title'=>'基本资料'),
					'modifydetail'=>array('title'=>'详细资料'),
					'setting'=>array('title'=>'客户配置'),
				);
				break;
			case 'view':
			case 'viewdetail':
			case 'viewsetting':
				$tabs = array(
					'view'=>array('title'=>'基本资料'),
					'viewdetail'=>array('title'=>'详细资料'),
					'viewsetting'=>array('title'=>'客户配置'),
				);
				break;
			case 'check':
				$tabs = array(
					'info'=>array('title'=>'基本资料'),
					'detail'=>array('title'=>'详细资料'),
					'setting'=>array('title'=>'客户配置'),
					'do'=>array('title'=>'审核客户'),
				);
				break;
			default:
				return array();
				break;
		}

		foreach ( $tabs as $key=>$val ){
			if( $action == 'check' ){
				if( $type == $key ){
					$tabs[$key]['class'] = 'active';
					$tabs[$key]['url'] = 'javascript::';
				}else{
					$tabs[$key]['class'] = '';
					$tabs[$key]['url'] = $this->createUrl('check',array('memberId'=>$memberId,'type'=>$key));
				}
			}else{
				if( $action == $key ){
					$tabs[$key]['class'] = 'active';
					$tabs[$key]['url'] = 'javascript::';
				}else{
					$tabs[$key]['class'] = '';
					$tabs[$key]['url'] = $this->createUrl($key,array('memberId'=>$memberId));
				}
			}
		}
		return $tabs;
	}
}