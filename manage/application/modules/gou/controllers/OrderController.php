<?php
/*
* @access 送货单管理
*/
class OrderController extends Controller {

	//欢迎页面无需检察权限
    public function beforeAction($action) {
        return true;
    }

	/**
	* @access 送货单列表
	*/
	public function actionIndex() {
		$condition['areaId'] = Yii::app()->request->getQuery('areaId');
		$condition['deliverymanId'] = Yii::app()->request->getQuery('deliverymanId');
		$condition['state'] = Yii::app()->request->getQuery('state',0);
		$condition['keyword'] = trim(Yii::app()->request->getQuery('keyword'));
		$condition['type'] = Yii::app()->request->getQuery('type');
		$condition['isDel'] = Yii::app()->request->getQuery('isDel','0');


		$excel = Yii::app()->request->getQuery('excel');
		$exportExcel = ( $excel === 'exportExcel' )?true:false;

		$model = new tbDeliveryOrder();
		$data = $model->serach( $condition ,20,$exportExcel );

		$data['from'] = Yii::app()->request->url;
		$condition['excel'] = 'exportExcel';
		$data['excelUrl'] = $this->createUrl( 'index', $condition );
		$this->render( 'index',array_merge( $data,$condition ) );
	}

	/**
	* @access 已关闭列表
	*/
	public function actionCloselist() {
		$condition['areaId'] = Yii::app()->request->getQuery('areaId');
		$condition['deliverymanId'] = Yii::app()->request->getQuery('deliverymanId');
		$condition['state'] = Yii::app()->request->getQuery('state',0);
		$condition['keyword'] = trim(Yii::app()->request->getQuery('keyword'));
		$condition['type'] = Yii::app()->request->getQuery('type');

		$condition['isDel'] = 1;

		$excel = Yii::app()->request->getQuery('excel');
		$exportExcel = ( $excel === 'exportExcel' )?true:false;

		$model = new tbDeliveryOrder();
		$data = $model->serach( $condition ,20,$exportExcel );

		$data['from'] = Yii::app()->request->url;
		$condition['excel'] = 'exportExcel';
		$data['excelUrl'] = $this->createUrl( 'index', $condition );
		$this->render( 'closelist',array_merge( $data,$condition ) );
	}


	/**
	* @access 导入送货单
	*/
	public function actionImport(){
		$this->temp();

		$model = new tbDeliveryOrder();
		if( Yii::app()->request->isPostRequest ) {
			if( $model->Import() ) {
				$this->dealSuccess( $this->createUrl('index') );
			}else{
				$this->dealError( $model->getErrors() );
			}
		}

		$this->render( 'import' );
	}

	private function temp(){
		$type = Yii::app()->request->getQuery('type');
		if( $type !== 'temp' ) return;

		Yii::$enableIncludePath = false;
		Yii::import('libs.vendors.phpexcel.*');

		$ExcelFile = new ExcelFile( '数据模板' );
		$saveData = array(
						array('收货人','固话','手机号码','订单地址','商品标题','客户留言','商家备注','isTitle'=>true),
						array('收货人','','手机110','订单地址01','商品01','客户留言01','商家备注01'),
						array('收货人','','手机120','订单地址02','商品02','客户留言02','商家备注02'),
					);

		/* $saveData = array(
						array('收货人','固话','手机号码','订单地址','商品','客户留言','收货人地址','数量','配送','送货员','商品标题','客户留言','预约送货时间','商家备注','isTitle'=>true),
						array('收货人','固话110','手机110','订单地址01','商品01','客户留言01','2','已送','送货员','商品标题','客户留言','订单地址','商家备注','2013-08-12'),
						array('收货人','固话110','手机120','订单地址02','商品02','客户留言02','1','待送','送货员','商品标题','客户留言','订单地址','商家备注','2013-08-12'),
					); */
		$ExcelFile->createMergeExl( $saveData );
		exit;
	}



	/**
	* @access 新增送货单
	*/
	public function actionAdd(){
		$model = new tbDeliveryOrder();
		$this->addEdit( $model );
	}


	/**
	* @access 编辑送货单
	*/
	public function actionEdit( $id ){
		$model = tbDeliveryOrder::model()->findByPk( $id );
		if ( !$model ) {
			$this->redirect( $this->createUrl( 'index' ) );
		}
		$this->addEdit( $model );
	}

