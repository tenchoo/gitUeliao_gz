<?php
/**
* 退货管理
*/
class RefundController extends Controller {

	/**
	* 退货单列表
	* @access 退货单列表
	*/
	public function actionIndex() {
		$this->showList( null );
	}

	/**
	* @access 待审核退货列表
	*/
	public function actionNeedconfirm() {
		$this->showList( 2 );
	}

	public function actionChecklist() {
		$this->showList( 0 );
	}

	private function showList( $state ){
		$condition['state'] = $state;
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
	* 查看退货单
	* @access  查看退货单
	* @param integer id
	*/
	public function actionView(){
		$id = Yii::app()->request->getQuery('id');

		$OrderRefund = new OrderRefund( Yii::app()->user->id,'' );
		$data = $OrderRefund->getOne( $id,null,true );

		if( !$data ){
			$url = $this->createUrl('index');
			$this->redirect($url);
		}

		$this->render('view',$data );
	}

	/**
	* @access  退货单财务确认
	* @param integer id
	*/
	public function actionConfirm(){
		$id = Yii::app()->request->getQuery('id');

		$OrderRefund = new OrderRefund( Yii::app()->user->id,'' );
		$data = $OrderRefund->getOne( $id,2,true );

		if( !$data ){
			$url = $this->createUrl('needconfirm');
			$this->redirect($url);
		}

		if( Yii::app()->request->isPostRequest ){
			if( $OrderRefund->confirm() ) {
				$this->dealSuccess( $this->createUrl( 'needconfirm' ) );
			} else {
				$errors = $OrderRefund->getErrors();
				var_dump( $errors );
				$this->dealError( $errors );
			}
		}

		$this->render('confirm',$data );
	}

	/**
	* 审核退货单
	* @access  审核退货单
	* @param integer id
	*/
	public function actionCheck(){
		$id = Yii::app()->request->getQuery('id');

		$OrderRefund = new OrderRefund( Yii::app()->user->id,'' );
		$data = $OrderRefund->getOne( $id,0,true );

		if( !$data ){
			$url = $this->createUrl('checklist');
			$this->redirect($url);
		}

		if( Yii::app()->request->isPostRequest ){
			if( $OrderRefund->check( true ) ) {
				$this->dealSuccess( $this->createUrl( 'checklist' ) );
			} else {
				$errors = $OrderRefund->getErrors();
				$this->dealError( $errors );
			}
		}

		$this->render('check',$data );
	}

	/**
	* 审核退货单
	* @access  审核退货单
	* @param integer id
	*/
	public function actionPrint(){
		$id = Yii::app()->request->getQuery('id');

		$state = PrintPush::printRefund( $id,$msg,true );
		if( Yii::app()->request->getIsAjaxRequest() ) {
			$json = new AjaxData( $state , $msg );
			echo $json->toJson();
		}else{
			echo $msg;
		}
		Yii::app()->end();
	}
}
