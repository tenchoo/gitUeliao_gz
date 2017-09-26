<?php
class searchHelper extends CBehavior {

	public $total = 0;

	public $pager;

	public $sphinxLimit;


	/**
	 * 进行产品关键字检索
	 * @param array   $category 类目ID
	 * @param string  $keyword  检索关键字
	 * @param array   $property 类目属性
	 * @param string  $productType  产品类型：all 全部，normal 常规产品，tail 尾货产品
	 */
	public function fetchProducts( $category=null, $keyword='', $property=array(),$productType = 'normal' ) {
		Yii::log(Yii::app()->request->urlReferrer, CLogger::LEVEL_INFO, 'searchHelper::'.__FUNCTION__);
		Yii::log(Yii::app()->request->getRequestUri(), CLogger::LEVEL_INFO, 'URI');

		$offset = Yii::app()->request->getQuery("page", 0);
		if(!is_numeric($offset) || $offset<0) {
			$offset = 0;
		}

		$limit = Yii::app()->request->getQuery("limit", 20);
		if(!is_numeric($limit) || $limit<0) {
			$limit = 20;
		}

		if($offset>0) {
			$offset = ($offset-1) * $limit;
		}

		$searcher         = new SearchAPI;
		$searcher->setHost( Yii::app()->params['newsphinx'] ) ;
		$searcher->fields = "productid,title,picture,serial,saletype,price";
		$searcher->dict   = "product";
		$searcher->offset = $offset;
		$searcher->limit  = $limit;
		$searcher->group  = "productid";
		$searcher->mode   = "all";

		$order = Yii::app()->request->getQuery("order", 'rankingdo');
		if(in_array($order,['rankingdo','rankingup','pricedo','priceup','salesdo','salesup','publishdo','publishup'])) {
			$m = substr($order,-2);
			$f = substr($order,0,-2);
			$fieldDict = ['ranking'=>'ranking', 'price'=>'price', 'sales'=>'salevolume','publish'=>'saletime'];
			$sortDict  = ['do'=>'desc', 'up'=>'asc'];
			$searcher->sort = $fieldDict[$f].",".$sortDict[$m];
		}

		foreach($property as $item) {
			$value = array_values($item);
			$value = array_shift($value);
			$searcher->addFilter("filter", "attrvalue", $value);
		}

		switch ($productType) {
			case 'tail':
				$searcher->addFilter("filter", "saletype", "1,2");
				break;

			case 'normal':
				$searcher->addFilter("filter", "saletype", 0);
				break;
		}

		if($category instanceof CActiveRecord) {
			$searcher->addFilter("filterrange", "lft", $category->lft.':'.$category->rft);
		}

		if(!empty($keyword)) {
			$keyword = "*{$keyword}*";
		}

		$result = $searcher->query($keyword);
		if( is_null( $result )) return array();
		if($result->err == 0) {
			$matches = $result->data;
			$this->total = $matches->total;
			$this->pager = new CPagination($this->total);
			$this->pager->pageSize=$limit;

			if($matches->total>0) {
				$matches->matches = array_map(function($row){
					$row->price = sprintf('%.2f', $row->price/100);
					$row->picture = $this->ImageServ($row->picture, 200);
					return $row;
				}, $matches->matches);
			}
			return $matches;
		}
		return array();

		// phpinfo();
		// var_dump($result);exit;

		$dict   = "product";

		$indexDict = "product";
		$order     = strtolower( Yii::app()->request->getQuery('order','rankingdo') );
		$field     = substr( $order, 0, -2 );
		$sort      = substr( $order, -2 );

		if( !in_array($field, array('ranking','price','sales','publish')) || !in_array($sort, array('do','up')) ) {
			//记录日志
			Yii::log( Yii::t('loger','Invalid sort value'), CLogger::LEVEL_ERROR, __CLASS__ );
			throw CHttpException( 404 , 'not found page' );
		}

		$search = Yii::createComponent(array('class'=>'ZSphinxSearcher','host'=>Yii::app()->params['sphinx']['host'],'port'=>Yii::app()->params['sphinx']['port']));
		$search->init();
		if( !empty($keyword) ) {
			$keyword = sprintf( "@key *%s*", $keyword );
		}

		//检索算法
		$search->setMatchMode( SPH_MATCH_EXTENDED2 );

		//排序方式处理
		$sortField = sprintf("%s %s", $field, $sort=="up"? "asc" : "desc");
		$search->setGroupBy( "searchproduct", SPH_GROUPBY_ATTR, $sortField );

		switch ( $productType ){
			case 'normal':
				$search->setFilter( "productType", array('0') );
				break;
			case 'tail':
				$search->setFilter( "productType", array('1') );
				break;
			default:
				break;
		}


		//数据偏移量(翻页)
		$offset    = Yii::app()->request->getQuery( 'page', 0 );

		if( $this->sphinxLimit > 0 ){
			$pageSize     = $this->sphinxLimit;
		}else{
			$pageSize     = Yii::app()->params['default_page_size'];
		}

		if( $offset > 0 ) {
			--$offset;
		}
		$search->setLimits( $offset*$pageSize, $pageSize, 500, 1000 );

		//按产品类目筛选
		if( !is_null($category) ) {
			$categorys = tbCategory::model()->findByLeftRight( $category->lft, $category->rft );
			$ids = array_map(function($row){
						return $row['categoryId'];
					}, $categorys);

			if( $ids ) {
				$search->setFilter( "categoryid", $ids );
			}
		}

		//按产品属性进行筛选
		// if( $property ) {
		// 	var_dump($property);
		// 	$attributeIds    = array_keys( $property );
		// 	$attributeValues = array_values( $property );

		// 	// 			$search->setFilter( "attributeid", $attributeIds );
		// 	$keyword .= sprintf(" @attrvalue(%s)",implode('|', $attributeValues));
		// }
		// var_dump($property);

		$result = $search->query( $keyword, $indexDict );

		$this->total = $result['total'];
		$this->pager = new CPagination( $this->total );
		$this->pager->pageSize = $pageSize;

		if( $result['total'] <= 0 || !array_key_exists('matches',$result) ) {
			return array();
		}

		$list = $productIds = $tailIds  = array();
		foreach ( $result['matches'] as $val ){
			if( $val['attrs']['producttype'] == '1' ){
				$tailIds[] = $val['attrs']['productid'];
			}else{
				$productIds[] = $val['attrs']['productid'];
			}

			$list[] = array( 'id'=>$val['attrs']['productid'],'productType'=>$val['attrs']['producttype'] );
		}

		$tails = array();
		if( !empty( $tailIds ) ){
			$tailMap = tbTail::model()->findAllByPk( $tailIds );
			foreach ( $tailMap as $val ){
				$productIds[] = $val->productId;
				$tails[$val->tailId] = $val->getAttributes( array('tailId','productId','price','saleType') );
			}
		}

		$productMap = tbProduct::model()->searchAllByPk( array_unique( $productIds ) );
		$products = array();
		foreach ( $productMap as $row ){
			$row['mainPic'] = $this->imageServ( $row['mainPic'],200 );
			$products[$row['productId']] = $row;
		}

		foreach ( $list as &$row ){
			if( $row['productType']=='1' ){
				$row = array_merge($row,$products[$tails[$row['id']]['productId']],$tails[$row['id']]);
			}else{
				$row = array_merge($row,$products[$row['id']]);
			}
		}

		return $list;
	}

