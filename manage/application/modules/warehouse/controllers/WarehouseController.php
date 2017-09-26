<?php
/**
 * 仓库中心
 * @access 仓库中心
 * @author liang
 * @package Controller
 * @version 0.1
 *
 */
class WarehouseController extends Controller {

	/**
	 * 仓库设置
	 * @access 仓库设置
	 */
	public function actionIndex() {
		$model = new tbWarehouseInfo();
		$data = $model->findAll( 'state = 0' );

		$areaIds = array_map( function($i){ return $i->areaId;},$data );
		$areaModel = tbArea::model()->findAllByPk( array_unique($areaIds) );
		$area = array();
		foreach ( $areaModel as $val ){
			$area[$val->areaId] = $val->title;
		}


		$types = $model->types();

		$printers    = tbPrinter::model()->getAll();
		$warehouseIds = array_map( function($i){ return $i->warehouseId; },$data );
		$c = new CDbCriteria;
		$c->compare( 'warehouseId', $warehouseIds);
		$c->compare( 'isDefault', '1');

		$wprinters = tbWarehousePrinter::model()->findAll( $c );
		$wps = array();
		foreach ( $wprinters as $val ){
			if( array_key_exists($val->printerId,$printers) ){
				$wps[$val->warehouseId] = $printers[$val->printerId];
			}
		}

		$list = array();
		foreach ( $data  as $val ){
			$val->type =  array_key_exists($val->type,$types)?$types[$val->type]:'';
			$val->areaId = array_key_exists($val->areaId,$area)?$area[$val->areaId]:'';
			$info = $val->attributes;
			$info['printer'] = array_key_exists($val->warehouseId,$wps)?$wps[$val->warehouseId]:'';
			$list[] = $info;
		}
		$this->render( 'index', array( 'list'=>$list,'types'=>$types ) );
	}


	/**
	 * 编辑仓库或者仓库区域
	 * @access 编辑仓库
	 * @throws CHttpException
	 */
	public function actionEdit() {
		$warehouseId = Yii::app()->request->getQuery('id');
		$model = $this->getWarehouseModel( $warehouseId );
		$this->saveWarehouse( $model );
	}

	/**
	* 新增仓库或者仓库区域
	* @access 新增仓库
	*/
	public function actionAdd(){
		$model = new tbWarehouseInfo();
		$this->saveWarehouse( $model );
	}

	private function getWarehouseModel( $warehouseId ){
		if( is_numeric( $warehouseId ) && $warehouseId >= '1' ){
			$model = tbWarehouseInfo::model()->findByPk( $warehouseId,'state = 0' );
			if( $model ) {
				return $model;
			}
		}

		$this->redirect( $this->createUrl( 'index' ) );
	}

	/**
	 * 删除仓库
	 * @access 删除仓库
	 */
	public function actionDel( $id ) {
		if( is_numeric($id) && $id>0 ){
			$hasChild = tbWarehousePosition::model()->countByAttributes(array('warehouseId'=>$id,'state'=>0));
			if( $hasChild ) {
				$ajax = new AjaxData(false,array('storage',Yii::t('category','Under this classification and information, please delete the information')));
				echo $ajax->toJson();
				Yii::app()->end();
			}else{
				$c = tbWarehouseInfo::model()->updateByPk( $id,array('state'=>1) );
			}
		}
		$this->dealSuccess( Yii::app()->request->urlReferrer );
	}

	/**
	* 保存仓库数据
	*/
	private function saveWarehouse( $model ){
		if( Yii::app()->request->isPostRequest ) {
			$model->title = Yii::app()->request->getPost('title');;
			$model->areaId = Yii::app()->request->getPost('areaId');
			$model->type = Yii::app()->request->getPost('type');
			if( empty($model->areaId) ){
				$model->areaId = '';
			}
			if( $model->save() ) {
				$this->dealSuccess( $this->createUrl('index') );
			}else{
				$this->dealError( $model->getErrors() );
			}
		}
		$data = $model->attributes;
		$data['types'] = $model->types();
		$this->render( 'edit',$data );
	}

