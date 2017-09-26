<?php
/**
* 购物车
* @version 0.1
* @package CFormModel
*/
class Cart extends CFormModel {

	/**
	* 用户类型
	*/
	private $userType;


	/**
	* 当前用户 memberId
	*/
	public $memberId;

	public $productId;

	public $cart;

	public $spec = array();

	public $totalItems = 0;

	public $totalPrice = 0;


	function __construct( $memberId,$userType) {
		parent::__construct();
		$this->memberId = $memberId;
		$this->userType = $userType;
	}

	public function rules()	{
		return array(
			array('productId,cart', 'required'),
			array('productId', "numerical","integerOnly"=>true,'min'=>'1'),
			array('cart','checkCart'),
			array('cart','safe'),
		);
	}

	/**
	* 验证码 rule 规则,检查购物车数量，只能为数字并只能带一个小数。
	*/
	public function checkCart($attribute,$params){
		if( $this->hasErrors() ) return false;

		if( !is_array( $this->cart ) ){
			$this->addError( 'cart',Yii::t('order','Purchase quantity must be submitted') );
			return false;
		}

		foreach( $this->cart as $key => $val ){
			if( !$this->checkNum( $val ) ){
				$this->addError( 'cart',Yii::t('order','Purchase quantity must be an integer greater than 0 or with a decimal point') );
				return false;
			}
		}
	}

