<?php
/**
 * 产品列表和产品详情
 * @author liang
 * @version 0.1
 * @package Controller
 */
class ProductController extends Controller {

	public function init() {
		parent::init();
// 		$this->initMemberInfo();
	}

	public function actionIndex(){
		$this->attachBehavior('searchHelper', 'libs.commons.models.searchHelper');

		$categoryId = Yii::app()->request->getQuery('categoryId',0);
		$ppath      = Yii::app()->request->getQuery('ppath');
		$propertys  = $this->parseAttribute($ppath);

		$category   = tbCategory::model()->findByPk( $categoryId );
		$keyword    = Yii::app()->request->getQuery('q','');
		// $result     = $this->fetchProducts($category, $keyword, $propertys);
		$result     = $this->fetchProducts($category, $keyword, []);
		
		$data = array();
		if( $result ) {
			foreach ( $result->matches  as &$val ){
				$new = new stdClass;
				$new->productId = $val->productid;
				$new->title     = '【'.$val->serial.'】'.$val->title;
				$new->price     = $val->price;
				$new->mainPic   = $val->picture;
				$new->unit      = tbUnit::getUnitByProduct($val->productid);
				$val = $new;
			}

			$data['page'] = (int)Yii::app()->request->getQuery( 'page', 1 ); //页码
			$data['total'] = $this->total;
			$total = $this->getSearchTotal();
			$data['totalpage'] = ceil($total/Yii::app()->params['default_page_size']);
			$data['list'] = array_values($result->matches);
		}

		$this->state = true;
		$this->data = $data;
		$this->showJson();
	}

	/**
	* 产品详情
	* @param integer $id 产品ID
	*/
	public function actionShow($id,$event='info'){
		$product = null;
		if( is_numeric($id) && $id>0 ){
			$product = tbProduct::model()->with('detail')->findByPk( $id );
		}

		//如果商品不存在
		if( !$product ) {
			$this->notFound();
		}

		//如果商品已下架
		if( !$product || $product->state != 0 ) {
			$this->message = '产品已下架';
			$this->showJson();
		}

		if( $product->type == tbProduct::TYPE_CRAFT ){
			$product->detail = tbProductDetail::model()->findByPk( $product->baseProductId );
		}

		$func  = "product_".$event;
		if( !method_exists($this, $func) ) {
			$this->notFound();
		}

		$this->data = call_user_func( array($this,$func), $product );

		$this->state = true;
		$this->showJson();
	}

	/**
	 * 颜色查找，产品详情页
	 * @param string $k 搜索关键词
	 */
	public function actionColor(){
		$k = Yii::app()->request->getParam("k");
		$model = tbSpecvalue::model()->search( $k );
		$data = array();
		if( $model ){
			foreach ( $model as $val ){
				$data[] = '[data-rel="'.$val->specId.':'.$val->specvalueId.'"]';;
			}
		}

		$this->data = implode(',',$data);
		$this->state = true;
		$this->showJson();
	}


	/**
	 * 产品详情
	 * @param CActiveRecord $product
	 * @return json
	 */
	private function product_info( $product ) {
		$data             = $product->getAttributes();
		$data['title'] = '【'.$product->serialNumber.'】'.$product->title;
		//计价单位
		$data['unit']	  = tbUnit::getUnitName( $product->unitId );

		$data['mainPic']  = $this->getImageUrl( $data['mainPic'], 600 );
		//销售总量
		$data['sells']    = tbOrderProduct::model()->dealCount( $product->productId );
		//客户反馈
		$data['feedback'] = tbComment::model()->count( 'productId = '.$product->productId);
		$data['pictures'] = $this->images_replace( $product->detail->pictures );
		return $data;
	}

	/**
	 * 产品图文详情
	 * @param CActiveRecord $product
	 * @return json
	 */
	private function product_detail( $product ) {
		if( $product->detail->phoneContent !='' ){
			$body = $product->detail->phoneContent;
		}else{
			$body = $product->detail->content;
		}
		$urlInfo = parse_url(Yii::app()->request->hostInfo);
		if( $urlInfo['scheme']==='http' ) {
			$body = str_replace('https://images.', 'http://images.', $body);
		}

		//加上HTML头尾。
		$data = $this->addHtml( $body );
		return $data;
	}

	private function addHtml( $body ){
		$html = '<!doctype html><html lang="zh-cn"><head><meta charset="utf-8"/>
			  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
			  <meta name="renderer" content="webkit|ie-stand|ie-comp">
			  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
			  <title></title><style>img{max-width:100%} </style><body>'.$body.'</body></html>';
		return $html;
	}


