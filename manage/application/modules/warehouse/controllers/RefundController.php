<?php
/**
 * 仓库---订单退货入库单管理
 * @access 订单退货入库单管理
 * @author liang
 * @package Controller
 * @version 0.1
 * @date 2016-07-07
 *
 */
class RefundController extends Controller {

	/**
	 * 待入库退货订单
	 * @access 待入库退货订单
	 */
	public function actionIndex() {
		$condition['state'] = 1;
		$condition['createTime1'] = Yii::app()->request->getQuery('createTime1');
		$condition['createTime2'] = Yii::app()->request->getQuery('createTime2');
		$condition['orderId'] = trim( Yii::app()->request->getQuery('orderId') );
		$condition['refundId'] = trim(Yii::app()->request->getQuery('refundId'));
		$OrderRefund = new OrderRefund( Yii::app()->user->id,'' );
		$data = $OrderRefund->search( $condition );
		$data['stateTitles'] =  $OrderRefund->stateTitles();

		$this->render('index',array_merge( $data,$condition ));
	}

	/**
	 * 退货入库单管理
	 * @access 退货入库单管理
	 */
	public function actionList() {
		$data['f'] =  Yii::app()->request->getQuery('f');
		$data['id'] =  Yii::app()->request->getQuery('id');
		$data['p'] =  Yii::app()->request->getQuery('p');

		$c = new CDbCriteria();
		$source = tbWarehouseWarrant::FORM_REFUND;
		$c->addInCondition('source',[tbWarehouseWarrant::FORM_REFUND]);
		$c->order = "createTime DESC";
		if( $data['f'] ){
			$c->compare('t.factoryNumber',$data['f']);
		}

		$pages = new CPagination( tbWarehouseWarrant::model()->count($c) );
		$pages->setPageSize( tbConfig::model()->get('page_size') );
		$pages->applyLimit( $c );

		$orderList = tbWarehouseWarrant::model()->findAll( $c );

		$data['pages']  = $pages;
		$data['list'] = $orderList;

		$this->render( 'list' ,$data );
	}


	/**
	 * 查看退货入库单
	 * @access 查看退货入库单
	 */
	public function actionView() {
		$id = Yii::app()->request->getQuery('id');
		if( is_numeric($id) && $id>0 ){
			$model = tbWarehouseWarrant::model()->with('posts')->findByPk( $id );
			if( !$model ){
				$this->redirect( $this->createUrl( 'list' ) );
			}
		}else{
			$this->redirect( $this->createUrl( 'list' ) );
		}


		$data = $model->attributes;
		if( $model->posts ) {
			$data['postInfo'] = $model->posts->attributes;
		}
		else {
			$data['postInfo'] = new tbOrderPost2();
		}
		$data['products'] = tbWarehouseWarrantDetail::model()->findAllByWarrant( $id );

		$this->render( 'view',array('data'=>$data ) );
	}

	/**
	 * 创建退货入库单
	 * @access 创建退货入库单
	 */
	public function actionImport() {
		$id = Yii::app()->request->getQuery('id');

		$OrderRefund = new OrderRefund( Yii::app()->user->id,'' );
		$data = $OrderRefund->getOne( $id,1,true );

		if( !$data ){
			$url = $this->createUrl('index');
			$this->redirect($url);
		}

		if( Yii::app()->request->isPostRequest ){
			if( $OrderRefund->import() ) {
				$this->dealSuccess( $this->createUrl( 'index' ) );
			} else {
				$errors = $OrderRefund->getErrors();
				$this->dealError( $errors );
			}
		}

		$this->render('import',$data );
	}
}