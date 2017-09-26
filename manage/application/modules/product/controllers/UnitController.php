<?php
/*
* @access 单位管理
*/
class UnitController extends Controller {

	/**
	* @access 单位列表
	*/
	public function actionIndex() {
		$result['unitName'] = trim(Yii::app()->request->getQuery('unitName'));

		$criteria = new CDbCriteria;
		if( !empty( $result['unitName'] ) ){
			$criteria->compare( 't.unitName',$result['unitName'] );
		}

		$pageSize = (int) tbConfig::model()->get( 'page_size' );
		$criteria->order  = 'unitId desc';
		$model = new CActiveDataProvider('tbUnit', array(
			'criteria'=>$criteria,
			'pagination'=>array('pageSize'=>$pageSize),
		));

		$data = $model->getData();
		$result['list'] = array_map( function($i){return $i->attributes;},$data);
		$result['pages'] = $model->getPagination();

		$this->render( 'index',$result );
	}

	/**
	* @access 新增单位
	*/
	public function actionAdd(){
		$model = new tbUnit();
		$this->addEdit( $model );
	}


	/**
	* @access 编辑单位
	*/
	public function actionEdit( $id ){
		$model = tbUnit::model()->findByPk( $id );
		if ( !$model ) {
			throw new CHttpException(404,"the require Craft has not exists.");
		}
		$this->addEdit( $model );
	}

	/**
	* @access 保存单位信息
	* 编辑时：如果当前分类已有子分类，则是否有子分类的值不能修改为0
	* 已有子分类时，编号更改要同步更新子分类的上级编号
	*/
	private function addEdit( $model ){
		if( Yii::app()->request->isPostRequest ) {
			$model->unitName = trim ( Yii::app()->request->getPost('unitName') );
			if( $model->save() ){
				$url = $this->createUrl( 'index' );
				$this->dealSuccess($url);
			}else{
				$errors = $model->getErrors();
				$this->dealError( $errors );
			}
		}

		$this->render( 'add',array( 'unitName'=>$model->unitName) );
	}


	/**
	* @access 删除单位
	* 有子分类不允许删除
	*/
	public function actionDel( $id ){
		if( is_numeric($id) && $id>0 ){
			tbUnit::model()->deleteByPk( $id );
		}
		$this->dealSuccess( Yii::app()->request->urlReferrer );
	}
}