	/**
	 * 产品属性
	 * @param CActiveRecord $product
	 * @return json
	 */
	private function product_property( $product ) {
		$baseProductId = ( $product['type'] == tbProduct::TYPE_CRAFT )?$product['baseProductId']:$product->productId;
		$propertys = tbProductAttribute::model()->productAttribtes( $baseProductId );

		$property = array();
		$orther = array();
		foreach ($propertys as $val){
			if(!empty( $val['groupName'] )){
				$property[$val['setGroupId']]['title'] = $val['groupName'];

				$property[$val['setGroupId']]['childs'][] = array('title'=>$val['title'],'attrValue'=>$val['attrValue']);
			}else{
				$orther[] = array('title'=>$val['title'],'attrValue'=>$val['attrValue']);
			}
		}
		$property = array_values($property);
		if(!empty( $orther )){
			$property[] = array('title'=>'','childs'=>$orther);
		}
		$data['property'] = $property;

		//应APP端的要求，把申明和测试报告拼成一块，组成HTML代码返回。2016-03-25
		$testResults = $product->detail->testResults;
		$model = tbPiece::model()->findByAttributes( array('mark'=> 'product_m' ) ,'t.state=0 and t.parentId > 0');
		if( $model ){
			$testResults = $model->content.'<br>'.$testResults;
		}

		$data['testResults'] =  $this->addHtml( $testResults );
		return $data;
	}

	/**
	* 产品评论
	*/
	private function product_comment( $product ){
		$perpage = 8;
		$data['page'] = (int)Yii::app()->request->getQuery( 'page', 1 );
		$model = new tbComment();
		$data['total'] = (int)$model->count('productId = :productId and state = 0',array('productId'=>$product->productId));
		$data['totalpage'] = ceil($data['total']/$perpage);
		$data['list'] = $model->productComment( $product->productId,$data['page'],$perpage );
		foreach ( $data['list'] as &$val){
			if(!empty($val['icon'])){
				$val['icon'] = $this->getImageUrl( $val['icon'] ,128); //头像大小：128，256，512
			}
		}
		return $data;
	}

	/**
	 * 产品交易信息
	 * @param CActiveRecord $product
	 * @return json
	 */
	private function product_dealinfo( $product ) {
		$perpage = 8;
		$model = new tbOrderProduct();
		$data['page'] = (int)Yii::app()->request->getQuery( 'page', 1 );
		$data['total'] = (int)$model->dealCount( $product->productId );
		$total = $model->count( 'productId = '.$product->productId);
		$list = $model->dealList( $product->productId,$data['page'],$perpage);
		//查找产品单位
		$data['unit'] = tbUnit::getUnitName( $product->unitId );
		foreach ( $list as &$val){
			unset($val['price']);
			unset($val['specifiaction']);
			if(!empty($val['icon'])){
				$val['icon'] = $this->getImageUrl( $val['icon'],128 );
			}
			$val['unit'] = $data['unit'];
		}
		$data['totalpage'] = ceil($total /$perpage);
		$data['list'] = $list ;
		return $data;
	}

	/**
	 * 产品规格和库存
	 * @param CActiveRecord $product
	 * @return json
	 */
	private function product_spec( $product ) {
		$spec =  tbProductSpec::getSpec ( $product->productId );
		$specStock = tbProductStock::model()->specStock ( $product->productId );

		//颜色色系统
		$colorSeries = tbSetGroup::model()->getList( 2 );
		$colorgroups = array();
		foreach( $specStock as $val ){
			unset($val['safetyStock'],$val['productId'],$val['state']);

			$relation =json_decode( '{'.preg_replace('/(\w+):/is', '"$1":',  $val['relation']).'}',true);
			foreach ( $relation as $rel ){
				if( isset( $spec[$rel] ) ){
					$val = array_merge( $val,$spec[$rel]); //单规格时，多规格是不能这样写。
					unset($val['specId']);
					unset($val['specvalueId']);
				}
			}
			if( !empty( $val['picture'] ) ){
				$val['picture']  = Yii::app()->params['domain_images'].$val['picture'];
			}
			if( isset($val['colorSeriesId']) && isset($colorSeries[$val['colorSeriesId']]) ){
				//可销售量
				$val['total'] = ProductModel::total( $val['singleNumber'] );
				$val['total'] = Order::quantityFormat( $val['total'] );

				$colorgroups[$val['colorSeriesId']]['colorSeries'] = $colorSeries[$val['colorSeriesId']];
				$colorgroups[$val['colorSeriesId']]['childs'][] = $val;
			}else{
			//	$colorgroups[0]['colorSeries'] = '';
			//	$colorgroups[0]['childs'][] = $val;
			}
		}

		$data['spec'] = array_values($colorgroups);
		$data['title'] = '【'.$product->serialNumber.'】'.$product->title;
		$data['price'] = $product->price;
		$data['mainPic']  = $this->getImageUrl( $product->mainPic,200 );
		$data['unit'] = tbUnit::getUnitName( $product->unitId );
		return $data;
	}

	private function images_replace( $pictures ) {
		$images = json_decode( $pictures );
		$images = array_map( function($image){
			return $this->getImageUrl( $image, NULL ); //600
		}, $images );
		return $images;
	}

	/**
	 * 申明--产品详情页
	 */
	public function actionDeclare(){
		$model = tbPiece::model()->findByAttributes( array('mark'=> 'product_m' ) ,'t.state=0 and t.parentId > 0');
		if( $model ){
			$this->state = true;
			$this->data = $model->content;
		}

		$this->showJson();
	}




}