<?php
/**
* 我的客户管理
* @access 我的客户
* @author liang
* @package Controller
*/

class MemberController extends Controller {
	public $pageTitle;


	public function init() {
		parent::init();
		$this->pageTitle .= ' 业务员个人中心';
	}

	public function beforeAction( $action ){
		$userType = Yii::app()->user->getState('usertype');
		if( $userType != tbMember::UTYPE_SALEMAN ){
			$this->redirect('/');
		}
		return  $action ;
	}

	/**
	* 客户管理
	* @access 客户管理
	*/
	public function actionIndex(){
		$param = array('userId'=>Yii::app()->user->id);
		$param['tel']  = Yii::app()->request->getQuery('tel');
		$param['corp'] = Yii::app()->request->getQuery('corp');

		$data = Member::search( $param  );
		$this->render('index',array('list'=>$data['list'],'pages'=>$data['pages'],'corp'=>$param['corp'],'tel'=>$param['tel']));
	}

	/**
	* 新增客户
	* @access 新增客户
	*/
	public function actionAdd(){
		$info=Yii::app()->request->getPost('info');
		if( $info ) {
			$model = new RegForm('addnew');
			$model->attributes = $info;
			$model->account = $model->phone;
			if( $model->register( false ) ){
				$url = $this->createUrl('info',array('id'=>$model->memberId));
				$this->dealSuccess($url);
			} else {
				$errors = $model->getErrors();
				$this->dealError($errors);
			}
		}
		$this->render('add');
	}

	/**
	* 编辑/查看客户信息
	* @access 客户信息
	*/
	public function actionInfo(){
		$memberId = Yii::app()->request->getQuery('id');
		$model = new EditForm();
		$model->getInfo( $memberId,Yii::app()->user->id );
		if( !$model->memberId ){
			throw new CHttpException(404,"This member requires that does not exists.");
		}

		$Editinfo=Yii::app()->request->getPost('Editinfo');
		if( $Editinfo ){
			$model->attributes = $Editinfo;
			if( $model->save() ){
				$url = $this->createUrl('info',array('id'=>$memberId));
				$this->dealSuccess($url);
			}else{
				$errors = $model->getErrors();
				$this->dealError($errors);
			}
		}
		$detailurl = $this->createUrl('detailinfo',array('id'=>$memberId));
		$this->render('//membercenter/editinfo',array('info'=>$model->attributes,'detailurl'=>$detailurl));
	}

	/**
	* 编辑/查看客户详细资料
	* @access 客户详细资料
	*/
	public function actionDetailinfo(){
		$memberId = Yii::app()->request->getQuery('id');
		$model = tbMember::model()->with('profiledetail')->findbypk( $memberId ,'userId = '.Yii::app()->user->id );
		if( !$model ){
			throw new CHttpException(404,"This member requires that does not exists.");
		}
		if( !empty( $model->profiledetail ) ){
			$model = $model->profiledetail;
		}else{
			$model = new tbProfileDetail();
			$model -> memberId = $memberId ;
		}

		$Editdetailinfo = Yii::app()->request->getPost('Editdetailinfo');

		if( $Editdetailinfo ){
			if( isset($Editdetailinfo['mainproduct']) ){
				$Editdetailinfo['mainproduct']= serialize($Editdetailinfo['mainproduct']);
			}
			$Editdetailinfo['gm'] = serialize($Editdetailinfo['gm']);
			$Editdetailinfo['pdm'] = serialize($Editdetailinfo['pdm']);
			$Editdetailinfo['designers'] = serialize($Editdetailinfo['designers']);
			$Editdetailinfo['cfo'] = serialize($Editdetailinfo['cfo']);
			$model->attributes = $Editdetailinfo;
			$model->scenario = 'modify';
				if ( $model->validate() ){
					$model->save();
					$url=$this->createUrl('detailinfo',array('id'=>$memberId));
					$this->dealSuccess($url);
				} else {
					$errors = $model->getErrors();
					$this->dealError($errors);
				}

		}

		$info = $model->attributes;

		if( isset( $info['mainproduct'] ) ){
			$info['mainproduct'] = unserialize($info['mainproduct']);
		}
		$info['gm'] = unserialize($info['gm']);
		$info['pdm'] = unserialize($info['pdm']);
		$info['designers'] = unserialize($info['designers']);
		$info['cfo'] = unserialize($info['cfo']);
		$infourl = $this->createUrl('info',array('id'=>$memberId));
		$this->render('//membercenter/editdetailinfo',array('info'=>$info,'infourl'=>$infourl));

	}


}