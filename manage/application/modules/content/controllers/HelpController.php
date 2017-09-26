<?php
/**
 * 帮助中心
 * @access 帮助中心
 * @author liang
 * @package Controller
 * @version 0.1
 *
 */
class HelpController extends Controller {

	/**
	 * 帮助管理
	 * @access 帮助管理
	 */
	public function actionIndex() {
		$id = Yii::app()->request->getQuery('categoryId');
		$condition['title'] =Yii::app()->request->getQuery('title');

		$category = tbHelpCategory::model()->getTree();
		if( $id!='' ){
			if( !isset($category[$id]) ){
				throw new CHttpException(404,'the require obj has not exists.');
			}

			if( $category[$id]['type'] =='1' ){ //单页
				$url = $this->createUrl('/content/helpcategory/setcontent',array('categoryId'=>$id));
				$this->redirect($url);
				exit;
			}

			$ids = array();
			if( isset( $category[$id]['childs'] ) ){
				$ids = array_map( function ($i){ return $i['categoryId'];},$category[$id]['childs']);
			}
			array_push($ids,$id);
			$condition['categoryId'] = $ids;
		}

		$pageSize = (int) tbConfig::model()->get( 'page_size' );
		$data  = tbHelp::search( $condition,$pageSize );

		foreach( $data['list'] as &$val ){
			$val['category'] = isset($category[$val['categoryId']])?$category[$val['categoryId']]['title']:'未分类';
		}

		$this->render( 'index' ,array('categoryId'=>$id,
									  'title'=>$condition['title'],
									  'list'=>$data['list'],
									  'pages'=>$data['pages'],
									  'category'=>$category,
									  ));
	}

	/**
	 * 发布帮助
	 * @access 发布帮助
	 */
	public function actionAdd() {
		$model = new tbHelp();
		$url = $this->createUrl('index');
		$this->saveData( $model, $url );
	}


	/**
	 * 编辑帮助
	 * @access 编辑帮助
	 * @throws CHttpException
	 */
	public function actionEdit() {
		$id = Yii::app()->request->getQuery('id');
		if( !is_numeric($id) || $id<1 || !$model = tbHelp::model()->findByPk( $id ,'state=0') ){
			throw new CHttpException(404,'the require obj has not exists.');
		}
		$url = $this->createUrl('index');
		$this->saveData( $model, $url );
	}


	/**
	* 保存数据
	*/
	private function saveData( $model, $url ){
		if( Yii::app()->request->isPostRequest ) {
			$data  =  Yii::app()->request->getPost('data');
			$model->attributes = $data;
			if( $model->save() ) {
				$this->dealSuccess( $url );
			}else{
				$errors = $model->getErrors();
				$this->dealError( $errors );
			}
		}

		$data = $model->attributes;
		$data['category'] = tbHelpCategory::getByParentId( 0 );
		$data['cid1'] = $model->categoryId;
		if( $model->categoryId && !isset($data['category'][$model->categoryId])){
			$cate2 = tbHelpCategory::model()->findByPk($model->categoryId,'state=0 and type=0');
			if( $cate2 ){
				$data['cid1'] =$cate2->parentId;
				$data['cate2'] = tbHelpCategory::getByParentId( $cate2->parentId );
				$data['cid2'] = $model->categoryId;
			}
		}
		$this->render( 'add',$data );
	}


	/**
	 * 帮助管理--删除,标删
	 * @access 删除帮助信息
	 */
	public function actionDel() {
		$id = Yii::app()->request->getQuery('id');
		$id = explode(',',$id);
		if( !empty($id) ){
			$set = array('state'=>'1','updateTime'=>date('Y-m-d H:i:s'));
			tbHelp::model()->updateByPk($id,$set);
		}
		$this->dealSuccess(Yii::app()->request->urlReferrer);
	}




}