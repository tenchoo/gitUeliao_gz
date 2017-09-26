<?php
/**
 * 员工管理
 * @access 员工管理
 * @author yagas
 *
 */
class UserController extends Controller {

	/**
	 * 所有员工
	 * @access 所有员工
	 */
	public function actionIndex() {
		$perSize = tbConfig::model()->get( 'page_size' );
		$condition['departmentId'] = Yii::app()->request->getQuery('departmentId');
		$condition['username'] = trim(Yii::app()->request->getQuery('username'));

		$form = new UserForm();
		$data = $form->search( $condition,$perSize );
		$data['departments'] = tbDepartment::model()->getAll();
		$this->render( 'index', array_merge($condition,$data));
	}

	/**
	 * 编辑员工
	 * @access 编辑员工
	 * @throws CHttpException
	 */
	public function actionEdit() {
		$this->doOp();

		$id = Yii::app()->request->getQuery('id');
		$form = new UserForm('patch');
		if(Yii::app()->request->getIsPostRequest()) {
			$request = Yii::app()->request->getPost('form');
			if($request['updatePasswd']=='1') {
				$form = new UserForm('put');
			}
		}

		if(is_null($id) || !is_numeric($id) || !$form->setModel( $id )) {
			throw new CHttpException('404', 'Not found record');
		}

		$this->saveUser($form);
	}

	/**
	* 新增员工
	* @access 新增员工
	*/
	public function actionAdd(){
		$this->doOp();
		$form = new UserForm('create');
		$this->saveUser( $form );
	}

	private function doOp(){
		$op = Yii::app()->request->getQuery('op');
		$func = 'op_'.$op ;
		if( method_exists ( $this,$func ) ) {
			$this->$func ();
		}
	}

	/**
	* @access 接口-根据部门ID查找角色
	*/
	private function op_getroles(){
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

	/**
	* @access 接口-根据部门ID查找职位
	*/
	public function op_getposition(){
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

	/**
	* @access 查看员工信息
	*/
	public function actionView(){
		$this->actionEdit();
	}

	/**
	* 保存角色数据
	*/
	private function saveUser( $form ){
		if( Yii::app()->request->isPostRequest ) {
			$form->attributes = Yii::app()->request->getPost('form');
			if( $form->save() ) {
				Yii::app()->session->add('alertSuccess', True);
				$this->redirect($this->createUrl('index'));
			}else{
				$errors = $form->getErrors();
				$error = array_shift($errors);
				$this->setError(array($error[0]));
			}
		}
		$departments = tbDepartment::model()->getAll();

		$positions   = tbDepPosition::model()->getByDepId(  $form->departmentId );
		$roles       = tbRole::model()->getByDepId(  $form->departmentId );

		$printers    = tbPrinter::model()->findAll();
		$printerList = array();
		foreach($printers as $item) {
			$printerList[$item->printerId] = $item->mark;
		}

		$this->render('edit', array( 'data'=>$form->attributes,'departments'=>$departments,'positions'=>$positions,'roles'=>$roles, 'printers'=>$printerList ));
	}

	/**
	 * 冻结员工账户
	 * @access 冻结员工账户
	 */
	public function actionDel() {
		$id = Yii::app()->request->getQuery('id');
		if( is_numeric($id) && $id >1 ){
			tbUser::model()->updateByPk( $id,array('state'=>1) );
		}
		$this->dealSuccess( Yii::app()->request->urlReferrer );
	}

	/**
	 * 解冻员工账户
	 * @access 冻结员工账户
	 */
	public function actionThaw() {
		$id = Yii::app()->request->getQuery('id');
		if( is_numeric($id) && $id >1 ){
			tbUser::model()->updateByPk( $id,array('state'=>0) );
		}
		$this->dealSuccess( Yii::app()->request->urlReferrer );
	}

	/**
	 * 添加角色权限
	 * @access 分配角色权限
	 * @access 编辑角色权限
	 */
	public function actionAssign() {
		$done = Yii::app()->request->urlReferrer;
		if( !$done ) {
			$this->redirect( $this->createUrl('index') );
		}

		if( Yii::app()->request->isPostRequest ) {
			$taskIds     = Yii::app()->request->getPost('taskId');
			$redirectUrl = Yii::app()->request->getPost('done');
			$UserId      = Yii::app()->request->getPost('UserId');
			$result      = tbPermission::model()->assignPermission( $UserId, $taskIds );
			if( $result ) {
				$this->redirect( $redirectUrl );
			}
			Yii::app()->end( 200 );
		}

		$UserId         = Yii::app()->request->getQuery('id');
		$UserPermission = tbPermission::model()->readPermission( $UserId );
		$UserPermission = array_map(function($i){
			return $i['taskId'];
		}, $UserPermission);
		$tasks          = tbTask::model()->taskTree(0, $UserPermission );
		$this->render('permission', array('tasks'=>$tasks, 'done'=>$done, 'UserId'=>$UserId));
	}

	/**
	 * 遍历模块提取动作
	 * @access hidden
	 */
	public function actionFlushaction() {
		set_time_limit(0);
		$st = new ScanTask();
		$st->filters( array('ajax') );
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
				$taskController = tbTask::model()->find("taskRoute=:route",array(':route'=>$controller['controller']) );
				if( is_null($taskController) ) {
					$taskController = new tbTask();
				}
				$taskController->type      = 'controller';
				$taskController->taskName  = $controller['comment'];
				$taskController->taskRoute = $controller['controller'];
				$taskController->parentId  = $task->taskId;
				$taskController->save();

				foreach ( $controller['actions'] as $action ) {
					if( $action['comment'] == 'hidden' ) {
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
}