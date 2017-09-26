<?php
/**
 * 广告管理
 * @access 广告管理
 * @author liang
 * @package Controller
 * @version 0.1
 *
 */
class AdvertisementController extends Controller {

	public $currentTitle;

	/**
	 * 广告管理
	 * @access 广告管理
	 */
	public function actionIndex() {
		if( Yii::app()->request->getIsAjaxRequest() ) {
			$id = Yii::app()->request->getQuery('adPositionId');
			$list = tbAdPosition::getByParentId( $id );
			$data = new AjaxData( true, null, $list );
			echo $data->toJson();
			Yii::app()->end(200);
		}
		$list = tbAdPosition::getByParentId( 0 );
		$this->render( 'index',array( 'list'=>$list ) );
	}


	/**
	 * 广告列表
	 * @access 广告列表
	 */
	public function actionAdlist() {
		$model = $this->getModel();
		$this->currentTitle = $model->page->title.'->'.$model->title;
		$pageSize = (int) tbConfig::model()->get( 'page_size' );
		$data = tbAd::getList( $model->adPositionId,$pageSize );
		$data['adPositionId'] = $model->adPositionId;
		$this->render( 'adlist',$data );
	}

	/**
	 * 编辑广告位
	 * @access 编辑广告位
	 * @throws CHttpException
	 */
	public function actionEditposition() {
		$model = $this->getModel();
		$model->scenario = 'editposition';
		if( Yii::app()->request->isPostRequest ) {
			$model->attributes = Yii::app()->request->getPost('data');
			if( $model->save() ) {
				$this->dealSuccess( $this->createUrl('index') );

			}else{
				$errors = $model->getErrors();
				$this->dealError( $errors );
			}
		}
		$this->render( 'editposition' ,$model->attributes );
	}

	/**
	* 查找广告位
	*
	*/
	private function getModel(){
		$id = Yii::app()->request->getQuery('adPositionId');

		if( is_numeric($id) && $id>1 ) {
			$model = tbAdPosition::model()->with('page')->findByPk( $id ,'t.state=0 and t.type = 1');
			if( $model ) return $model;
		}

		if( Yii::app()->request->getIsAjaxRequest() ) {
			$data = new AjaxData( false, 'Not found record' );
			echo $data->toJson();
			Yii::app()->end();
		}else{
			throw new CHttpException(404,'Not found record.');
		}
	}


	/**
	 * 广告位管理--编辑页面或广告位,树形编辑
	 * @access 编辑广告位/页面
	 * @throws CHttpException
	 */
	public function actionEdit() {
		$form = Yii::app()->request->getPost('form');
		$model = tbAdPosition::model()->findByPk( $form['adPositionId'],'state=0' );
		if( !$model ) {
			$error = new AjaxData(false,'Not found record');
			echo $error->toJson();
			Yii::app()->end(200);
		}
		$this->saveData( $model );
	}

	/**
	 * 广告位管理--新增
	 * @access 新增广告位
	 * @throws CHttpException
	 */
	public function actionAdd() {
		$model = new tbAdPosition();
		$this->saveData( $model );
	}

	/**
	 * 广告位管理--删除,
	 * @access 删除广告位
	 */
	public function actionDel() {
		$adPositionId  =  Yii::app()->request->getPost('adPositionId');
		$message = Yii::t('category','Delete failed');;
		$state = tbAdPosition::model()->del ( $adPositionId,$message );
		$json = new AjaxData($state,$message);
		echo $json->toJson();
		Yii::app()->end();
	}

	/**
	 * @access 删除广告
	 */
	public function actionDelad() {
		$id  =  Yii::app()->request->getQuery('id');
		$id = explode(',',$id);
		$message = Yii::t('category','Delete failed');
		$state = tbAd::model()->del( $id,$message );
		$json = new AjaxData($state,$message);
		echo $json->toJson();
		Yii::app()->end();
	}


	/**
	 * @access 广告下架
	 */
	public function actionOffshelf() {
		$ids  =  Yii::app()->request->getQuery('ids');
		$ids = explode(',',$id);
		$state = tbAd::model()->offShelf( $ids );
		$json = new AjaxData($state);
		echo $json->toJson();
		Yii::app()->end();
	}


	/**
	 * @access 新增广告
	 */
	public function actionAddad() {
		$position = $this->getModel();

		if(empty( $position->mark )){
			$this->redirect( $this->createUrl('editposition',array('adPositionId'=>$position->adPositionId) ));
			exit;
		}
		$model = new tbAd();
		$model->adPositionId = $position->adPositionId;
		$model->pageId = $position->parentId;

		$this->adSave( $model,$position );
	}