	/**
	* 仓位/分区列表
	* @access 仓位/仓位列表
	*/
	public function actionPosition(){
		list( $paramName,$name,$id,$wid ) = $this->pinfo();

		$criteria = new CDbCriteria();
		if( $paramName == 'parentId' ){
			$criteria->compare ('parentId' , $id );
			$printers = array();
		}else{
			$criteria->compare ('warehouseId' , $id );
			$criteria->compare ('parentId' , '0' );

			$printers    = tbPrinter::model()->getAll();
		}

		$model = new tbWarehousePosition();
		$types = $model->areaTypes();
		$criteria->compare ('state' , '0' );
		$pages = new CPagination( $model->count($criteria) );
		$pages->setPageSize( (int) tbConfig::model()->get('page_size') );
		$pages->applyLimit( $criteria );

		$positions = $model->findAll( $criteria );
		$list = array();
		foreach( $positions as $val ){
			$val->type = array_key_exists( $val->type,$types )?$types[$val->type]:'';
			$val->printerId = array_key_exists( $val->printerId,$printers )?$printers[$val->printerId]:'';
			$list[] = $val->attributes;
		}
		$this->render( 'position', array('list' => $list,'pages'=>$pages, 'id'=>$id,'paramName'=>$paramName,'name'=>$name,'wid'=>$wid) );
	}


	private function pinfo( $param = array() ){
		$name = array('parentId'=>'仓位','warehouseId'=>'分区');
		foreach( $name as $key=>$val ){
			if( !empty($param) ){
				if( array_key_exists($key,$param) ){
					$id = $param[$key];
					break;
				}
			}else{
				$id = Yii::app()->request->getQuery( $key );
				if( is_numeric($id) && $id > 0 ){
					break;
				}
			}

		}

		if( is_null($id) || !is_numeric($id) ){
			throw new CHttpException( '404', 'Invalid id value' );
		}

		$title = array();
		if( $key == 'parentId' ){
			$zoning = tbWarehousePosition::model()->findByPk( $id ,'state=0' );
			if( is_null($zoning) ) {
				throw new CHttpException( '404', 'Not found zoning record' );
			}

			$warehouseId = $zoning->warehouseId;
			array_unshift($title,$zoning->title);
		}else{
			$warehouseId = $id;
		}

		$warehouse = $this->getWarehouseModel( $warehouseId );
		array_unshift($title,$warehouse->title);

		$this->pageTitle = implode('>',$title);
		return array( $key,$name[$key],$id,$warehouseId );
	}

	/**
	* 新增分区/仓位
	* @access 新增分区/仓位
	*/
	public function actionPadd(){
		list( $paramName,$name,$id,$warehouseId ) = $this->pinfo();
		$model = new tbWarehousePosition();
		$model->warehouseId = $warehouseId;
		if( $paramName == 'parentId' ){
			$model->parentId = $id;
		}
		$this->pageTitle .= '>新增'.$name;
		$this->savePosition( $model,$paramName,$name,$id );
	}

	private function savePosition( $model,$paramName,$name,$id ){
		if( $model->parentId == '0' ){
			$printers    = tbPrinter::model()->getAll();
			$types 		 = $model->areaTypes();
		}

		if( Yii::app()->request->isPostRequest ) {
			$model->title = Yii::app()->request->getPost('title');
			if( $model->parentId == '0' ){
				$model->type = Yii::app()->request->getPost('type');
				if( !array_key_exists( $model->type, $types )  ){
					$model->type = 1;
				}
				$model->printerId = Yii::app()->request->getPost('printerId');
				if( !array_key_exists( $model->printerId, $printers )  ){
					$model->printerId = null;
				}
			}
			if( $model->save() ) {
				$this->dealSuccess( $this->createUrl('position',array($paramName=>$id) ) );
			}else{
				$this->dealError( $model->getErrors() );
			}
		}

		$data = $model->attributes;
		$data['name'] = $name;

		if( $model->parentId == '0' ){
			$data['printers'] = $printers;
			$data['types'] = $types;
		}
		$this->render( 'pedit',$data );
	}



	/**
	* 编辑分区/仓位
	* @access 编辑分区/仓位
	*/
	public function actionPedit( $id ){
		$model = $this->getPosition( $id );
		if( $model->parentId>0 ){
			$param['parentId'] = $model->parentId;
		}else{
			$param['warehouseId'] = $model->warehouseId;
		}

		list( $paramName,$name,$fid,$warehouseId ) = $this->pinfo( $param );

		$this->pageTitle .= '>编辑'.$name;
		$this->savePosition( $model,$paramName,$name,$fid );
	}

	/**
	 * 删除分区/仓位信息
	 * @access 删除分区/仓位
	 */
	public function actionPdel() {
		$id = Yii::app()->request->getQuery('id');
		$model = new tbWarehousePosition();
		if( $model->pdel( $id ) ){
			$this->dealSuccess( Yii::app()->request->urlReferrer );
		}else{
			$this->dealError( $model->errors );
		}
	}


