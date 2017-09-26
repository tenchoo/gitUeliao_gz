<?php
/*
* 帮助中心
* @access 帮助中心
*/
class HelpController extends Controller {

	public $layout = '/layouts/help';

	public $menu;

	public $activeId;

	public $pageSize = 10;

	/**
	 * 开启访问控制器
	 * @see CController::filters()
	 */
	public function filters() {
		return array();
	}

	/**
	 * 设置访问权限
	 * @see CController::accessRules()
	 */
	public function accessRules() {
		return array();
	}


	public function init() {
		parent::init();
		$this->pageTitle = '帮助中心 - '.$this->pageTitle;
		$this->menu = tbHelpCategory::model()->getTree();
	}


	/*
	* @access 帮助中心
	*/
	public function actionIndex(){
		$data = tbHelp::search(array(),14);
		$i = ceil(count($data['list'])/2);
		$list[] = array_slice($data['list'],0, $i);
		$list[] = array_slice($data['list'],$i);
		$this->render( 'index' ,array('list'=>$list));
	}

	public function actionDetail(){
		$id = Yii::app()->request->getQuery('id');
		if( !is_numeric($id) || $id<1 || !$model = tbHelp::model()->findByPk( $id ,'state=0') ){
			throw new CHttpException(404,'the require help has not exists.');
		}

		$this->activeId = $model->categoryId;
		$tabs = array();
		$cid = $model->categoryId;
		while( $cid && isset( $this->menu[$cid] ) ){
			array_unshift($tabs,array('id'=>$cid,'title'=>$this->menu[$cid]['title']));
			$cid = $this->menu[$cid]['parentId'];
		}

		$this->render('detail',array( 'title'=>$model->title,
									  'content'=>$model->content,
									  'tabs'=>$tabs));
	}

	public function actionCategory() {
		$id = Yii::app()->request->getQuery('id');
		if( !isset( $this->menu[$id] ) ){
			throw new CHttpException(404,'the require help has not exists.');
		}
		$single = false; //列表OR单页
		$category = $this->menu[$id];
		$this->activeId = $id;

		if( $category['parentId'] == '0' ){
			//一级分类，先判断是否有子分类，有则跳转到第一个子分类页面
			if(isset($category['childs'])){
				$url = $this->createUrl('category',array('id'=>$category['childs']['0']['categoryId']));
				$this->redirect ( $url );
				exit;
			}

			$data  = tbHelp::search( array('categoryId'=>$id),$this->pageSize );
			if(empty($data['list'])){
				$single = true;//如果列表页为空，则显示单页。
			}
		}else{
			if($category['type'] == '1') {
				$single = true;
			}else{
				$data  = tbHelp::search( array('categoryId'=>$id),$this->pageSize );
			}
		}

		$tabs = array( array('id'=>$id,'title'=>$category['title']));
		$cid = $category['parentId'];
		while( $cid && isset( $this->menu[$cid] ) ){
			array_unshift($tabs,array('id'=>$cid,'title'=>$this->menu[$cid]['title']));
			$cid = $this->menu[$cid]['parentId'];
		}

		if( $single ){
			$content = tbHelpCategoryPage::model()->findByPk($id);
			$content = ($content)?$content->content:'';
			$this->render('detail',array( 'title'=>$category['title'],
									  'content'=>$content,
									  'tabs'=>$tabs));
			Yii::app()->end();
		}
		
		$this->render('list',array( 'list'=>$data['list'],
									  'pages'=>$data['pages'],
									  'tabs'=>$tabs));
		Yii::app()->end();








		//判断是否有子分类，有则转到第一个子分类页面。

		//对一级分类，无子分类，判断是否有列表，有列表则显示列表页，否则显示单页。


		//对于子分类，按页面类型显示列表页或单页。

		/* $this->render('detail',array( 'title'=>$model->title,
									  'content'=>$model->content,
									  'tabs'=>$tabs)); */
	}





}