<?php
/**
 * 帮助类目
 * @access 帮助类目
 * @author liang
 * @package Controller
 * @version 0.1
 *
 */
class HelpcategoryController extends Controller {

	/**
	 * 帮助类目管理
	 * @access 帮助类目管理
	 */
	public function actionIndex() {
		//获取产品类目列表
		if( Yii::app()->request->getIsAjaxRequest() ) {
			$id = Yii::app()->request->getQuery('categoryId');
			$list = tbHelpCategory::getByParentId( $id );
			$data = new AjaxData( true, null, $list );
			echo $data->toJson();
			Yii::app()->end(200);
		}
		$list = tbHelpCategory::getByParentId( 0 );
		$this->render( 'index',array( 'categorys'=>$list ) );
	}


	/**
	* 取得类目内容，不包含单页,增加帮助时选择分类使用
	* @access 取得类目列表
	*/
	public function actionGetcategory(){
		if( Yii::app()->request->getIsAjaxRequest() ) {
			$id = Yii::app()->request->getQuery('parentId');
			$list = tbHelpCategory::getByParentId( $id ,false);
			$data = new AjaxData( true, null, $list );
			echo $data->toJson();
			Yii::app()->end(200);
		}

		$this->redirect( $this->createUrl('index' ));
		exit;
	}

	/**
	 * 帮助类目管理--编辑类目单页内容
	 * @access 编辑单页
	 */
	public function actionSetcontent(){
		$id = Yii::app()->request->getQuery('categoryId');
		if( !is_numeric($id) || $id<1 || !$model = tbHelpCategory::model()->findByPk( $id ,'state=0') ){
			throw new CHttpException(404,'the require obj has not exists.');
		}

		//列表，跳转到列表页面。
		if( $model->parentId>0 && $model->type == '0' ){
			$url = $this->createUrl('/content/help/index',array('categoryId'=>$id));
			$this->redirect($url);
			exit;
		}

		$content = tbHelpCategoryPage::model()->findByPk($model->categoryId);
		if( !$content ){
			$content = new tbHelpCategoryPage();
			$content->categoryId =  $model->categoryId;
		}

		if( Yii::app()->request->isPostRequest ) {
			$model->title =  Yii::app()->request->getPost('title');
			$content->content = Yii::app()->request->getPost('content');
			if( $model->save() && $content->save() ) {
				$this->dealSuccess( $this->createUrl('index') );
			}else{
				$errors = $model->getErrors();
				if(  empty($errors) ){
					$errors = $content->getErrors();
				}
				$this->dealError( $errors );
			}
		}

		$this->render( 'singlepage' ,array('title'=>$model->title,'content'=>$content->content) );
	}




	/**
	 * 帮助类目管理--编辑
	 * @access 编辑帮助类目
	 * @throws CHttpException
	 */
	public function actionEdit() {
		$form = Yii::app()->request->getPost('form');
		$model = tbHelpCategory::model()->findByPk( $form['categoryId'],'state=0'  );
		if( !$model ) {
			$error = new AjaxData(false,'Not found record');
			echo $error->toJson();
			Yii::app()->end(200);
		}
		$this->saveData( $model );
	}

	/**
	 * 帮助类目管理--新增
	 * @access 新增帮助类目
	 * @throws CHttpException
	 */
	public function actionAdd() {
		$model = new tbHelpCategory();
		$this->saveData( $model );
	}

	/**
	 * 帮助类目管理--删除,
	 * 如果是列表，删除前要先判断列表下是否有信息，如有信息，返回提示：列表下有信息，请先删除信息。
	 * @access 删除帮助类目
	 * @throws CHttpException
	 */
	public function actionDel() {
		$categoryId  =  Yii::app()->request->getPost('categoryId');
		$message = Yii::t('category','Delete failed');;
		$state = tbHelpCategory::model()->del ( $categoryId,$message );
		$json = new AjaxData($state,$message);
		echo $json->toJson();
		Yii::app()->end();
	}



	/**
	 * 帮助类目管理--移动顺序,
	 * @access 移动类目
	 * @throws CHttpException
	 */
	public function actionMove() {
		$form  =  Yii::app()->request->getPost('form');
		$state = tbHelpCategory::model()->changePosition( $form['categoryId'],$form['to'] );
		$json = new AjaxData( $state );
		echo $json->toJson();
		Yii::app()->end();
	}


	/**
	 * 帮助类目管理--更改页面类型
	 * @access 更改页面类型
	 * @throws CHttpException
	 */
	public function actionChangetype() {
		$categoryId  =  Yii::app()->request->getPost('categoryId');
		$type  =  Yii::app()->request->getPost('type');
		$message = Yii::t('category','Change type failed');
		$state = tbHelpCategory::model()->changetype( $categoryId, $type,$message );
		$json = new AjaxData($state,$message);
		echo $json->toJson();
		Yii::app()->end();
	}

	/**
	* 保存数据
	*/
	private function saveData( $model ){
		if( Yii::app()->request->isPostRequest ) {
			$form  =  Yii::app()->request->getPost('form');
			unset( $form['categoryId'] );

			$state = false;
			$message = null;

			//判断是否修改页面类型
			if( $model->categoryId && $form['type'] == '1' && $model->type == '0' ){
				//列表
				$count = tbHelp::model()->count( 'state=0 and categoryId = '.$model->categoryId );
				if($count){
					$message = Yii::t('category','This classification has help information and is not allowed to turn into a single page. Please delete or transfer the information to help the information to be transferred.');
					goto end;
				}
			}

			$model->attributes = $form;
			if( $model->save() ) {
				$state = true;
				$data = $model->getAttributes(array('categoryId','title','parentId','type'));
			}else{
				$error = $model->getErrors();
				$message = current($error);
				$message = isset($message['0'])?$message['0']:$message;
			}
			end:
			$data = $model->getAttributes(array('categoryId','title','parentId','type'));
			$json = new AjaxData($state,$message,$data);
			echo $json->toJson();
		}
		Yii::app()->end();
	}
}