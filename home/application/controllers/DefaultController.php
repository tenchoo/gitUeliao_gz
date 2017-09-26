<?php
/*
* home
* @access home
*/
class DefaultController extends Controller {

	public $layout = '/layouts/home';

	/**
	 * 开启访问控制器
	 * @see CController::filters()
	 */
	public function filters() {
		return array();
	}

	public function init() {
		parent::init();
		$this->attachBehavior( 'zqh', 'application.models.ZRequestHelper' );
		$this->attachBehavior('searchHelper', 'libs.commons.models.searchHelper');
	}


	public function actionIndex(){
		$marks = array('index_product1','index_product2','index_product3');
		$model = tbRecommend::model()->findAllByAttributes( array('mark'=>$marks ,'state'=>'0','type'=>'1') );
		$data = array();
		foreach ( $model as $val ){
			$products = $val->getProducts();

			foreach ( $products as &$pval){
				if( array_key_exists('tailId',$pval) && $pval['tailId']>0 ){
					$pval['url'] = $this->createUrl('tailproduct/detail',array('id'=>$pval['tailId']));
				}else{
					$pval['url'] = $this->createUrl('product/detail',array('id'=>$pval['productId']));
				}
			}

			$data[] = array( 'title'=>$val->title,'products'=>$products  );
		}

		$this->render( 'home',array( 'data'=>$data ) );
	}

	public function actionProduct(){
		$this->showpage();
	}

	public function actionTail(){
		$this->showpage( 'tail' );

	}

	private function showpage( $productType = 'all' ){
		$params               = array();
		$categoryId           = Yii::app()->request->getQuery('c',0);
		$keyword              = Yii::app()->request->getQuery('q','');
		$params['current']    = tbCategory::model()->find( "categoryId=:id", array(':id'=>$categoryId) );
		$propertys            = $this->parseAttribute( Yii::app()->request->getQuery('ppath') );
		$filter = array_map(function($row){
			$keys = array_keys($row);
			return array_shift($keys);
		}, $propertys);
		$params['propertys']  = tbAttribute::model()->fetchAttributes($categoryId, $filter);		
		$params['selected']   = $this->propertyBox($propertys);

		$matches = $this->fetchProducts( $params['current'], $keyword, $propertys, $productType );
		$params['products']   = $matches->matches;

		$params['total'] 	  = $matches->total;
		$pager = new CPagination($matches->total);
		$pager->pageSize = Yii::app()->request->getQuery('limit', 20);
		$params['pages']      = $pager;

		$params['trace']      = $this->getTraceView();

		$this->render( 'index', $params );
	}


	public function actionProxy(){
		$this->renderPartial('proxy');
	}

	public function actionS() {
		return $this->actionIndex();
	}


	/**
	 * 创建产品属性链接
	 * @param array $property
	 */
	public function createPropertyUrl( $property ) {
		$id        = $property['attributeId'];
		$values    = $property['attrValue'];
		$params    = $this->parse_params( Yii::app()->request->url );
		if( !isset( $params['ppath'] ) || empty($params['ppath']) ) {
			$params['ppath'] = array();
		}
		else {
			$params['ppath'] = explode(';', $params['ppath']);
		}

		foreach( $values as $vid => $item ) {
			$srcPath = $params['ppath'];
			array_push( $srcPath, $id.':'.urlencode($vid) );
			$srcPath = implode(';', $srcPath);
			$srcQuery = $params;
			$srcQuery['ppath'] = $srcPath;

			$action = $this->action->id;
			$link = CHtml::link($item, $this->createUrl( $action ,$srcQuery));
			echo '<li>',$link,'</li>';
		}
	}


	//############################################
	//##      以下为对象私有方法，无法通过url访问             ##
	//############################################

	/**
	 * 提取及转换检索属性信息
	 * @param string $ppath
	 */
	private function parseAttribute( $ppath ) {
		$rows = array();

		if( is_null($ppath) || !$ppath || empty($ppath) ) {
			return $rows;
		}

		$ppath = explode(';', $ppath);
		$rows = array_map(function($item){
			list($pid,$vid) = explode(':', $item);
			return array($pid=>$vid);
		}, $ppath);
		return $rows;
	}

	/**
	 * 已选中属性沙盒
	 * @param array $propertys
	 * @return array
	 */
	private function propertyBox( $propertys ) {
		$urlParams = $this->parse_params( Yii::app()->request->url );		
		$links = array();
		if(!array_key_exists('ppath', $urlParams) || !$urlParams['ppath']) {
			return $links;
		}

		$ppath = explode(';', $urlParams['ppath']);

		for( $i=0; $i<count($ppath); $i++ ) {
			$params = $ppath;
			$header = $ppath[$i];
			unset( $params[$i] );
			$item = explode(':', $header);
			if(count($item)<2) {
				continue;
			}

			$attr = tbAttribute::model()->findByPk($item[0]);
			if(!$attr) {
				continue;
			}

			$key = tbAttr::model()->titleName($attr->attrId).':'.tbAttrValue::model()->getValue($item[1]);
			$query = $urlParams;
			$query['ppath'] = implode(';', $params);

			$url = $this->createUrl($this->getRoute(), $query);
			array_push($links, CHtml::link( $key.'<span class="arr"></span>', $url));
		}
		return $links;
	}

	private function getTraceView() {
		$cookie   = Yii::app()->request->cookies['view'];
		$products = array();
		if( $cookie ) {
			$searcher         = new SearchAPI;
			$searcher->setHost( Yii::app()->params['newsphinx'] ) ;
			$searcher->fields = "productid,title,picture,serial,saletype,price";
			$searcher->dict   = "product";
			$searcher->group  = "productid";
			$searcher->mode   = "all";

			$searcher->addFilter("filter","productid", str_replace(";",",",$cookie->value));

			$result = $searcher->query();

			if(is_null($result)) {
				return array();
			}

			$matches = $result->data->matches;
			$products = array_map(function($row){
				$record = new stdClass;
				$record->picture = $row->picture;
				$record->serial = $row->serial;
				$record->productId = $row->productid;
				$record->saletype = $row->saletype;
				return $record;
			}, $matches);
		}
		return $products;
	}
}