<?php
/**
 * 会员收货地址页面
 * @author morven
 * @package member
 */
class AddressController extends Controller {

	public function init(){
		parent::init();
		$userType = Yii::app()->user->getState('usertype');
		if( $userType == 'saleman'){
			$this->redirect('/');
		}
	}

	/**
	 * 默认首页
	 */
	public function actionIndex() {
		//查询会员地址
		$address = tbMemberAddress::model()->findAll('memberId=:memberId',array(':memberId'=>Yii::app()->user->id));
		//新建
		$data = new tbMemberAddress();
		$count = count($address);//总条数
		$this->render('index',array('data'=>$data,'address'=>$address,'count'=>$count));
	}


	/**
	 * AJAX更新收货地址
	 */
	public function actionUpdate() {
		$memberId = Yii::app()->user->id;
		$addressId = (int)Yii::app()->request->getQuery( 'id' );
		$data = tbMemberAddress::model()->findByAttributes( array('memberId'=>$memberId,'addressId'=>$addressId));
		$this->renderPartial('_address', array('data'=>$data), false, true);
	}



	/**
	 * 新增收货地址
	 * @param array $MemberAddress 数据
	 */
	public function actionSave() {
		$address  = Yii::app()->request->getPost( 'MemberAddress' );
		if( empty($address) ){
			$this->dealMessage('msg','NO Data');
			exit;
		}

		$memberId = Yii::app()->user->id;
		$model = new tbMemberAddress();
		if( empty( $address['addressId'] ) ){
			$model->memberId = $memberId;

			//最多只能有10个地址
			$count = $model-> getCount( $memberId );
			if( $count >= 10 ){
				$errors ='Receive the goods address 10';
				$this->dealMessage('msg',$errors);
				exit;
			}
		}else{
			$model = $model->findOne( $address['addressId'],$memberId );
			if( !$model ){
				$this->dealMessage('msg','NO Data');
				exit;
			}
		}
		unset($address['addressId']);
		$model->attributes = $address;
		if($model->save()) {
			$cookie = new CHttpCookie('addressId',$model->addressId);
			$cookie->expire = time()+60*60*24*1;  //有限期1天
			Yii::app()->request->cookies['addressId']=$cookie;
			$url=$this->createUrl('index');
			$this->dealSuccess($url);
		}else{
			$this->dealError( $model->getErrors() );
		}
	}

	/**
	 * 删除会员地址
	 * @param int $id 地址ID
	 */
	public function actionDelete($id)
	{
		$memberId = Yii::app()->user->id;
		$model=tbMemberAddress::model()->findByPk($id,'memberId=:memberId',array(':memberId'=>$memberId));
		if($model){
			if($model->delete()){
				$this->dealSuccess('');
			}else{
				$errors = $model->getErrors();
				$this->dealError($errors);
				exit;
			}
		}else{
			$errors ='NO Data';
			$this->dealMessage('msg',$errors);
			exit;
		}
	}

	/**
	 * 设置默认值
	 * @param int $id 地址ID
	 */
	public function actionIsdefault($id)
	{
		$memberId = Yii::app()->user->id;
		$model= tbMemberAddress::model()->findByPk($id,'memberId=:memberId',array(':memberId'=>$memberId));
		if($model){
			tbMemberAddress::model()->updateAll( array('isDefault'=>0 ) , 'memberId=:memberId' , array( ':memberId'=>$memberId ) ) ;
			$model->isDefault = 1 ;
			if($model->update()){
				$this->dealSuccess('');
			}else{
				$errors = $model->getErrors();
				$this->dealError($errors);
				exit;
			}
		}else{
			$errors ='NO Data';
			$this->dealMessage('msg',$errors);
			exit;
		}

	}
}