	/**
	 * 提取及转换检索属性信息
	 * @param string $ppath
	 */
	public function parseAttribute( $ppath ) {
		$rows = array();

		if( is_null($ppath) || !$ppath || empty($ppath) ) {
			return $rows;
		}

		$ppath = explode(';', $ppath);
		foreach( $ppath as $item ) {
			$row = explode(':', $item);
			$id  = array_shift( $row );
			if( is_numeric($id) && isset($row[0]) ) {
				$rows[$id] = $row[0];
			}
		}
		return $rows;
	}

	public function getSearchTotal() {
		return $this->total;
	}

	public function getSearchPager() {
		return $this->pager;
	}

	private function imageServ( $imageUrl,$size=100 ) {
		return Yii::app()->params['domain_images'] . $imageUrl . '_' . $size;
	}

	public function setSphinxLimit( $limit ){
		if( is_numeric( $limit ) && $limit >0 ){
			$this->sphinxLimit = $limit;
		}
	}
}

/**
 * 实现PHP与Sphinx(Python)接口请求的封装
 */
class SearchAPI {
  const HOST = "http://localhost:8130/sphinx/?";
  protected $options = [
    "dict"=>"kaiyigz", //搜索字典
    "fields"=>"",
    "group"=>"",
    "filter"=>[],
    "sort"=>"",
    "mode"=>"any",
    "offset"=>0,
    "limit"=>10
  ];
  protected $host;

  public function query($keyword="") {
    $options = $this->options;
    $options["query"] = $keyword;

    $filter = array_map(function($row){
        return "filter[]=".urlencode($row);
    }, $options["filter"]);
    $filter = implode('&', $filter);

    unset($options["filter"]);
    $params = http_build_query($options);
    $params .= "&$filter";

    $host = is_null($this->host)? self::HOST : $this->host;
    return $this->fetchUrl($host.$params);
  }

  public function __set($name, $value) {
    if(array_key_exists($name, $this->options)) {
      $this->options[$name] = $value;
      return true;
    }
    return false;
  }

  public function __get($name) {
    if(array_key_exists($name, $this->options)) {
      return $this->options[$name];
    }
    return null;
  }

  public function addFilter($type, $field, $filter) {
    $rule = sprintf("%s:%s:%s", $type, $field, $filter);
    array_push($this->options["filter"], $rule);
  }

  public function setHost($host) {
  	$this->host = $host;
  }

  protected function fetchUrl($url) {
	$parse = parse_url( $url );
	$ch    = curl_init( $url );
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_TIMEOUT, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);

	if( $parse['scheme'] == 'https' ) {
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
	}
	$result = curl_exec($ch);
	if( curl_errno($ch) === 0 ) {
		$result = json_decode($result);
	}
	else {
		$result = null;
	}
	curl_close($ch);
	return $result;
  }
}