	/**
	* 检查购买数量是否合法，购买数量必须为大于0的数字，最多只能带一位小数点。
	* @param integer $num 购买数量
	*/
	public function checkNum( $num ){
		if( preg_match('/^\d+(\.[0-9])?$/',$num) && $num!='0' ){
			return true;
		}
		return false;
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签
	*/
	public function attributeLabels() {
		return array(
			'productId' => '产品编号',
			'cart' => '产品规格和数量',
		);
	}

	/**
	* 删除购物车产品
	* @param integer/array $cartId 要删除的购物车ID
	*/
	public function delete( $cartId ){
		if(empty($cartId)){
			return ;
		}

		$criteria = new CDbCriteria;
		$criteria->compare('memberId', $this->memberId );
		$criteria->compare('cartId', $cartId);

		return tbCart::model()->deleteAll( $criteria ) ;
	}

	/**
	* 更改购物车产品数量
	* @param integer $cartId 要更改的购物车ID
	* @param integer $num 更改后的数量
	*/
	public function updateNum( $cartId,$num ){
		if( !is_numeric($cartId) || !$this->checkNum( $num ) ){
			return ;
		}
		$attributes = array( 'num'=>$num );
		$condition = 'memberId = :memberId';
		$params = array ( ':memberId'=>$this->memberId );
		return tbCart::model()->updateByPk( $cartId ,$attributes,$condition, $params ) ;
	}

	/**
	* 取得购物车产品信息
	*/
	public function getCarts( array $cartIds = array() ){
		$data = $this->cartProducts( $cartIds );
		$this->spec = array();
		array_walk($data,array($this,'toArrayRelation'));
		return $data;
	}

	/**
	* 取得购物车产品信息,并拆分成现货和订货
	* 订购量大于现有库存量-安全库存量则进入订货
	*/
	public function getConfirms( array $cartIds = array() ){
		$data = $this->cartProducts( $cartIds );
		$this->spec = array();
		$result = array();
		foreach ( $data as &$val ){
			$val['relation'] = json_decode( '{'.preg_replace('/(\w+):/is', '"$1":',  $val['relation']).'}',true);
			if( $val['state']=='0' && !empty( $val['relation'] ) ){
				$this->spec =  array_merge( $this->spec,$val['relation'] );
			}
		}
		$specs = $this->getSpecs();
		foreach ( $data as &$val ){
			//可销售量
			$val['total'] = ProductModel::total( $val['singleNumber'] );
			$relation = '';
			foreach ( $val['relation'] as $reval ){
				if(isset($specs[$reval])){
					$relation .= $specs[$reval]['specName'].': '.$specs[$reval]['title'].' '.$specs[$reval]['serialNumber'];
				}
			}
			$val['relation'] = $relation;

			if( $val['relation'] =='' )  continue;

			if( $val['num'] > $val['total'] ) {
				$key = '1';
			}else{
				$key = '0';
			}

			$result[$key]['list'][] = $val;
			if( !isset( $result[$key]['totalItems'] ) ){
				$result[$key]['totalItems'] = 0;
			}
			$result[$key]['totalItems']  += $val['num'];
		}


		return $this-> modileData( $result );
	}

	/**
	* 手机端组装数据
	*
	*/
	public function modileData( $result ){
		if( empty( $result ) ){
			return;
		}



		$order = new Order();
		$deliveryMethod = $order->deliveryMethod();
		foreach ( $deliveryMethod as $key=>&$val){
			$val = array ('id'=>$key,'title'=>$val);
		}
		reset($deliveryMethod);
		$data = array(	'keeyday'=>self::getKeeyday(),  //留货天数
						'userType'=> $this->userType,
						'totalPayment'=>0,
						'inttotalPayment'=>0,
						'deliveryMethod'=>array_values( $deliveryMethod ),
						'defaultMethod'=>current($deliveryMethod),
						'readyList'=>array('flag'=>false),
						'bookingList'=>array('flag'=>false),

						);

		$priceType = 0;
		if( $this->userType == tbMember::UTYPE_MEMBER ){
			$priceType = (int)tbMember::model()->getPriceType( $this->memberId );
		}

		if(isset( $result['0']['list'] )){
			$data['readyList'] = $this->assembledData( $result['0']['list'] ,$priceType );
			$data['totalPayment'] += $data['readyList']['total'] ;
		}

		if(isset( $result['1']['list'] )){
			$data['bookingList'] = $this->assembledData( $result['1']['list'] ,$priceType );
			$data['totalPayment'] += $data['bookingList']['total'] ;
		}
		$data['inttotalPayment']  = $data['totalPayment'] *100;
		$data['totalPayment']  =  Order::priceFormat($data['totalPayment'],'');

		return $data;

	}

	/**
	* 手机端组装数据2
	*/
	private function assembledData( $list ,$priceType ){
		if( empty($list) ) return array();
		$products = $count = array();
		$unit = Yii::app()->cache->get('unit');
		foreach ( $list as $val){
			if( !array_key_exists( $val['unitId'],$unit ) ){
				$unit[$val['unitId']] = tbUnit::getUnitName( $val['unitId']);
				Yii::app()->cache->set('unit',$unit,3600*24);
			}
			$price = ($priceType)?$val['tradePrice']:$val['price'];
			$total = $count[] = $price*$val['num'];
			if( !array_key_exists( $val['productId'],$products ) ){
			$products[$val['productId']]  = array(
							'productId'=>$val['productId'],
							'title'=>'【'.$val['serialNumber'].'】'.$val['title'],
							'mainPic'=> Yii::app()->params['domain_images'] . $val['mainPic'] . '_200',
							'price'=>$price,
							'intprice'=>$price*100,
							'unit'=>$unit[$val['unitId']],
							'totalPrice'=> 0,
							'intTotalprice'=> 0,

						);
			}
			$products[$val['productId']]['details'][] = array(
				'cartId'=>$val['cartId'],
				'stockId'=>$val['stockId'],
				'singleNumber'=>$val['singleNumber'],
				'relation'=>$val['relation'],
				'num'=>$val['num'],
				'totalprice'=> Order::priceFormat($total,''),
				'intTotalprice'=>$total*100,
			);
			$products[$val['productId']]['totalPrice']  += $total;
		}

		$return['flag'] = true;
		$return['total'] = Order::priceFormat(array_sum($count),'');
		$return['inttotal'] = $return['total']*100;
		$return['list'] = array_values(array_map(function ( $i ){
							$i['intTotalprice'] = $i['totalPrice']*100;
							$i['totalPrice'] = Order::priceFormat($i['totalPrice'],'');
							return $i;
						} ,$products));
		return $return;
	}



	/**
	* 取得系统设置中配置的留货天数
	*/
	public static function getKeeyday(){
		return tbConfig::model()->get( 'order_keep_time' );
	}

	/**
	* 从数据库中查询购物车产品信息
	* @param array 指定的购物车IDS
	*/
	private function cartProducts( array $cartIds = array() ){

		$where = ' c.memberId = '.$this->memberId ;
		if( !empty ( $cartIds ) ){
			$where .= ' and c.cartId in ( '. implode(',',$cartIds ).' ) '  ;
		}

		$sql = "SELECT c.*,p.state,p.price,p.unitId,p.tradePrice,p.title,p.serialNumber,p.mainPic,s.safetyStock,s.relation,s.singleNumber from {{cart}} c left join {{product}} p on c.productId = p.productId left join {{product_stock}} s on c.stockId=s.stockId where $where";
		$cmd = Yii::app()->db->createCommand( $sql );
		return $cmd->queryAll();
	}

	/**
	* 取得购物车产品的规格信息
	*/
	public function getSpecs(){
		return tbSpecvalue::getSpecs( $this->spec );

	}

	/**
	* 把产品规格转成数组，并计算总件数和总价格
	*/
	public function toArrayRelation(&$val,$key){
		if(!empty( $val['relation'] )){
			$val['relation'] = json_decode( '{'.preg_replace('/(\w+):/is', '"$1":',  $val['relation']).'}',true);
			$this->spec =  array_merge( $this->spec,$val['relation'] );
			// $this->totalItems += $val['num'];
			// $this->totalPrice += $val['num']*$val['price'];
		}
	}

	/**
	* 加入购物车
	*/
	public function save(){
		if( !$this->validate() ) {
			return false ;
		}

		//判断此产品是否存在
		$f = tbProduct::model()->exists('productId = :pid and state = 0 ',array(':pid'=>$this->productId));
		if( !$f ){
			$this->addError( 'productId','产品不存在或已下架' );
			return false;
		}

		//判断此产品的规格是否存在
		$st = tbProductStock::specStock ( $this->productId, array_keys($this->cart) );
		$st = array_map( function ($i){ return $i['stockId'];},$st);
		$nofound = array_diff(array_keys($this->cart),$st);
		if(!empty($nofound)){
			$this->addError( 'productId','产品不存在id为'.current($nofound).'的单品规格' );
			return false;
		}

		//判断此产品的规格是否存在
		$st = tbProductStock::specStock ( $this->productId, array_keys($this->cart) );
		$st = array_map( function ($i){ return $i['stockId'];},$st);
		$nofound = array_diff(array_keys($this->cart),$st);
		if(!empty($nofound)){
			$this->addError( 'productId','产品不存在id为'.current($nofound).'的单品规格' );
			return false;
		}

		$model = new tbCart();
		$model->productId = $this->productId ;

		//客户登录
		$model->memberId = $this->memberId ;

		//使用事务处理
		$transaction = Yii::app()->db->beginTransaction();
		try {
			foreach( $this->cart as $key => $val ){
				//如果购物车里有此产品，num相加
				$count = $model->updateCounters(
						array('num'=>$val),
						'productId=:productId and memberId=:memberId and stockId =:stockId',
						array(':productId'=>$model->productId,':memberId'=>$model->memberId,':stockId'=>$key)
						);
				if( !$count ){
					$_model = clone $model;
					$_model->stockId = $key;
					$_model->num = $val;
					if( !$_model->save() ) {
					$this->addErrors( $_model->getErrors() );
					return false;
					}
				}
			}
			$transaction->commit();
			return true;
		} catch (Exception $e) {
			//print_r($e);
			$transaction->rollback(); //如果操作失败, 数据回滚
			//$this->addError( 'cart','发生系统错误' );
			throw new CHttpException(503,$e);

			return false;
		}
	}

	/**
	* 手机端购物车列表。。数据跟web端不一致
	*
	*/
	public function cartList(){
		$cartsdata = $this->getCarts();
		$specs = $this->getSpecs();
		if( $this->userType == tbMember::UTYPE_SALEMAN ){
			$priceType = 0;
		}else{
			$priceType = (int)tbMember::model()->getPriceType( $this->memberId );
		}


		$totalPrice = 0;
		$totalItems = count($cartsdata);
		$failureList = array();
		$data['list'] = array();

		//给数据分组
		foreach ( $cartsdata as &$val ){
			$relation = '';
			foreach ( $val['relation'] as $reval ){
				if(isset($specs[$reval])){
					$relation .= $specs[$reval]['specName'].': '.$specs[$reval]['title'].' '.$specs[$reval]['serialNumber'];
				}
			}

			$val['price'] = ($priceType)?$val['tradePrice']:$val['price'];

			if( $val['state']!='0' || empty( $relation ) ) {
				$totalItems --;
				$key = 'failureList';
				$failureList[] = $val['cartId'];
			}else{
				$totalPrice  += $val['num']*$val['price'];
				$key = 'list';
			}

			if( !isset($data[$key][$val['productId']]) ){
				$unit = tbUnit::getUnitName( $val['unitId']);
				$data[$key][$val['productId']] = array(
							'productId'=>$val['productId'],
							'title'=>'【'.$val['serialNumber'].'】'.$val['title'],
							'mainPic'=> Yii::app()->params['domain_images'] . $val['mainPic'] . '_200',

						);
			}

			$data[$key][$val['productId']]['relations'][] = array(
				'cartId'=>$val['cartId'],
				'stockId'=>$val['stockId'],
				'relation'=>$relation,
				'num'=>$val['num'],
				'price'=>$val['price'],
				'intprice'=>$val['price']*100,
				'unit'=>$unit,
			);
		}

		$data['list'] = array_values($data['list']);

		$data['totalItems'] = $totalItems;
		$data['totalPrice'] =  Order::priceFormat($totalPrice,'');
		$data['inttotalPrice'] = $totalPrice*100;

		//把失效的cartId写进缓存，清除失效产品时直接从cache中读数据。
		if(!empty($failureList)){
			Yii::app()->cache->set('failureCart_'.$this->memberId,$failureList,3600);
		}
		return $data;
	}

}
