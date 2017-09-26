<?php
/*
* @access 尾货产品
*/
class DefaultController extends Controller {

	/**
	* @access 尾货产品列表-销售中
	*/
	public function actionIndex() {
		$this->showList( 'selling','0',true );
	}

	/**
	* @access 尾货产品列表-已售完
	*/
	public function actionSoldout() {
		$this->showList( 'selling','1' );
	}

	/**
	* @access 尾货产品列表--仓库中
	*/
	public function actionStocklist() {
		$this->showList( 'underShelf' );
	}

	/**
	* @access 尾货产品列表-回收站
	*/
	public function actionRecycle() {
		$this->showList( 'del' );
	}

	/**
	* @access 尾货产品列表显示
	*/
	private function showList( $state ,$isSoldOut = '',$isAdd = false ){

		//按产品编号
		$singleNumber = trim(Yii::app()->request->getQuery('singleNumber'));

		//仓库列表
		$warehouse = tbWarehouseInfo::model()->getAll();
		$criteria = new CDbCriteria;
		if( !empty( $singleNumber ) ){
			$criteria->join = "inner join {{tail_single}} s on (s.singleNumber = '$singleNumber' and s.tailId = t.tailId )";
		}

		$criteria->compare( 't.state',$state );
		if( $state == 'selling' && in_array( $isSoldOut,array(0,1) ) ){
			$criteria->compare( 't.isSoldOut',$isSoldOut );
		}

		$pageSize = (int) tbConfig::model()->get( 'page_size' );
		$criteria->order  = 'createTime desc';
		$model = new CActiveDataProvider('tbTail', array(
			'criteria'=>$criteria,
			'pagination'=>array('pageSize'=>$pageSize,'pageVar'=>'page'),
		));

		$data = $model->getData();
		$pages = $model->getPagination();
		$list = array();

		if( !empty( $data ) ){
			$saleTypes = $data[0]->saleTypes();
			foreach ( $data as &$val ){
				$info = $val->attributes;
				$info['single'] = array();
				if( array_key_exists($val->saleType,$saleTypes) ){
					$info['saleType'] = $saleTypes[$val->saleType];
				}
				foreach ( $val->single as $_single ){
					$single = $_single->attributes;
					$single['stock'] = $_single->getStock();
					$info['single'][] = $single;
				}

				if($val['state'] == 'selling'){
					if( $val['isSoldOut'] =='1'){
						$info['buttons'] = array('del');
					}else{
						$info['buttons'] = array('edit','offshelf');
					}
				}else if($val['state'] =='underShelf'){
					$info['buttons'] = array('edit','onshelf','del');
				}else{
					$info['buttons'] = array('recycledel');
				}

				$list[] =  $info;
			}

		}

		$this->render( 'index',array('warehouse'=>$warehouse,'list'=>$list,'pages'=>$pages,'singleNumber'=>$singleNumber,'isAdd'=>$isAdd));
	}


	/**
	* @access 编辑尾货产品
	*/
	public function actionEdit( $id ){
		if( !is_numeric($id) || $id< 1 || !( $model = tbTail::model()->findByPk( $id ) ) ){
			$this->redirect( $this->createUrl('index') );
			exit;
		}

		$TailForm = new TailForm;
		if( Yii::app()->request->isPostRequest ) {
			$singles = Yii::app ()->request->getPost ( 'singleNumber' );
			$TailForm->attributes = Yii::app()->request->getPost('tail');
			if( $TailForm->edittail( $model,$singles ) ){
				$url = $this->createUrl( 'index' );
				$this->dealSuccess($url);
			}else{
				$errors = $TailForm->getErrors();
				$this->dealError( $errors );
			}
		}else{
			$TailForm->attributes = $model->getAttributes( array('saleType','price','tradePrice') );
		}

		$list = array();
		foreach ( $model->single  as $val ){
			$single = $val->attributes;
			$single['stock'] = $val->getStock();
			$list[] = $single;
		}

		//仓库列表
		$warehouse = tbWarehouseInfo::model()->getAll();

		$this->render( 'edit', array('list'=>$list,'warehouse'=>$warehouse,'tail'=>$TailForm->attributes) );
	}

	/**
	* 已经是尾货的产品不能在此搜索列表中出现,库存为0的也不能出现
	* @access 添加尾货
	*/
	public function actionAddtail(){
		//按产品编号
		$singleNumber = trim(Yii::app()->request->getQuery('singleNumber'));
		if( !empty( $singleNumber ) ){
			//查找出相关在售尾货产品
			$criteria = new CDbCriteria;
			$criteria->select = 't.singleNumber';
			$criteria->compare( 't.singleNumber',$singleNumber,true );
			$criteria->addCondition(" exists (select null from {{tail}} tail where tail.state = 'selling' and tail.tailId = t.tailId )");
			$tailSingles = tbTailSingle::model()->findAll( $criteria );
			$tailSingles = array_map( function($i){ return $i->singleNumber;},$tailSingles );

			$criteria = new CDbCriteria;
			$criteria->compare( 't.singleNumber',$singleNumber,true );

			//过滤在售的尾货产品
			$criteria->addNotInCondition('t.singleNumber', $tailSingles );


		//	$criteria->addCondition(" not exists ( select null from {{tail_single}} s where s.state = 0 and s.singleNumber = t.singleNumber and exists (select null from {{tail}} tail where tail.state = 'selling' and tail.tailId = s.tailId ))"); //直接一个SQL查询，改为分两部，第一步先查出在售尾货产品编号，第二步再过滤掉。减少长SQL
			$criteria->addCondition(" num > 0");
			$criteria->order = 'singleNumber ASC';

			$pageSize = (int) tbConfig::model()->get( 'page_size' );
			$model = new CActiveDataProvider('tbWarehouseCount', array(
				'criteria'=>$criteria,
				'pagination'=>array('pageSize'=>$pageSize,'pageVar'=>'page'),
			));

			$data = $model->getData();
			$pages = $model->getPagination();
			$list = array_map( function ($i){ return $i->attributes;},$data );
		}else{
			$list = array();
			$pages = new CPagination;
		}


		//仓库列表
		$warehouse = tbWarehouseInfo::model()->getAll();
		$this->render( 'addtail',array('warehouse'=>$warehouse,'list'=>$list,'pages'=>$pages,'singleNumber'=>$singleNumber));
	}

