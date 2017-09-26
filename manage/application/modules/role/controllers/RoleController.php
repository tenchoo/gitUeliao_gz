<?php
/**
 * 角色管理及权限分配
 * @access 角色分配管理
 * @author yagas
 *
 */
class RoleController extends Controller {
	public $currentTitle;

	/**
	 * 角色列表
	 * @access 角色列表
	 */
	public function actionIndex() {
		$perSize = tbConfig::model()->get( 'page_size' );
		$condition['roleName'] = trim(Yii::app()->request->getQuery('roleName'));
		$data = tbRole::model()->search( $condition,$perSize );
		$data['departments'] = tbDepartment::model()->getAll();
		$this->render( 'list', array_merge($condition,$data));
	}

	/**
	 * 编辑角色
	 * @access 编辑角色
	 * @throws CHttpException
	 */
	public function actionEdit() {
		$id = Yii::app()->request->getQuery('id');
		if( $id <= 1 || !is_numeric($id) || ($model = tbRole::model()->findByPk( $id ))==null ) {
			$this->redirect( $this->createUrl('index') );
		}
		$this->saveRole( $model );
	}

	/**
	* 新增角色
	* @access 新增角色
	* @throws CHttpException
	*/
	public function actionAdd(){
		$model = new tbRole();
		$this->saveRole( $model );
	}

	/**
	 * 删除角色
	 * @access 删除角色
	 */
	public function actionDel() {
		$id = Yii::app()->request->getQuery('id');
		if( is_numeric($id) && $id >1 ){
			tbRole::model()->updateByPk( $id,array('state'=>1) );
		}
		$this->dealSuccess( Yii::app()->request->urlReferrer );
	}

	/**
	* 保存角色数据
	*/
	private function saveRole( $model ){
		if( Yii::app()->request->isPostRequest ) {
			$model->roleName = Yii::app()->request->getPost('roleName');
			$model->description = Yii::app()->request->getPost('description');
			//$model->departmentId = Yii::app()->request->getPost('departmentId');
			if( $model->save() ) {
				$this->dealSuccess( $this->createUrl('index') );
			}else{
				$this->dealError( $model->getErrors() );
			}
		}
		$departments = tbDepartment::model()->getAll();
		$this->render( 'edit', array( 'role'=>$model->attributes ,'departments'=>$departments ) );
	}

	/**
	 * 添加角色权限
	 * @access 分配角色权限
	 * @access 编辑角色权限
	 */
	public function actionAssign() {
		$roleId = Yii::app()->request->getQuery('id');
		$done = Yii::app()->request->urlReferrer;
		if( is_numeric($roleId) && $roleId >1 && !empty( $done ) ){
			$role = tbRole::model()->findByPk( $roleId,'state = 0' );
			if( !$role ){
				$this->redirect( $this->createUrl('index') );
			}
		}else{
			$this->redirect( $this->createUrl('index') );
		}

		$this->currentTitle = $role->roleName;

		$menus = tbSysmenu::model()->findAllByAttributes(['type'=>tbSysmenu::TYPE_NAVIGATE,'hidden'=>0]);
		foreach($menus as & $navigate) {
			$navigate->childrens = $navigate->findAllByNavigate($navigate->id);
		}

		if( Yii::app()->request->isPostRequest ) {
			$taskIds     = Yii::app()->request->getPost('menuId');
			$done = $redirectUrl = Yii::app()->request->getPost('done');

			$trans = Yii::app()->db->beginTransaction();
			//删除原有权限，重新写入新的权限
			tbPermission::model()->deleteAllByAttributes(['roleId'=>$roleId]);
			if(is_null($taskIds) || tbPermission::model()->assignPermission($roleId,$taskIds)) {
				$trans->commit();
				Yii::app()->session->add('alertSuccess',true);
				$this->redirect( $redirectUrl );
				Yii::app()->end();
			}
			$trans->rollback();
			$this->setError(['permission'=>Yii::t('base','An error occurred while trying to set the role authorization, please try again')]);
		}

		$this->render('permission', array('menus'=>$menus, 'done'=>$done, 'roleId'=>$roleId, 'roleId'=>$roleId));
	}

	/**
	 * 遍历模块提取动作
	 * @access hidden
	 */
	public function actionFlushaction() {
		set_time_limit(0);
		$st = new ScanTask();
		$st->filters( array('ajax','api','rest','push') );
		$st->basePath( Yii::getPathOfAlias('application.modules') );
		$maps = $st->execute();

		foreach( $maps as $item ) {
			$task = tbTask::model()->find("taskRoute=:route",array(':route'=>$item['route']) );
			if( is_null($task) ) {
				$task = new tbTask();
			}
			$task->type      = 'module';
			$task->taskName  = $item['comment'];
			$task->taskRoute = $item['route'];
			$task->parentId  = 0;
			$task->save();

			foreach ( $item['controllers'] as $controller ) {
				$title = trim($controller['comment']);
				if( strtolower($title)=='hidden' ) {
					continue;
				}

				$taskController = tbTask::model()->find("taskRoute=:route",array(':route'=>$controller['controller']) );
				if( is_null($taskController) ) {
					$taskController = new tbTask();
				}
				$taskController->type      = 'controller';
				$taskController->taskName  = $title;
				$taskController->taskRoute = $controller['controller'];
				$taskController->parentId  = $task->taskId;
				$taskController->save();

				foreach ( $controller['actions'] as $action ) {
					$title = trim($action['comment']);
					if( strtolower($title) == 'hidden' ) {
						continue;
					}

					$taskAction = tbTask::model()->find( 'taskRoute=:route', array(':route'=>$action['route']) );
					if( is_null($taskAction) ) {
						$taskAction = new tbTask();
					}

					$taskAction->type      = 'action';
					$taskAction->taskName  = $action['comment'];
					$taskAction->taskRoute = $action['route'];
					$taskAction->parentId  = $taskController->taskId;
					$taskAction->save();
				}
			}
		}
		$done = Yii::app()->request->urlReferrer;
		if( !$done ) {
			$done = 'index';
		}

		$this->redirect( $done );
	}


	/**
	* @access 接口-根据部门ID查找角色
	*/
	public function actionGetroles(){
		$id = Yii::app()->request->getQuery('id');
		if( is_numeric($id) && $id >0 ){
			$data = $data = tbRole::model()->findAll( 'state = 0 and departmentId = '.$id );
			$data = array_map(function ($i){
				return $i->getAttributes(array('roleId','roleName'));
			},$data);
			$json = new AjaxData(true,null,$data);
		}else{
			$json = new AjaxData(false);
		}
		echo $json->toJson();
		Yii::app()->end();
	}
}