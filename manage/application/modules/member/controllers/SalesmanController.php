<?php
/**
 * 业务员管理模块控制器
 * @access 业务员管理
 * @author liang
 * @package Controller
 */
class SalesmanController extends Controller {


	/**
	 * 显示业务员列表
	 * @access 业务员列表
	 */
	public function actionIndex() {
		$data = Member::search(array('groupId'=>1));
		$param = '';
		$this->render('index',array('list' =>$data['list'], 'pages' => $data['pages'], 'param' => $param));
	}

	/**
	 * 添加业务员
	 * @access 添加业务员
	 */
	public function actionAddedit() {
		$memberId = Yii::app()->request->getQuery('memberId');
		if( $memberId ){
			$model = tbMember::model()->with('profile')->findByPk( $memberId );
			if( !$model ){
				throw new CHttpException(404,"the require obj has not exists.");
			}
		}else{
			$model = new tbMember();
		}

		if( Yii::app()->request->isPostRequest ){
			$data = Yii::app()->request->getPost('data');
			$member = new Member();
			$result = $member->editSaleman( $data,$model );
			if( $result ) {
				$from = Yii::app()->request->getQuery('from');
				if( $from ){
					$url = urldecode($from);
				}else{
					$url = $this->createUrl( 'index' );
				}
				$this->dealSuccess( $url  );
			} else {
				$errors = $member->getErrors();
				$this->dealError( $errors );
			}
		}else{
			$data = $model->attributes;
			if( $model->profile ){
				$data['username'] = $model->profile->username;
			}else{
				$data['username'] = '';
			}
		}
		
		$this->render( 'addedit',array('data'=>$data,'memberId'=>$memberId) );
	}
	
	
	/**
	 * 冻结业务员
	 * @access 冻结业务员
	 * @param int or array $memberId 会员ID字符串或数组
	 */
	public function actionRemove() {
		$memberId = Yii::app()->request->getQuery('id');
		Member::frozen( $memberId );
		$this->dealSuccess( Yii::app()->request->urlReferrer );
	}

	/**
	 * 解冻业务员
	 * @access 解冻业务员
	 * @param int or array $memberId 会员ID字符串或数组
	 */
	public function actionThaw() {
		$memberId = Yii::app()->request->getQuery('id');
		Member::thaw( $memberId );
		$this->dealSuccess( Yii::app()->request->urlReferrer );
	}
}