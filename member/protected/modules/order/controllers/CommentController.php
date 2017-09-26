<?php
class CommentController extends Controller {

	public $userType;

	public function init(){
		parent::init();
		$this->userType =  Yii::app()->user->getState('usertype');
	}

	/**
	* 客户反馈
	* @access 客户反馈
	*/
	public function actionIndex(){
		$uid = Yii::app()->user->id;
		$data = tbComment::model()->getList( array('t.memberId'=>$uid),10 );
		$this->render('index',array('list' => $data['list'],'pages'=>$data['pages'] ));
	}

	/**
	* 提交客户反馈
	* @access 提交客户反馈
	* @param integer id  订单ID
	*/
	public function actionAdd(){
		$id = Yii::app()->request->getQuery('orderId');
		$model = tbOrder::model()->with('products')->findByPk( $id ,'t.state = 6 and t.commentState = 0 ');
		if( !$model ){
			$url = $this->createUrl('index');
			$this->redirect($url);
			//throw new CHttpException(404,"the require obj has not exists.");
		}

		if( Yii::app()->request->isPostRequest ){
			$dataArr = Yii::app()->request->getPost('data');
			$Comment = new Comment();

			if( $Comment->save( $dataArr,$model ) ) {
				$this->dealSuccess( $this->createUrl( 'index' ) );
			} else {
				$errors = $Comment->getErrors();
				$this->dealError( $errors );
			}
		}

		$this->render('add',array('model' => $model ));
	}


	/**
	* 修改客户反馈
	* @access 修改客户反馈
	* @param integer id  反馈ID
	*/
	public function actionEdit(){
		$id = Yii::app()->request->getQuery('id');
		$model = tbComment::model()->with('product')->findByPk( $id );
		if( !$model ){
			$url = $this->createUrl('index');
			$this->redirect($url);
			//throw new CHttpException(404,"the require obj has not exists.");
		}
		if( Yii::app()->request->isPostRequest ){
			$model->content = Yii::app()->request->getPost('content');
			if( $model->save() ) {
				$this->dealSuccess( $this->createUrl( 'index' ) );
			} else {
				$errors = $model->getErrors();
				$this->dealError( $errors );
			}
		}
		$data = $model->attributes;
		$data['title'] = $model->product->title;
		$data['serialNumber'] = $model->product->serialNumber;
		$data['mainPic'] = $model->product->mainPic;
		$this->render('edit',array('data' => $data ));
	}

}