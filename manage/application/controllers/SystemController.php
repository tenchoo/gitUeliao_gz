<?php
class SystemController extends Controller {

	public function actionIndex() {
		$variables = tbConfig::model()->findAll('type=:type',array(':type'=>'base'));
		$this->updateConfig( $variables );
		$this->render('variables', array('variables'=>$variables));
	}

	public function updateConfig( $variables ) {
		if( !Yii::app()->request->isPostRequest ) {
			return ;
		}

		set_time_limit(0);
		$configs = Yii::app()->request->getPost('config');

		foreach ( $variables as $val ){
			if(!isset( $configs[$val->key] )) continue;

			$val->scenario = $val->valueType;
			$val->value = $configs[$val->key];
			if( !$val->save() ){
				$error = $val->getErrors();
				$error['value']['0'] = $val->comment.' :'.$error['value']['0'];
				$this->setError($error );
				return ;
			}
		}
		Yii::app()->session->add('alertSuccess',true);
		$this->redirect('index');
	}

	/**
	* @access 客服列表
	*/
	public function actionCs(){
		$data = tbCS::model()->findAll();
		$data = array_map(function($i){
					$i->state = ($i->state)?'已启用':'已禁用';
					$i->type = ($i->type == '2')?'旺旺':'QQ';
					return $i->attributes;},$data);
		$isadd = ( count( $data ) <= tbCS::CS_MAX)?'1':'0';
		$this->render('cs', array('data'=>$data,'isadd'=>$isadd));
	}

	/**
	* @access 新增/编辑客服
	*/
	public function actionSetcs(){
		$id = Yii::app()->request->getQuery('id');
		if( $id =='' ){
			$model =  new tbCS();
		}else if( is_numeric($id) && $id>0 ){
			$model =  tbCS::model()->findByPk( $id );
		}

		if(!isset($model) || is_null($model)){
			$this->redirect( $this->createUrl( 'cs' ) );
		}

		if( Yii::app()->request->isPostRequest ) {
			$model->attributes = Yii::app()->request->getPost('data');
			if( $model->save() ){
				$this->dealSuccess( $this->createUrl( 'cs' ) );
			}else{
				$errors = $model->getErrors();
				$this->dealError( $errors );
			}
		}
		$this->render( 'setcs', $model->attributes );
	}

	public function actionDelcs(){
		$id = Yii::app()->request->getQuery('id');
		if( $id =='' ){
			$model =  new tbCS();
		}else if( is_numeric($id) && $id>0 ){
			tbCS::model()->deleteByPk( $id );
		}

		$this->dealSuccess( $this->createUrl( 'cs' ) );
	}
	
	
	/**
	* @access 打印列表
	*/
	public function actionPrinter(){
		$data = tbPrinter::model()->findAll( array( 'order'=>'state asc,printerSerial asc' ));
		$data = array_map(function($i){
					$i->state = ($i->state == '0')?'已启用':'已禁用';
					return $i->attributes;},$data);
		$this->render('printer', array('data'=>$data) );
	}

	/**
	* @access 新增/编辑客服
	*/
	public function actionSetprinter(){
		$id = Yii::app()->request->getQuery('id');
		if( $id =='' ){
			$model =  new tbPrinter();
		}else if( is_numeric($id) && $id>0 ){
			$model =  tbPrinter::model()->findByPk( $id );
		}

		if(!isset($model) || is_null($model)){
			$this->redirect( $this->createUrl( 'printer' ) );
			exit;
		}

		if( Yii::app()->request->isPostRequest ) {
			$model->attributes = Yii::app()->request->getPost('data');
			if( $model->save() ){
				$this->dealSuccess( $this->createUrl( 'printer' ) );
			}else{
				$errors = $model->getErrors();
				$this->dealError( $errors );
			}
		}
		$this->render( 'setprinter', $model->attributes );
	}

	/**
	* @access 访问日志
	*/
	public function actionOplog(){
		$c = Yii::app()->request->getQuery( 'c' );
		$collection = array('manage','home','member','mobile','app');

		$list = array();
		$nextId = $preId = null;
		if( in_array( $c ,$collection ) ){
			$list = $this->getOplog( $c,$nextId,$preId );
		}

		$this->render('oplog', array('list'=>$list,'nextId'=>$nextId,'preId'=>$preId,'collection'=>$collection,'c'=>$c));
	}

	/**
	* 取得访问日志
	*/
	private function getOplog( $c,&$nextId,&$preId ){
		$where = array();
		$issort = -1;

		$s = Yii::app()->request->getQuery( 's' );
		if( !empty( $s ) ){
			$s = explode( '_',$s );
			if( count($s) != 2 || empty($s['1'] ) ) return array();

			if( $s['0'] == 'pre' ){
				$where = array('_id'=>array('$gt'=>new MongoDB\BSON\ObjectID( $s['1'] ) ));
				$issort = 1;
			}else if( $s['0'] == 'next' ){
				$where = array('_id'=>array('$lt'=>new MongoDB\BSON\ObjectID( $s['1'] ) ));
			}else{
				return array();
			}
		}

		//按项目查找日志
		// $mongo = Yii::app()->mongoDB->getMongoDB();
		// $_db = $mongo->selectCollection( Yii::app()->mongoDB->dbname, 'viewlog_'.$c );
		$_db = Yii::app()->mongoDB->collection('viewlog_'.$c);

		$size = (int)tbConfig::model()->get('page_size');
		$cursor = $_db->find( $where, ['limit'=>$size, 'sort'=>array('viewTime'=>$issort)] );
		if(is_null($cursor))
			$cursor = array();
			
		$list = array();
		foreach ($cursor as $item) {
			$item->id = (string)$item->_id;
			$item->viewTime = date('Y-m-d H:i:s',$item->viewTime);
			$list[] = $item;
		}

		if( $issort === 1 ){
			//如果上一页条数小于$size,跳转到第一页
			if ( count( $list ) < $size ){
				$this->redirect( $this->createUrl( 'oplog',array( 'c'=>$c ) ) );
				exit;
			}
			krsort( $list );
		}

		if( empty ( $list ) ){
			return $list;
		}

		//首页没有上一页
		if( !empty( $s ) ){
			$pre = current($list);
			$preId = $pre->id;
		}

		if( count( $list ) == $size || $issort === 1 ){
			$end = end($list);
			$nextId = $end->id;
		}

		return $list;
	}

	/**
	* @access 在线统计
	*/
	public function actionOnline() {
		$type = Yii::app()->request->getQuery('type','user');
		$online = new Online();
		$options = array(
				'userTotal'=>$online->CountOnline('user'),
				'memberTotal'=>$online->CountOnline('member'),
				'Datalist'=>$online->online($type)
				);
		$this->render('online', $options);
	}

	public function actionKick() {
		$id = Yii::app()->request->getQuery('id');
		$session = Yii::app()->mongoDB->collection("session");
		if($session->delete(['key'=>$id])) {
			Yii::app()->session->add("alertSuccess",true);
			$this->redirect($this->createUrl('online',['type'=>Yii::app()->request->getQuery('type','user')]));
		}
		else {
			$this->setErrors([Yii::t("base","Faild kick user")]);
			$this->redirect($this->createUrl('online',['type'=>Yii::app()->request->getQuery('type','user')]));
		}
	}

}
