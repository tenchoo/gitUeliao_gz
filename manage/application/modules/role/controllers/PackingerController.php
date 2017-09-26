<?php
/**
 * 分拣员管理
 * @access 分拣员管理
 * @author liang
 *
 */
class PackingerController extends Controller {

	/**
	 * 分拣员管理
	 * @access 分拣员管理
	 */
	public function actionIndex() {
		$model = new tbUserPackinger();
		$model->setPackingRoleId();

		if( empty( $model->packing_roleId ) || !( $role = tbRole::model()->findByPk( $model->packing_roleId ) ) ){
			$this->redirect( $this->createUrl('setrole') );
			exit;
		}

		//查找分拣员组关联的职位
		$condition['username'] = trim(Yii::app()->request->getQuery('username'));
		$condition['warehouseId'] = trim(Yii::app()->request->getQuery('warehouseId'));

		$perSize = tbConfig::model()->get( 'page_size' );
		$data = $model->search( $condition,$perSize );
		$data['warehouse'] = tbWarehouseInfo::model()->getAll();
		$data['departments'] = tbDepartment::model()->getAll();
		$data['roleId'] = $role->roleId;
		$data['roleName'] = $role->roleName;
		$this->render( 'index', array_merge($condition,$data));
	}

	/**
	 * 分拣员管理
	 * @access 关联角色组
	 */
	public function actionSetrole(){
		$model = tbConfig::model()->find( "`key`=:key", array(':key'=>'packing_roleId') );
		if( !$model ){
			exit( '缺少config配置' );
		}

		$roleModel = tbRole::model()->findAll( 'roleId>1' );
		$roles = array();
		foreach ( $roleModel as $val ){
			$roles[$val->roleId] = $val->roleName;
		}

		if( Yii::app()->request->isPostRequest ) {
			$model->value = Yii::app()->request->getPost('roleId');

			if( !array_key_exists( $model->value,$roles ) ){
				$model->value = '';
			}
			if( $model->save() ) {
				$this->dealSuccess( $this->createUrl('index') );
			}else{
				$this->dealError( $model->getErrors() );
			}
		}


		$this->render( 'setrole', array( 'roles'=>$roles ,'roleId' => $model->value) );
	}

	/**
	 * 编辑分拣员
	 * @access 编辑分拣员
	 * @throws CHttpException
	 */
	public function actionChooseware() {
		$id = Yii::app()->request->getQuery('id');
		if( is_null($id) || !is_numeric($id) || ($user = tbUser::model()->findByPk( $id,'state = 0'))==null ) {
			throw new CHttpException( '404', 'Not found record' );
		}

		$model = tbUserPackinger::model()->findByPk( $id );
		if( !$model ){
			$model = new tbUserPackinger();
			$model->userId = $id;
		}
		$warehouse = tbWarehouseInfo::model()->getAll();

		if( Yii::app()->request->isPostRequest ) {
			$model->warehouseId = Yii::app()->request->getPost('warehouseId');
			if( !array_key_exists( $model->warehouseId,$warehouse ) ){
				$model->warehouseId = '';
			}
			$model->state = 0;
			if( $model->save() ) {
				$this->dealSuccess( $this->createUrl('index') );
			}else{
				$this->dealError( $model->getErrors() );
			}
		}

		$this->render( 'chooseware', array('username'=>$user->username,'warehouseId'=>$model->warehouseId,'warehouse'=>$warehouse) );
	}
}