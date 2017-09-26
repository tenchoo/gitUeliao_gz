<?php
/**
 * 产品详情
 * @package models
 * @version 0.1.1
*/
class Product {
	/**
	* 取得产品详情
	* @param integer $productId
	*/
	public function getDetail( $productId ){
		if( empty( $productId ) ){
			return ;
		}

		$productModel = tbProduct::model()->findByPk( $productId );
		if ( !$productModel ){
			return ;
		}

		$product = $productModel->attributes;
		$detail = tbProductDetail::model()->findByPk( $productId );
		if( !$detail ){
			return ;
		}

		$product['testResults'] = $detail->testResults;
		$product['content'] = $detail->content;
		$product['pictures'] = json_decode( $detail->pictures,true);;
		if( empty ($product['pictures'] ) || !is_array($product['pictures']) ) $product['pictures'] = array();

		$product['attr']		=	$this->getAttr( $productId );
		$product['specStock']	=	$this->specStock( $productId );
		$product['dealCount']	=	tbOrderProduct::model()->dealCount( $productId ,0 );
		$product['commentCount']=	tbComment::model()->commentCount( $productId,0 );
		$product['unitName']	=	tbUnit::getUnitName( $product['unitId'] );



		//取得工艺信息
		if( $product['type'] == tbProduct::TYPE_CRAFT ){
			$product['crafts'] = $productModel->getCrafts();
		}

		//取得工艺产品列表
		$product['craftList'] = $productModel->getCraftList();;
		return $product;
	}


	/**
	* 取得尾货产品详情
	* @param integer $tailId
	*/
	public function getTailDetail( $tailId ){
		if( empty( $tailId ) ){
			return ;
		}

		$tailModel = tbTail::model()->findByPk( $tailId );
		if( !$tailModel ){
			return ;
		}

		$productId = $tailModel->productId;
		$productModel = tbProduct::model()->findByPk( $productId );
		if ( !$productModel ){
			return ;
		}

		$detail = tbProductDetail::model()->findByPk( $productId );
		if( !$detail ){
			return ;
		}

		$product = array_merge( $tailModel->attributes,$productModel->getAttributes( array('type','baseProductId','categoryId','unitId','title','serialNumber','mainPic') ) );
		$product['testResults'] = $detail->testResults;
		$product['content'] = $detail->content;
		$product['pictures'] = json_decode( $detail->pictures,true);;
		if( empty ($product['pictures'] ) || !is_array($product['pictures']) ) $product['pictures'] = array();

		$product['attr']		=	$this->getAttr( $productId );
		$product['dealCount']	=	tbOrderProduct::model()->dealCount( $productId ,$tailId );
		$product['commentCount']=	tbComment::model()->commentCount( $productId,$tailId );
		$product['unitName']	=	tbUnit::getUnitName( $product['unitId'] );
		$product['specStock']	=	$this->getTailStock( $productId,$tailModel ) ;

		//取得工艺信息
		if( $product['type'] == tbProduct::TYPE_CRAFT ){
			$product['crafts'] = $productModel->getCrafts();
		}

		//取得工艺产品列表
		$product['craftList'] = $productModel->getCraftList();;
		return $product;
	}


	public function getTailStock( $productId,$tailModel ){
		$singleNumbers = array_map( function($i){ return $i->singleNumber;},$tailModel->single );

		$specStock = tbProductStock::model()->specBySingles ( $singleNumbers );
		$specList =	$this->getSpec( $productId );

		foreach ( $specStock as $key=>&$sval){
			$spec =json_decode( '{'.preg_replace('/(\w+):/is', '"$1":',  $sval['relation']).'}');
            $k = current($spec);
			if( !array_key_exists( $k, $specList ) ){
				unset($specStock[$key]);
				continue;
			}

			//可销售量
			$sval['total'] = ProductModel::total( $sval['singleNumber'] ,true );
			$sval['total'] =  Order::quantityFormat( $sval['total'] );

			if( $sval['total']<=0 ){
				unset($specStock[$key]);
				continue;
			}

			$sval['colorSeriesId'] =$specList[$k]['colorSeriesId'];
			$sval['title'] = $specList[$k]['title'];
			$sval['code'] = $specList[$k]['code'];
			$sval['picture'] = $specList[$k]['picture'];
			$sval['serialNumber'] = $specList[$k]['serialNumber'];
		}

		return $specStock;
	}

	/**
	* 取得颜色组
	* @param array $data 产品单品信息
	*/
	public function colorGroups( $data ){
		$colorgroups = array();
		if( count($data) <= 5 ){
			return $colorgroups;
		}

		//颜色色系统
		$colorSeries = tbSetGroup::model()->getList( 2 );
		foreach( $data as $val ){
			if( array_key_exists($val['colorSeriesId'],$colorSeries ) ){
				$colorgroups[$val['colorSeriesId']] = $colorSeries[$val['colorSeriesId']];
			}
		}
		return $colorgroups;
	}



	/**
	* 取得属性信息
	* @param integer $productId 产品ID
	*/
	public function getAttr( $productId ){
		$data = tbProductAttribute::model()->productAttribtes( $productId );
		$result = array();
		foreach( $data as $val ){
			$result[$val['setGroupId']][] = $val;
		}
		return $result;
	}

	/**
	* 规格信息
	* @param integer $productId 产品ID
	*/
	public function getSpec( $productId ){
		$data =  tbProductSpec::getSpec ( $productId );
		return $data;
	}

	/**
	* 规格库存信息--普通产品
	* @param integer $productId 产品ID
	*/
	public function specStock( $productId ){
		$data = tbProductStock::model()->specStock ( $productId );

		$specList =	$this->getSpec( $productId );
		foreach ( $data as $key=>&$sval){
			$spec =json_decode( '{'.preg_replace('/(\w+):/is', '"$1":',  $sval['relation']).'}');
            $k = current($spec);
			if( !array_key_exists( $k,$specList ) ) {
				unset( $data[$key] );
			}else{
				$sval['colorSeriesId'] =$specList[$k]['colorSeriesId'];
				$sval['title'] = $specList[$k]['title'];
				$sval['code'] = $specList[$k]['code'];
				$sval['picture'] = $specList[$k]['picture'];
				$sval['serialNumber'] = $specList[$k]['serialNumber'];

				//可销售量
				$sval['total'] = ProductModel::total( $sval['singleNumber'] );
				$sval['total'] =  Order::quantityFormat( $sval['total'] );
			}
		}
		return $data;
	}




	/**
	* 相似产品推荐--读取同一子分类下的最新产品, 状态为在售中
	* @param integer $productId 产品ID
	*/
	public static function getSamelist( $productId ){
		$model = tbProduct::model()->findByPk( $productId );
		if( !$model ){
			return ;
		}

		$result = $model->findAll(array(
					'select'=>'productId,title,price,mainPic',
					'condition' => 'categoryId = :categoryId and productId != :productId and state = 0',
					'params' => array( ':categoryId'=>$model->categoryId,':productId'=>$productId ),
					'order' =>'createTime desc',
					'limit' =>'5',

			));
		return $result;
	}

	/**
	* 增加浏览记录
	* @param integer $productId 产品ID
	*/
	public function addViews( $productId ){
		$model = new tbProductView();
		$model->productId = $productId;
		$model->save();
	}
}