	/**
	 * 打印机设置--当前先设置默认打印机，只设置一个。扩展是一个仓库有多个打印机
	 * @access 打印机设置
	 */
	public function actionPrinter() {
		$warehouseId = Yii::app()->request->getQuery('warehouseId');
		$wModel = $this->getWarehouseModel( $warehouseId );
		$printers    = tbPrinter::model()->getAll();
		$model = tbWarehousePrinter::model()->find( 'warehouseId =:wid' ,array(':wid'=>$warehouseId ) );
		if( !$model ){
			$model = new tbWarehousePrinter();
			$model->warehouseId = $warehouseId;
			$model->isDefault = '1';
		}

		if( Yii::app()->request->isPostRequest ) {
			$printerId = Yii::app()->request->getPost('printerId');
			if( array_key_exists( $printerId, $printers )  ){
				$model->printerId = $printerId;
				if( $model->save() ) {
					$this->dealSuccess( $this->createUrl('printer',array('warehouseId'=>$warehouseId) ) );
				}else{
					$this->dealError( $model->getErrors() );
				}
			}
		}

		$this->render('printer', array('title'=>$wModel->title, 'printerId'=>$model->printerId,'printers'=>$printers));
	}

	/**
	 * 仓库管理员设置--当前先设置默认管理员，只设置一个。扩展是一个仓库有多个管理员
	 * @access 仓库管理员设置
	 */
	public function actionManager() {
		$warehouseId = Yii::app()->request->getQuery('warehouseId');
		$wModel = $this->getWarehouseModel( $warehouseId );

		$model = new WarehouseManager();
		$model->warehouseId = $warehouseId;

		if( Yii::app()->request->isPostRequest ) {
			$user = Yii::app()->request->getPost('user');
			if( $model->save( $user ) ) {
				$this->dealSuccess( $this->createUrl( 'manager' ,array('warehouseId'=>$warehouseId) ) );
			}else{
				$this->dealError( $model->getErrors() );
			}
		}

		$list = $model->getManages();
		$this->render('manager', array('title'=>$wModel->title, 'list'=>$list));
	}

	/**
	*
	* @access 分拣员设置
	*/
	public function actionUserlist() {
		$positionId = Yii::app()->request->getQuery( 'positionId' );

		//判断分区是否存在
		$model = $this->getPosition( $positionId );
		if( $model->parentId>0 ){
			$this->redirect( $this->createUrl('position',array('warehouseId'=>$model->warehouseId) ) );
		}

		$warehouseModel = $this->getWarehouseModel( $model->warehouseId );

		$this->pageTitle = $warehouseModel->title.' > '.$model->title.' > 分拣员';

		//取得分拣员列表
		$UserForm =  new WarehouseUserForm();
		$UserForm->warehouseId = $model->warehouseId;
		$UserForm->positionId = $model->positionId;

		//增加分拣员
		if( Yii::app()->request->isPostRequest ) {

			if( $UserForm->addPackingUser() ) {
				$this->dealSuccess( $this->createUrl( 'userlist',array('positionId'=>$positionId) ) );
			}else{
				$this->dealError( $UserForm->getErrors() );
			}
		}else{
			//删除分拣员
			$this->delPackingUser( $UserForm );
			$this->setMamage( $UserForm );
		}

		$data = $UserForm->getlist($positionId);
		$data['warehouseId'] = $model->warehouseId;
		$data['positionId'] = $model->positionId;

		$this->render( 'userlist',$data );
	}


	private function getPosition( $id ){
		if( is_numeric( $id ) && $id>0 ){
			$model = tbWarehousePosition::model()->findByPk( $id,'state=0' );
			if( $model ) {
				return $model;
			}
		}
		throw new CHttpException( '404', 'Invalid id value' );
	}

	/**
	* @access 删除分拣员
	*/
	private function delPackingUser( $model ){
		$op = Yii::app()->request->getQuery('op');
		$userId = Yii::app()->request->getQuery('id');
		if( $op !== 'del' || empty( $userId ) || !is_numeric( $userId ) ) return ;

		$model->delPackingUser( $userId );
		$this->dealSuccess(  Yii::app()->request->urlReferrer  );
	}

	/**
	* 归单管理---设置默认分拣负责人
	*/
	private function setMamage( $model ){
		$op = Yii::app()->request->getQuery('op');
		$userId = Yii::app()->request->getQuery('userId');
		if( $op !== 'setManger' || empty( $userId ) || !is_numeric( $userId ) ) return ;

		$model->setMamage( $userId );
		$this->dealSuccess(  Yii::app()->request->urlReferrer  );
	}
}