	/**
	* @access 产品转成尾货
	*/
	public function actionChangetail() {
		if( Yii::app()->request->isPostRequest ) {
			$singleNumber = Yii::app ()->request->getPost ( 'singleNumber' );
		}else{
			$singleNumber = Yii::app ()->request->getQuery ( 'singleNumber' );
		}

		if( empty($singleNumber) ){
			$this->redirect( $this->createUrl('addtail') );
			exit;
		}

		$c = new CDbCriteria;
		$c->addCondition(" num > 0");
		$c->compare('singleNumber',$singleNumber);
		$c->order  = 'singleNumber asc';
		$model = tbWarehouseCount::model()->findAll( $c );
		if( !$model ){
			$this->redirect( $this->createUrl('addtail') );
			exit;
		}

		$TailForm = new TailForm();
		$productId = array_unique( array_map( function($i){ return $i->productId;},$model ) );
		if( count($productId) !== 1 ){
			$this->setError( array( array(Yii::t('product','Different series of products cannot be turned into')) ) );
			goto showpage;
		}

		$tailData = Yii::app()->request->getPost('tail');
		if( !empty( $tailData ) && is_array( $tailData ) ) {
			$TailForm->attributes = $tailData;
			$singles = array_unique( array_map( function($i){ return $i->singleNumber;},$model ) );
			if( $TailForm->changetail( $productId['0'],$singles ) ){
				$url = $this->createUrl( 'index' );
				$this->dealSuccess($url);
			}else{
				$errors = $TailForm->getErrors();
				$this->dealError( $errors );
			}
		}

		showpage:
		$list = array();
		foreach ( $model as $val ){
			if( !array_key_exists( $val->singleNumber,$list ) ){
				$list[$val->singleNumber]['singleNumber'] = $val->singleNumber;
			}

			$list[$val->singleNumber]['stock'][] = $val->attributes;
		}

		//仓库列表
		$warehouse = tbWarehouseInfo::model()->getAll();
		$this->render( 'edit', array('list'=> array_values( $list ),'warehouse'=>$warehouse,'tail'=>$TailForm->attributes) );
	}

	/**
	* @access 尾货产品下架
	*/
	public function actionOffshelf( $id ){
		$this->changeState( $id,'underShelf','selling' );
	}

	/**
	* @access 尾货产品上架
	*/
	public function actionOnshelf( $id ){
		$this->changeState( $id,'selling','underShelf' );
	}

	/**
	* @access 尾货产品删除
	*/
	public function actionDel( $id ){
		if( is_numeric( $id ) && $id>0 ){
			$c = new CDbCriteria;
			$c->compare( 'tailId',$id );
			$c->compare( 'state',array('selling', 'underShelf') );
			$model = tbTail::model()->find( $c );
			if( $model && !( $model->state == 'selling' && $model->isSoldOut == '0' )  ){
				$model->state = 'del';
				if( $model->save()){
					$this->dealSuccess( Yii::app()->request->urlReferrer );
				}
			}
		}

		$this->dealError( array(array('delete failed')) );
	}

	/**
	* @access 尾货产品从回收站中删除
	*/
	public function actionRecycledel( $id ){
		$this->changeState( $id,'recycledel','del' );
	}



	/**
	* @access 更改尾货产品状态
	* @param integer $id 尾货产品ID
	* @param string $toState   尾货产品目标状态
	* @param string $fromState 尾货产品原状态
	*/
	private function changeState( $id,$toState,$fromState = '' ){
		$tail = new tbTail();
		$states = $tail->tailStates();
		if( is_numeric( $id ) && $id>0 && in_array($toState,$states) && in_array($fromState,$states) ){
			$n = $tail->updateByPk( $id,array( 'state'=>$toState ),'state = :state', array(':state'=>$fromState) );
			if( $n>0 ){
				$this->dealSuccess( Yii::app()->request->urlReferrer );

				if( $toState !='selling' ){
					//下架后把购物车的状态改为过期
					tbCart::model()->updateAll ( array('state'=>'1') ,' tailId = :tailId ',array(':tailId'=>$id) ); 

				}

			}
		}

		$this->dealError( array(array('operation failed')) );
	}
}