	/**
	* @access 保存送货单信息
	*/
	private function addEdit( $model ){
		$mems = tbDeliveryman::model()->getDeliverymans();
		$areas = tbDeliveryArea::model()->getDeliveryAreas();
		$states = $model->getStates();

		if( Yii::app()->request->isPostRequest ) {
			$oState = $model->state;//原来的状态

			$model->attributes = Yii::app()->request->getPost('data');

			if( !array_key_exists( $model->state ,$states ) ){
				$model->state =0;
			}

			if( !array_key_exists( $model->deliverymanId ,$mems ) ){
				$model->deliverymanId = $model->deliverymanTitle = '';
			}else{
				$model->deliverymanTitle = $mems[$model->deliverymanId];
			}

			if( !array_key_exists( $model->areaId ,$areas ) ){
				$model->areaId = $model->areaTitle = '';
			}else{
				$model->areaTitle = $areas[$model->areaId];
			}

			if( $model->appointment == '') {
				$model->appointment = '0000-00-00 00:00:00';
			}

			//如果原来的状态不是已配送，更改后的状态为已配送，记录配送时间为当前时间
			if( $oState!= '1' && $model->state == '1' ){
				$model->deliveryTime = new CDbExpression('NOW()');
			}

			if( $model->save() ){
				$from = urldecode(Yii::app()->request->getQuery('from'));
				if( !empty( $from ) ){
					$url = $from;
				}else{
					$url = Yii::app()->request->urlReferrer ;
				}

				$this->dealSuccess($url);
			}else{
				$errors = $model->getErrors();
				$this->dealError( $errors );
			}
		}

		if( $model->appointment == '0000-00-00 00:00:00') {
			$model->appointment = '';
		}else{
			$model->appointment = date( 'Y-m-d',strtotime( $model->appointment ) );
		}

		$data = $model->attributes;
		$data['mems'] = $mems;
		$data['areas'] = $areas;
		$data['states'] = $states;

		if( $model->id ){
			$data['ops'] =  tbDeliveryOrderOp::model()->getAllops( $model->id );
		}else{
			$data['ops'] = array();
		}

		$this->render( 'add', $data );
	}


	/**
	* @access 删除送货单
	*/
	public function actionDel( $id ){
		if( is_numeric($id) && $id>0 ){
			tbDeliveryOrder::model()->updateByPk( $id,array('isDel'=>'1')  );
		}
		$this->dealSuccess( Yii::app()->request->urlReferrer );
	}

	/**
	* @access 恢复送货单
	*/
	public function actionUndel( $id ){
		if( is_numeric($id) && $id>0 ){
			tbDeliveryOrder::model()->updateByPk( $id,array('isDel'=>'0')  );
		}
		$this->dealSuccess( Yii::app()->request->urlReferrer );
	}

	/**
	* post结果处理 success
	* @param $url 跳转到的地址
	*/
	public function dealSuccess($url=null){
		if( Yii::app()->request->getIsAjaxRequest() ) {
			$json=new AjaxData(true,null,$url);
			echo $json->toJson();
			Yii::app()->end(200);
		} else {
			if($url)
				$this->redirect($url);
		}
	}

	/**
	* @access 批量设置送货员
	*/
	public function actionSetdeliveryman(){
		$ids = Yii::app()->request->getPost('ids');
		$ids = explode( ',',$ids );

		$deliverymanId = Yii::app()->request->getPost('deliverymanId');
		$deliverymans = tbDeliveryman::model()->getDeliverymans();
		if( !empty($ids) && array_key_exists( $deliverymanId , $deliverymans ) ){
			tbDeliveryOrder::model()->updateByPk( $ids,array( 'deliverymanId'=>$deliverymanId,'deliverymanTitle'=>$deliverymans[$deliverymanId] )  );
		}

		$this->dealSuccess( Yii::app()->request->urlReferrer );
	}


	/**
	* @access 批量设置片区
	*/
	public function actionSetarea(){
		$ids = Yii::app()->request->getPost('ids');
		$ids = explode( ',',$ids );

		$areaId = Yii::app()->request->getPost('areaId');
		$areas = tbDeliveryArea::model()->getDeliveryAreas();
		if( !empty($ids) && array_key_exists( $areaId , $areas ) ){
			tbDeliveryOrder::model()->updateByPk( $ids,array( 'areaId'=>$areaId,'areaTitle'=>$areas[$areaId] )  );
		}

		$this->dealSuccess( Yii::app()->request->urlReferrer );
	}

	/**
	* @access 批量设置片区
	*/
	public function actionSetsend(){
		$ids = Yii::app()->request->getPost('ids');
		if( !empty($ids) ){
			tbDeliveryOrder::model()->updateByPk( $ids,array('state'=>'1','deliveryTime'=>date('Y-m-d H:i:s')),'state != 1'  );
		}
		$this->dealSuccess( Yii::app()->request->urlReferrer );
	}

	/**
	* @access 批量设置片区
	*/
	public function actionSetdel(){
		$ids = Yii::app()->request->getPost('ids');

		if( !empty($ids) ){
			tbDeliveryOrder::model()->updateByPk( $ids,array('isDel'=>'1'),'isDel = 0'  );
		}
		$this->dealSuccess( Yii::app()->request->urlReferrer );
	}

	/**
	* @access 送货统计
	*/
	public function actionStatistics(){
		$deliverymanId = Yii::app()->request->getQuery('deliverymanId');
		$t1 = Yii::app()->request->getQuery('t1',date('Y-m-d'));
		$t2 = Yii::app()->request->getQuery('t2',date('Y-m-d'));


		if( is_null( $deliverymanId  ) ){
			$list = array();
		}else{
			$model = new tbDeliveryOrder();
			$list = $model->statistics( $deliverymanId, $t1,$t2 );
			$errors = $model->getErrors();
			if( !empty( $errors ) ){
				$this->dealError( $errors );
			}
		}

		$mems = tbDeliveryman::model()->getDeliverymans();
		unset( $mems['0'] );
		$this->render( 'statistics',array('mems'=>$mems, 'deliverymanId'=>$deliverymanId,'t1'=>$t1,'t2'=>$t2,'list'=>$list,) );

	}

}