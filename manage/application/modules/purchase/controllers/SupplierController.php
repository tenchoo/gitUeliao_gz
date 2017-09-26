<?php
/**
 * 厂商管理
 * @access 厂商管理
 * @author liang
 * @package Controller
 * @version 0.1
 *
 */
class SupplierController extends Controller {

	/**
	 * 厂商列表
	 * @access 厂商列表
	 */
	public function actionIndex() {
		$keyword = Yii::app()->request->getQuery('keyword');
		$factoryNumber = Yii::app()->request->getQuery('factoryNumber');
		$criteria = new CDbCriteria;
		$criteria->compare('t.state','0');
		$criteria->order = 'supplierId desc';
		if( $keyword ){
			$criteria->compare('t.shortname',$keyword,true);
		}
		if( $factoryNumber ){
			$criteria->compare('t.factoryNumber',$factoryNumber);
		}
		$pageSize = tbConfig::model()->get( 'page_size' );
		$model = new CActiveDataProvider('tbSupplier', array(
			'criteria'=>$criteria,
			'pagination'=>array('pageSize'=>$pageSize,'pageVar'=>'page'),
		));
		$list = $model->getData();
		$pages = $model->getPagination();
		$this->render( 'index', array('list' => $list,'pages'=>$pages,'keyword'=>$keyword,'factoryNumber'=>$factoryNumber) );
	}

	/**
	 * 编辑厂商
	 * @access 编辑厂商
	 * @throws CHttpException
	 */
	public function actionEdit() {
		$id = Yii::app()->request->getQuery('id');
		if( is_null($id) || !is_numeric($id) || ($model = tbSupplier::model()->findByPk( $id ))==null ) {
			throw new CHttpException( '404', 'Not found record' );
		}
		$this->saveData( $model );
	}

	/**
	* 新增厂商
	* @access 新增厂商
	* @throws CHttpException
	*/
	public function actionAdd(){
		$model = new tbSupplier();
		$this->saveData( $model );
	}

	/**
	 * 删除厂商
	 * @access 删除厂商
	 */
	public function actionDel() {
		$id = Yii::app()->request->getQuery('id');
		if( is_numeric($id) && $id >0 ){
			tbSupplier::model()->updateByPk( $id,array('state'=>1) );
		}
		$this->dealSuccess( Yii::app()->request->urlReferrer );
	}

	public function actionAccount() {
		$id = Yii::app()->request->getQuery('id');
		if(is_null($id)) {
			$this->setError([Yii::t('base', 'invalid seller supplierId')]);
			$this->render('create');
			Yii::app()->end();
		}

		$userInfo = tbSupplierAccount::model()->findAllByAttributes(['supplierId'=>$id]);
		$this->render('accounts', ['sellers'=>$userInfo]);
	}

	public function actionNewaccount() {
		$seller = new tbSupplierAccount;

		if(Yii::app()->request->getIsPostRequest()) {
			$form = Yii::app()->request->getPost('form');
			if(strnatcmp($form['password'], $form['comfirmpassword'])!==0) {
				$this->setError(['password'=>Yii::t('base','The two passwords not match')]);
			}
			else {
				$seller->setAttributes(['account'=>$form['account'], 'password'=>$form['password'], 'supplierId'=>$form['supplierId']]);

				if($seller->save()) {
					Yii::app()->session->add('alertSuccess',true);
					$this->redirect($this->createUrl('account',['id'=>$seller->supplierId]));
				}
				else {
					$this->setError(['password'=>Yii::t('base','failed seller account save')]);
				}
			}
		}
		$this->render('newaccount', ['seller'=>$seller]);
	}

	public function actionEditaccount() {
		$id      = Yii::app()->request->getQuery('id');
		$seller = tbSupplierAccount::model()->findByPk($id);
		if(!$seller) {
			$this->setError(['account'=>Yii::t('seller','seller account not exists')]);
			$seller = new tbSupplierAccount;
		}

		if(Yii::app()->request->getIsPostRequest()) {
			if(!$seller->isNewRecord) {
				$form = Yii::app()->request->getPost('form');
				if(strnatcmp($form['password'], $form['comfirmpassword'])!==0) {
					$this->setError(['password'=>Yii::t('base','The two passwords not match')]);
				}
				else {
					$seller->setAttributes(['account'=>$form['account'], 'password'=>$form['password']]);

					if($seller->save()) {
						Yii::app()->session->add('alertSuccess',true);
						$this->redirect($this->createUrl('account',['id'=>$seller->supplierId]));
					}
					else {
						$this->setError(['password'=>Yii::t('base','failed seller account save')]);
					}
				}
			}
		}
		$this->render('editaccount', ['seller'=>$seller]);
	}

	/**
	* 保存厂商数据
	*/
	private function saveData( $model ){
		if( Yii::app()->request->isPostRequest ) {
			$model->attributes = Yii::app()->request->getPost('data');
			if( $model->save() ) {
				$this->dealSuccess( $this->createUrl('index') );
			}else{
				$this->dealError( $model->getErrors() );
			}
		}
		$this->render( 'edit',$model->attributes );
	}
}