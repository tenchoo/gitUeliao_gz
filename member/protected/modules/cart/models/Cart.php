<?php
/**
* 购物车
* @version 0.1
* @package CFormModel
*/
class Cart extends CFormModel {

	public $productId;

	public $memberId = '';

	public $cart;

	public $spec;

	public $totalItems = 0;

	public $totalPrice = 0;

	/**
	* 用户类型，0为客户，1为业务员
	*/
	public $userType = 0;
	
	/**
	* 原来的程序，无调用后删除。
	*/
	public function init(){
		$userType = Yii::app()->user->getState('usertype');
		if( $userType != tbMember::UTYPE_MEMBER ){
			$this->userType = 1;
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
	* 取得购物车产品信息
	*/
	public function getCarts( array $cartIds = array() ){
		$cart = new tbCart();
		$list  =  $cart->cartLists();
		if( empty($list) ){
			$totalItems = $totalPrice = 0;
			return ;
		}

		$spec = $items = $total = array();
		foreach ( $list as &$val ){
			if(!empty( $val['relation'] )){
				$val['relation'] = json_decode( '{'.preg_replace('/(\w+):/is', '"$1":',  $val['relation']).'}',true);
				$spec =  array_merge( $spec,$val['relation'] );
			}
		}

		$specs = tbSpecvalue::getSpecs( $spec );

		$userType = Yii::app()->user->getState('usertype');
		if( $userType == tbMember::UTYPE_MEMBER ){
			$priceType = tbMember::model()->getPriceType( Yii::app()->user->id );
		}

		$api = new ApiClient('www','service',false);
		foreach ( $list as &$val ){
			$val['url'] = $api->createUrl('/product/detail',array('id'=>$val['productId']));

			$relation = '';
			if(is_array($val['relation'])){
				foreach ( $val['relation'] as $reval ){
					if(isset($specs[$reval])){
						$relation .= $specs[$reval]['specName'].':'.$specs[$reval]['title'].' '.$specs[$reval]['serialNumber'];
					}
				}
			}
			$val['relation'] = $relation;

			$val['price'] = ( $priceType )?$val['tradePrice']:$val['price'];

			if( $val['state'] == '0' && $val['relation']!='' ) {
				$total[] = $val['totalPrice'] = $val['num']*$val['price'];
				$items[] = $val['num'];
			}

		}

		return $list;
	}

	/**
	* 取得购物车产品信息,并拆分成现货和订货
	* 订购量大于现有库存量-安全库存量则进入订货
	*/
	public function getConfirms( array $cartIds = array() ){

		$cart = new tbCart();
		$data =  $cart->cartLists();
		$result = array();
		foreach ( $data as &$val ){
			//下架或失效产品过滤掉
			if( $val['state'] != '0' || $val['relation'] =='' ) continue;

			//可销售量
			$val['total'] = ProductModel::total( $val['singleNumber'] );
			$key = ( $val['num'] > $val['total'] )?'1':'0';
			$result[$key]['list'][] = $val;

			if( !isset( $result[$key]['totalItems'] ) ){
				$result[$key]['totalItems'] = 0;
			}
			$result[$key]['totalItems']  += $val['num'];
		}
		return $result;
	}



	/**
	* 取得系统设置中配置的留货天数
	*/
	public static function getKeeyday(){
		return tbConfig::model()->get( 'order_keep_time' );;
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
		}
	}

	/**
	* 取得地址信息
	* @param integer $memberId
	*/
	public function getlist( $memberId ){
		$address = tbMemberAddress::model()->getAll( $memberId );
		$result = array();
		foreach( $address as $key=>$val ){
			$result[$key] = $val->attributes;
			$result[$key]['cityinfo'] = tbArea::getAreaStrByFloorId( $val->areaId );
		}
		return $result;
	}
}