	/**
	 * @access 编辑广告
	 */
	public function actionEditad() {
		$id  =  Yii::app()->request->getQuery('id');
		if( !is_numeric($id) || $id<1 || !$model = tbAd::model()->findByPk( $id ,'state!=2') ) {
			throw new CHttpException(404,'Not found record.');
		}

		//提交编辑后，状态默认改为0
		$model->state = 0;
		$position = tbAdPosition::model()->with('page')->findByPk( $model->adPositionId );
		$this->adSave( $model,$position );
	}


	/**
	* 编辑保存广告内容
	*/
	private function adSave( $model,$position ){
		if( Yii::app()->request->isPostRequest ) {
			$model->attributes = Yii::app()->request->getPost('data');
			if( $model->startTime =='' ) $model->startTime ='0000-00-00 00:00:00';
			if( $model->endTime =='' )$model->endTime ='0000-00-00 00:00:00';

			if( $model->save() ) {
				$this->dealSuccess( $this->createUrl('adlist',array('adPositionId'=>$model->adPositionId)) );
			}else{
				$errors = $model->getErrors();
				$this->dealError( $errors );
			}
		}

		$data = $model->attributes;
		if( $data['startTime'] =='0000-00-00 00:00:00' ) $data['startTime'] ='';
		if( $data['endTime'] =='0000-00-00 00:00:00' ) $data['endTime'] ='';


		$data['pageTitle'] = $position->title;
		if( $position->page ){
			$data['pageTitle'] = $position->page->title .' > '.$data['pageTitle'];
		}
		$data['height'] = $position->height;
		$data['width'] = $position->width;
		$data['cycles'] = $model->cycles();
		$this->render( 'editad' , $data );
	}

	/**
	* 保存数据
	*/
	private function saveData( $model ){
		if( Yii::app()->request->isPostRequest ) {
			$form  =  Yii::app()->request->getPost('form');
			unset( $form['adPositionId'] );
			$model->attributes = $form;
			if( $model->save() ) {
				$state = true;
				$data = $model->getAttributes(array('adPositionId','title','parentId','type','maxNum','num','mark'));
			}else{
				$state = false;
				$data = $model->getErrors();
			}
			$json = new AjaxData($state,null,$data);
			echo $json->toJson();
		}
		Yii::app()->end(200);
	}


	/**
	 * 广告位生成JS,页面弹窗显示JS内容.
	 * @access 广告位生成JS
	 */
	public function actionCreatejs() {
		$model = $this->getModel();
		$memberApi =  new ApiClient('member','service');
		$ajaxUrl = $memberApi->getDomain().'/ajax?action=spread&';

		$data = array(
				'url1'=>$ajaxUrl.'id='.$model->adPositionId,
				'url2'=>$ajaxUrl.'mark='.$model->mark,
				'url3'=>$ajaxUrl.'id='.$model->adPositionId.'&adtype=2',
				'url4'=>$ajaxUrl.'mark='.$model->mark.'&adtype=2',
				'id'=>$model->adPositionId,
				'mark'=>$model->mark,
				);
		$data = new AjaxData( true, null,$data );
		echo $data->toJson();
		Yii::app()->end();

		$str = '<H2>js引用 json格式:</H2>';
		$url = $ajaxUrl.'id='.$model->adPositionId;
		$str .= htmlspecialchars('<script type="text/javascript" src="'.$url.'" charset="utf-8"></script>');
		$str .= '<p>或者用标识读取:</p>';
		$url = $ajaxUrl.'mark='.$model->mark;
		$str .= htmlspecialchars('<script type="text/javascript" src="'.$url.'" charset="utf-8"></script>');


		$str .= '<p><H2>js引用 document.write:</H2></p>';
		$url = $ajaxUrl.'id='.$model->adPositionId.'&adtype=2';
		$str .= htmlspecialchars('<script type="text/javascript" src="'.$url.'" charset="utf-8"></script>');
		$str .= '<p>或者用标识读取:</p>';
		$url = $ajaxUrl.'mark='.$model->mark.'&adtype=2';
		$str .= htmlspecialchars('<script type="text/javascript" src="'.$url.'" charset="utf-8"></script>');

		$str .= '<p></p><H2>PHP引用</H2>';


		$str .= htmlspecialchars('<?php $this->widget(\'widgets.AdWidget\', array(\'id\' => '.$model->adPositionId.'));?>');
		$str .= '<p>或者用标识读取:</p>';

		$str .= htmlspecialchars('<?php $this->widget(\'widgets.AdWidget\', array(\'mark\' =>\''.$model->mark.'\'));?>');



		$data = new AjaxData( true, null,$str );
		echo $data->toJson();
		Yii::app()->end();
	}
}