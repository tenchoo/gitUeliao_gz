<?php
/**
 * 购物车
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$cartId
 * @property integer	$productId			产品ID
 * @property integer	$tailId				尾货ID
 * @property integer	$state				购物车是否已过期：0正常，1已过期
 * @property integer	$memberId			所属memberId
 * @property number		$num				购买数量,带2位小数点
 * @property integer	$stockId			产品规格ID
 * @property timestamp	$createTime			加入购物车时间 CURRENT_TIMESTAMP
 * @property string		$singleNumber		单品编码
 *
 */

 class tbCart extends CActiveRecord {

	public $memberId;

	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{cart}}";
	}

	public function rules() {
		return array(
			array('productId,memberId,num,stockId','required'),
			array('productId,memberId,stockId', "numerical","integerOnly"=>true),
			array('num', "numerical",'min'=>'0.1','numberPattern'=>'/^\d+(\.[0-9])?$/','message'=>Yii::t('order','Purchase quantity must be an integer greater than 0 or with a decimal point')),
			array('singleNumber','safe'),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'productId' => '产品ID',
			'memberId' => '客户ID',
			'num' => '购买数量',
			'stockId' => '产品规格ID',
		);
	}

	public function init() {
		parent::init();
		$this->memberId = Yii::app()->user->id ;
	}


	/**
	* 取得购物车产品数量
	*/
	public function getTotalNum( $memberId ,$containTail = false ){

		if( empty( $memberId ) ) return 0;

		$condition =  'memberId = :id ';
		if( !$containTail ){
			$condition .=  ' and tailId = 0 ';
		}

		return $this->count( $condition , array( ':id'=>$memberId ));
	}

	/**
	* 加入购物车--正常产品
	* @param integer $productId 产品ID
	* @param array $cartData   加入购物车的规格和数量
	*/
	public function addCart( $productId, $cartData ){
		if ( !is_array( $cartData ) || empty( $cartData ) ){
			$this->addError( 'num',Yii::t('order','Purchase quantity must be submitted') );
			return false ;
		}

		//检查提交过来的数据格式是否正确
		$this->productId = $productId;
		$_models = array();
		foreach( $cartData as $key => $val ){
			$_model = clone $this;
			$_model->stockId = $key;
			$_model->num = $val;
			if( !$_model->validate() ) {
				$this->addErrors( $_model->getErrors() );
				return false ;
			}
			$_models[] = $_model;
		}


		//判断此产品是否存在
		$f = tbProduct::model()->exists( 'productId = :pid and state = 0 ',array( ':pid'=>$this->productId ) );
		if( !$f ){
			$this->addError( 'productId','产品不存在或已下架' );
			return false;
		}

		//判断此产品的规格是否存在
		$Stock = tbProductStock::model()->specStock ( $this->productId, array_keys( $cartData ) );
		$nofound = array_diff( array_keys($cartData),array_keys( $Stock ) );
		if(!empty($nofound)){
			$this->addError( 'productId','产品不存在id为'.current($nofound).'的单品' );
			return false;
		}



		//使用事务处理,保存购物车
		$transaction = Yii::app()->db->beginTransaction();
		try {
			foreach( $_models as $_model ){
				//如果购物车里有此产品，num相加
				$cmodel = $this->find('stockId=:stockId and memberId=:memberId',array(':stockId'=>$_model->stockId,':memberId'=>$_model->memberId));
				if( $cmodel ){
					//转成整数再相加
					$cmodel->num = (($cmodel->num *100 )+($_model->num *100 ))/100;
					$_model = $cmodel;
				}else{
					$_model->singleNumber = $Stock[$_model->stockId]['singleNumber'];
				}
				if( !$_model->save() ) {
					$this->addErrors( $_model->getErrors() );
					return false;
				}
			}
			$transaction->commit();
			return true;
		} catch (Exception $e) {
			$transaction->rollback(); //如果操作失败, 数据回滚
			throw new CHttpException(503,$e);
			return false;
		}
	}


	/**
	* 加入购物车--尾货产品
	* @param integer $tailId 	尾货产品ID
	* @param array $cartData   加入购物车的单品编码和数量
	*/
	public function addTailCart( $tailId, $cartData = array() ){
		if ( empty( $tailId ) ){
			$this->addError( 'num',Yii::t('order','Purchase quantity must be submitted') );
			return false ;
		}

		//判断此产品是否存在
		$tbTail = tbTail::model()->findByPk( $tailId,'state = :state and isSoldOut = 0 ',array( ':state'=>'selling' ) );
		if( !$tbTail ){
			$this->addError( 'productId','产品不存在或已下架' );
			return false;
		}

		$this->tailId = $tailId;
		$this->productId = $tbTail->productId;

		$_models = array();
		if( $tbTail->saleType == 'whole' ){
			//是否已在购物车
			$cmodel = $this->findByAttributes( array('tailId'=>$this->tailId,'memberId'=>$this->memberId,'state'=>'0','singleNumber'=>'') );
			if( $cmodel ){
				$this->addError( 'num',Yii::t('order','Shopping cart has been added') );
				return false ;
			}

			//整批销售
			$this->num = 1;
			$this->stockId = 0;
			$this->singleNumber = '';
			$_models[] = $this;

		}else{
			//降价促销
			if ( !is_array( $cartData ) || empty( $cartData ) ){
				$this->addError( 'num',Yii::t('order','Purchase quantity must be submitted') );
				return false ;
			}

			$singles = array_map( function($i){ return $i->singleNumber;},$tbTail->single );

			//检查提交过来的数据格式是否正确
			foreach( $cartData as $key => $val ){
				$_model = clone $this;
				$_model->stockId = 0;
				$_model->singleNumber = $key;
				$_model->num = $val;
				if( !$_model->validate() ) {
					$this->addErrors( $_model->getErrors() );
					return false ;
				}

				if( !in_array( $key,$singles) ){
					$this->addError( 'productId','产品不存在编号为'.$key.'的单品' );
					return false ;
				}

				//如果购物车里有此产品，num相加
				$cmodel = $this->findByAttributes( array('tailId'=>$_model->tailId,'memberId'=>$_model->memberId,'state'=>'0','singleNumber'=>$_model->singleNumber) );
				if( $cmodel ){
					//转成整数再相加
					$cmodel->num = (($cmodel->num *100 )+($_model->num *100 ))/100;
					$_model = $cmodel;
				}

				//对比可销售量
				$stockNum = ProductModel::total( $key, true );
				if( $_model->num > $stockNum  ){
					$this->addError( 'productId',$key.'购买数量不能超过库存数量' );
					return false ;
				}

				$_models[] = $_model;
			}
		}

		//使用事务处理,保存购物车
		$transaction = Yii::app()->db->beginTransaction();
		try {
			foreach( $_models as $_model ){
				if( !$_model->save() ) {
					$this->addErrors( $_model->getErrors() );
					return false;
				}
			}
			$transaction->commit();
			return true;
		} catch (Exception $e) {
			$transaction->rollback(); //如果操作失败, 数据回滚
			throw new CHttpException(503,$e);
			return false;
		}
	}



	/**
	* 修改购物车数量
	* @param integer $cartId	购物车ID
	* @param integer $num   	购物车的数量
	*/
	public function qty( $cartId,$num ){
		if( !is_numeric($cartId) || empty($cartId) || empty( $this->memberId ) ) return false;

		$model =  $this->findByPk( $cartId ,'memberId = :m and state = 0 ' ,array( ':m'=>$this->memberId ) );
		if( !$model ) return false;

		//如果是尾货购物车
		if( $model->tailId > 0 ){
			return $this->qtyTail( $model,$num  );
		}else{
			$model->num = $num;
			return $model->save();
		}
	}

	private function qtyTail( $model,$num ){
		$tbTail = tbTail::model()->findByPk( $model->tailId,'state = :state and isSoldOut = 0 ',array( ':state'=>'selling' ) );
		if( !$tbTail ){
			$model->state = 1;
			$model->save();
			return false;
		}

		if( $tbTail->saleType == 'whole' ){
			return false;
		}

		if( empty( $model->singleNumber ) ){
			$model->state = 1;
			$model->save();
			return false;
		}

		//对比可销售量
		$stockNum = ProductModel::total( $model->singleNumber, true );
		if( $num > $stockNum  ){
			$this->addError( 'productId',$key.'购买数量不能超过库存数量' );
			return false ;
		}

		$model->num = $num;
		return $model->save();
	}


	/**
	* 从取得购物车列表--普通产品
	* @param array 指定的购物车IDS
	*/
	public function cartLists( array $cartIds = array() ){

		$where = ' c.tailId=0 and c.memberId = '.$this->memberId ;
		if( !empty ( $cartIds ) ){
			$where .= ' and c.cartId in ( '. implode(',',$cartIds ).' ) '  ;
		}

		$sql = "SELECT c.*,p.state as pstate,p.price,p.unitId,p.tradePrice,p.title,p.serialNumber,p.mainPic,s.depositRatio,s.relation,s.singleNumber from {{cart}} c left join {{product}} p on c.productId = p.productId left join {{product_stock}} s on c.stockId=s.stockId where $where";
		$cmd = Yii::app()->db->createCommand( $sql );

		$list  = $cmd->queryAll();
		if( empty( $list ) ) return array();

		//取得当前客户支付的批发价格
		$productIds = array_map( function ( $i ){ return $i['productId'];},$list );
		$specPrices = tbMemberApplyPrice::model()->getMemberPrice( $this->memberId,$productIds );

		foreach ( $list as &$val ){
			$val['state'] = max( $val['pstate'],$val['state'] );
			$val['num'] = Order::quantityFormat( $val['num'] );

			//查找出规格名称
			$val['relation'] = tbProductStock::relationTitle( $val['singleNumber'],$val['color'] );

			if( array_key_exists($val['productId'],$specPrices ) ){
				$val['price'] 	 	= $val['tradePrice']	 = $specPrices[$val['productId']];
			}
		}
		return $list;
	}

	/**
	* 从取得购物车列表--全部产品
	* @param array 指定的购物车IDS
	*/
	public function allCartLists( array $cartIds = array() ){
		//正常产品列表
		$list = $this->cartLists( $cartIds );

		//尾货产品列表
		$tailList = $this->cartTailLists( $cartIds );

		return array_merge( $list,$tailList );
	}


	/**
	* 从取得购物车列表--尾货产品
	* @param array 指定的购物车IDS
	*/
	public function cartTailLists( array $cartIds = array() ){

		$c = new CDbCriteria;
		$c->addCondition('tailId>0');
		$c->compare('memberId',$this->memberId );

		if( !empty($cartIds) ){
			$c->compare('cartId',$cartIds );
		}
		//尾货产品列表
		$carts = $this->findAll( $c );
		if( empty($carts) ){
			return array();
		}

		$tailId = array_map( function($i){ return $i->tailId;},$carts );
		$tailModel = tbTail::model()->findAllByPk( $tailId );

		$tails = array();
		foreach ( $tailModel as $val ){
			$tails[$val->tailId]['product'] = array_merge(  $val->product->getAttributes( array('title','unitId','serialNumber','mainPic') ),
									 $val->getAttributes( array('tailId','productId','price','tradePrice','saleType') )
								);
			$tails[$val->tailId]['state']  = ( $val->state == 'selling' && $val->isSoldOut == '0' )?'0':'1';
			$tails[$val->tailId]['single'] = array_map( function($i){ return $i->singleNumber;},$val->single );
		}

		$list = array();
		$color = $relation = '';
		foreach (  $carts as $val ){
			if( !array_key_exists( $val->tailId , $tails ) ){
				$val->delete();
				continue;
			}

			$val->state = max( $val->state,$tails[$val->tailId]['state'] );

			if( $tails[$val->tailId]['product']['saleType'] == 'whole' ){
				if( $val->state == '0' ){
					//整批销售，计算总数
					$num = array();
					foreach ( $tails[$val->tailId]['single'] as $_single ){
						$num[] = ProductModel::total( $_single,true );
					}

					$val->num = array_sum( $num );
				}
			}else{
				//查找出规格名称
				$relation = tbProductStock::relationTitle( $val->singleNumber,$color );

				if( !in_array( $val->singleNumber,$tails[$val->tailId]['single'] ) ){
					$val->state = 1;
				}

				if( $val->state == '0' ){
					//判断购买数量是否大于当前库存量，若大于，把购物车失效。
					$num = ProductModel::total( $val->singleNumber,true );
					if( $val->num > $num ){
						$val->state = 1;
					}
				}
			}

			$val->num = Order::quantityFormat( $val->num );
			$info = array_merge( $val->getAttributes( array('cartId','tailId','state','num','stockId','singleNumber') ),$tails[$val->tailId]['product'] );
			$info['relation'] = $relation;
			$info['color'] = $color;
			$list[] = $info;
		}
		return $list;
	}

	/**
	* 取得购物车产品信息,并拆分成现货和订货
	* 订购量大于现有库存量-安全库存量则进入订货
	* @use 订单确定页面
	*/
	public function getConfirms( $containTail = false ){
		if( $containTail ){
			$data =  $this->allCartLists();
		}else{
			$data =  $this->cartLists();
		}


		$result = array();
		foreach ( $data as &$val ){
			//下架或失效产品过滤掉
			if( $val['state'] != '0' ) continue;

			if( array_key_exists('tailId',$val ) && $val['tailId']>0 ){
				$key = tbOrder::TYPE_TAIL;
			}else{
				//可销售量
				$val['total'] = ProductModel::total( $val['singleNumber'] );
				$key = ( $val['num'] > $val['total'] )?'1':'0';
			}

			$result[$key]['list'][] = $val;

			if( !array_key_exists( 'totalItems', $result[$key] ) ){
				$result[$key]['totalItems'] = 0;
			}
			$result[$key]['totalItems']  = bcadd( $val['num'],$result[$key]['totalItems'],1 );
		}

		return $result;
	}


	/**
	* 修改购物车数量
	* @param integer $cartId	购物车ID
	* @param integer $num   	购物车的数量
	*/
	public function del( $cartId ){
		if( empty ($cartId) || empty($this->memberId) ) return false;

		$criteria = new CDbCriteria;
		$criteria->compare('cartId', $cartId);
		$criteria->compare('memberId', $this->memberId  );

		return  tbCart::model()->deleteAll( $criteria ) ;
	}


}