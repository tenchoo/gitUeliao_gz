<?php
/**
 * 职位管理
 * @access 职位管理
 * @author yagas
 *
 */
class DeppositionController extends Controller {

	/**
	 * 职位管理
	 * @access 职位管理
	 */
	public function actionIndex() {
		$id = Yii::app()->request->getQuery('id');
		$positionName = Yii::app()->request->getQuery('positionName');
		$criteria = new CDbCriteria();
		$criteria->condition = 't.departmentId=:id and t.state=:state';
		$criteria->params = array(':id'=>$id, 'state'=>0);

		$pages = new CPagination();
		$pages->setItemCount(tbDepPosition::model()->count($criteria));
		$pages->setPageSize(tbConfig::model()->get( 'page_size' ));
		$pages->applyLimit($criteria);

		$listData = tbDepPosition::model()->with('department')->findAll($criteria);

		$this->render( 'index', ['dataList'=>$listData, 'pages'=>$pages, 'positionName'=>$positionName]);
	}

	/**
	 * 编辑职位
	 * @access 编辑职位
	 * @throws CHttpException
	 */
	public function actionEdit() {
		$id = Yii::app()->request->getQuery('id');
		if( is_null($id) || !is_numeric($id) || ($model = tbDepPosition::model()->findByPk( $id ))==null ) {
			throw new CHttpException( '404', 'Not found record' );
		}
		$this->saveData( $model );
	}

	/**
	* 新增职位
	* @access 新增职位
	* @throws CHttpException
	*/
	public function actionAdd(){
		$model = new tbDepPosition();
		$model->departmentId = Yii::app()->request->getQuery('dep');
		$this->saveData( $model );
	}

	/**
	 * 删除职位
	 * @access 删除职位
	 */
	public function actionDel() {
		$id = Yii::app()->request->getQuery('id');
		if( is_numeric($id) && $id >0 ){
			tbDepPosition::model()->updateByPk( $id,array('state'=>1) );
		}
		$this->dealSuccess( Yii::app()->request->urlReferrer );
	}

	/**
	* 保存职位数据
	*/
	private function saveData( $model ){
		$department = tbDepartment::model()->findByPk( $model->departmentId );
		if( !$department ){
			throw new CHttpException( '404', 'Not found department' );
		}

		if( Yii::app()->request->isPostRequest ) {
			$model->positionName = Yii::app()->request->getPost('positionName');

			$trans = Yii::app()->db->beginTransaction();
			if( $model->save() ) {
				$roleIds = Yii::app()->request->getPost('roleId');

				//清除原有角色的绑定，再重新分配角色
				tbRoleGroup::model()->removeRoles($model->depPositionId);
				if( is_array( $roleIds ) ){
					foreach($roleIds as $roleId) {
						$role = new tbRoleGroup();
						$role->departmentId = $model->departmentId;
						$role->deppositionId = $model->depPositionId;
						$role->roleId = $roleId;
						if(!$role->save()) {
							$trans->rollback();
							$this->dealError( $role->getErrors() );
							goto showpage;
						}
					}
				}
				$trans->commit();
				$this->dealSuccess( $this->createUrl('index',array( 'id'=>$model->departmentId ) ) );
			}else{
				$this->dealError( $model->getErrors() );
			}
		}


		showpage:

		$roles = tbRole::model()->findAll();
		$roleList = array();
		foreach($roles as $item) {
			$roleList[$item->roleId] = $item->roleName;
		}

		if(!$model->isNewRecord) {
			$group = tbRoleGroup::model()->findAllByAttributes(['deppositionId'=>$model->depPositionId,'state'=>0]);
			$selected = array_map(function($row){
				return $row->roleId;
			}, $group);
		}
		else {
			$selected = array();
		}

		$this->render( 'edit', array( 'positionName'=>$model->positionName,'departmentName'=>$department->departmentName, 'roles'=>$roleList, 'selected'=>$selected ) );
	}

	/**
	* @access 接口-根据部门ID查找职位
	*/
	public function actionGetposition(){
		$id = Yii::app()->request->getQuery('id');
		if( is_numeric($id) && $id >0 ){
			$data = $data = tbDepPosition::model()->findAll( 'state = 0 and departmentId = '.$id );
			$data = array_map(function ($i){
				return $i->getAttributes(array('depPositionId','positionName'));
			},$data);
			$json = new AjaxData(true,null,$data);
		}else{
			$json = new AjaxData(false);
		}
		echo $json->toJson();
		Yii::app()->end();
	}
}