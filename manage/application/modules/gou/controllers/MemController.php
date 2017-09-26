<?php
/*
* @access 送货员管理
*/
class MemController extends Controller {

	//欢迎页面无需检察权限
    public function beforeAction($action) {
        return true;
    }

	/**
	* @access 送货员列表
	*/
	public function actionIndex() {
		$result['title'] = trim(Yii::app()->request->getQuery('title'));

		$criteria = new CDbCriteria;
		if( !empty( $result['title'] ) ){
			$criteria->compare( 't.title',$result['title'] );
		}

		$criteria->order  = 'deliverymanId desc';
		$model = new CActiveDataProvider('tbDeliveryman', array(
			'criteria'=>$criteria,
			'pagination'=>array('pageSize'=>20),
		));

		$data = $model->getData();
		$result['list'] = array_map( function($i){
			$info =  $i->attributes;
			$info['url'] = Yii::app()->request->hostInfo.$this->createUrl('/gou/d/index',array('id'=>$i['deliverymanId'],'c'=>$i->idMd5( $i['deliverymanId'] )));
			return $info;},$data);
		$result['pages'] = $model->getPagination();

		$this->render( 'index',$result );
	}

	/**
	* @access 新增送货员
	*/
	public function actionAdd(){
		$model = new tbDeliveryman();
		$this->addEdit( $model );
	}


	/**
	* @access 编辑送货员
	*/
	public function actionEdit( $id ){
		$model = tbDeliveryman::model()->findByPk( $id );
		if ( !$model ) {
			$this->redirect( $this->createUrl( 'index' ) );
		}
		$this->addEdit( $model );
	}

	/**
	* @access 保存送货员信息
	*/
	private function addEdit( $model ){
		if( Yii::app()->request->isPostRequest ) {
			$model->title = trim ( Yii::app()->request->getPost('title') );
			if( $model->save() ){
				$url = $this->createUrl( 'index' );
				$this->dealSuccess($url);
			}else{
				$errors = $model->getErrors();
				$this->dealError( $errors );
			}
		}

		$this->render( 'add',array( 'title'=>$model->title) );
	}


	/**
	* @access 删除送货员
	*/
	public function actionDel( $id ){
		if( is_numeric($id) && $id>0 ){
			tbDeliveryman::model()->deleteByPk( $id );
		}
		$this->dealSuccess( Yii::app()->request->urlReferrer );
	}
}