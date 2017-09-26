<?php
/*
* 产品管理
* @access 产品管理
*/
class DefaultController extends Controller {

	/**
	* 显示除删除外所有的产品列表
	* @access 价格设置
	*/
	public function actionList() {
		$this->showlist( array(0,1),'setprice' );
	}

	/**
	* 销售中的产品
	* @access 销售中的产品
	*/
	public function actionIndex() {
		$this->showlist( 0 );
	}

	/**
	* 仓库中的产品
	* @access 仓库中的产品
	*/
	public function actionStocklist() {
		$this->showlist( 1 );
	}

	/**
	* 回收站的产品
	* @access 回收站的产品
	*/
	public function actionRecycle() {
		$this->showlist( 2 );
	}

	private function showlist( $state ,$op = '' ){
		$serialNumber = trim(Yii::app()->request->getQuery('serialNumber'));
		$categoryId = trim(Yii::app()->request->getQuery('category'));
		$pageSize = (int) tbConfig::model()->get( 'page_size' );
		$product = Product::getList( $state, array('serialNumber'=>$serialNumber,'categoryId'=>$categoryId),$pageSize);
		$this->render('index',array( 'list'=>$product['data'],'pages'=>$product['pages'],'serialNumber'=> $serialNumber,'categoryId'=> $categoryId,'op'=> $op) );
	}

	/**
	 * 商品上架
	 * @access 商品上架
	 */
	public function actionOnshelf() {
		$this->changeState( 0 );
	}

	/**
	 * 商品下架
	 * @access 产品下架
	 */
	public function actionOffshelf() {
		$this->changeState( 1 );
	}

	/**
	 * 删除产品，标删
	 * @access 删除产品
	 */
	public function actionDel() {
		$this->changeState( 2 );
	}

	/**
	 * 从回收站中删除产品
	 * @access 回收站删除产品
	 */
	/* public function actionRecycledel() {
		$this->changeState( 4 );
	} */

	/**
	 * 商品上/下架
	 * @access 商品上/上架
	 */
	private function changeState( $state ) {
		$id = Yii::app()->request->getQuery('id');
		Product::changeState( $state,$id );
		$this->dealSuccess(Yii::app()->request->urlReferrer);
	}

	/**
	 * 商品可销售量查询
	 * @access 可销售量查询
	 */
	public function actionSalesnum(){
		$data['s'] = Yii::app()->request->getQuery('s');
		$type = Yii::app()->request->getQuery('type');
		$data['showDetail'] = false;

		if( empty( $data['s'] )) goto end;

		//查找产品安全库存并判断产品是否存在
		$safetyTotal = tbProductStock::model()->findByAttributes(array('singleNumber'=>$data['s']));
		if( !$safetyTotal ) goto end;

		//安全库存
		$data['safety'] = $safetyTotal->safetyStock;

		//库存
		$data['total'] = tbWarehouseProduct::model()->singleSaleCount( $data['s'] );

		//锁定产品数量
		$data['lockTotal'] = tbStorageLock::model()->singleCount( $data['s'] );

		$data['canSell'] = bcsub( $data['total'],$data['lockTotal'],1 );
		//$data['canSell'] = bcsub( $data['canSell'],$data['safety'],1 );

		//查看锁定明细
		if( $type == 'lockdetail' ){
			$data['showDetail'] = true;
			$criteria = new CDbCriteria;
			$criteria->compare('t.singleNumber',$data['s']);
			$pageSize = (int) tbConfig::model()->get('page_size');
			$model = new CActiveDataProvider('tbStorageLock', array(
				'criteria'=>$criteria,
				'pagination'=>array('pageSize'=>$pageSize,'pageVar'=>'page'),
			));
			$list = $model->getData();
			$data['pages'] = $model->getPagination();
			$data['list'] = array_map( function( $i ){ $i->createTime = date('Y-m-d H:i',$i->createTime); return $i->attributes;}, $list);
		}

		//是否尾货销售
		$data['isTail'] = ProductModel::isTail( $data['s'] );

		if( $data['isTail'] ) {
			$data['canSell'] = 0;
		}

		end:
		$this->render('salesnum',$data );
	}

}