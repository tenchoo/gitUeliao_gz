<?php
/**
 * 订单分拣管理
 * @access 分拣管理
 * @author liang
 * @package Controller
 * @version 0.1
 *
 */
class PackingController extends Controller {

	/**
	 * 待分拣订单,从订单表读数据
	 * @access 待分拣订单
	 */
	public function actionIndex() {
		$condition['warehouseId'] = Yii::app()->request->getQuery('warehouseId');
		$condition['orderId'] = Yii::app()->request->getQuery('orderId');
		$condition['singleNumber'] = '';//Yii::app()->request->getQuery('singleNumber');
		$condition['state'] = '0';

		$Packing = new Packing();
		$data = $Packing->waitPackingList( $condition,'t.createTime asc');

		$data['warehouse'] = tbWarehouseInfo::model()->getAll();
		$this->render('index',array_merge($data,$condition));
	}


	/**
	* 分拣
	* @access 分拣
	* @param integer id 订单ID，针对订单分拣
	*/
	public function actionPacking(){
		$id = Yii::app()->request->getQuery('id');
		$model = tbPacking::model()->with('order','distribution')->findByPk( $id,'t.state=0 ');

		if( !$model || empty($model->order) ){
			throw new CHttpException(404,"the require obj has not exists.");
		}

		$dataArr = $unitRate = array();
		$Packing = new Packing();
		if( Yii::app()->request->isPostRequest ){
			$cancle = Yii::app()->session->get('packing_cancle');
			if( $cancle && $cancle == $model->orderId ){
				$result = $Packing->cancle( $model );
				Yii::app()->session->add('packing_cancle',null);
			}else{
				$dataArr = Yii::app()->request->getPost('pack');
				$unitRate = Yii::app()->request->getPost('unitRate');
				$result = $Packing->save( $dataArr,$model,$unitRate);
			}

			if( $result ) {
				$this->dealSuccess( $this->createUrl( 'index' ) );
			} else {
				$errors = $Packing->getErrors();
				$this->dealError( $errors );
			}
		}

		$warehouse = tbWarehouseInfo::model()->getAll();
		$data = $Packing->setData($model,$warehouse,$unitRate);

		$order = new Order();
		$data['deliveryMethod'] = $order->deliveryMethod( $model->order->deliveryMethod );

		$data['applyInfo'] = null;
		if( $data['orderState'] == '7' ){
			Yii::app()->session->add('packing_cancle',$data['orderId']);
		}else{
			$data['applyInfo'] = tbWarehouseMessage::model()->getWMessage( $data['orderId'],$model->warehouseId );
		}

		$this->render('packing',array(	'data'=>$data,'dataArr'=>$dataArr));
	}

	/**
	 * 分拣单管理,从分拣单表读数据
	 * @access 分拣单管理
	 */
	public function actionList() {
		$condition['orderId'] = Yii::app()->request->getQuery('orderId');
		$condition['singleNumber'] = '';//Yii::app()->request->getQuery('singleNumber');
		$condition['string'] ='t.state>0';
		$Packing = new Packing();
		$data = $Packing->search( $condition );
		$this->render('list',array_merge($data,$condition));
	}


	/**
	* 查看分拣单
	* @access 查看分拣单
	* @param integer id 分拣单ID
	*/
	public function actionView(){
		$id = Yii::app()->request->getQuery('id');
		$model = tbPacking::model()->with('order','distribution','operator')->findByPk( $id );
		if( !$model || empty($model->order) ){
			throw new CHttpException(404,"the require obj has not exists.");
		}

		$Packing = new Packing();
		$warehouse = tbWarehouseInfo::model()->getAll();
		$data = $Packing->setData($model,$warehouse);
		$order = new Order();
		$data['deliveryMethod'] = $order->deliveryMethod( $model->order->deliveryMethod );
		$this->render('view',$data);
	}


	/**
	* 打印标识，显示打印内容
	* @access 打印标识
	* @param integer id 分拣单ID
	*/
	public function actionPrint(){
		$id = Yii::app()->request->getQuery('id');
		$state = PrintPush::printPacking( $id,$msg );
		if( Yii::app()->request->getIsAjaxRequest() ) {
			$json = new AjaxData( $state , $msg );
			echo $json->toJson();
		}else{
			echo $msg;
		}
		Yii::app()->end();
	}



	/* public function actionPrint(){
		$id = Yii::app()->request->getQuery('id');
		$model = tbPacking::model()->with('detail')->findByPk( $id );
		if( !$model || empty($model->order) ){
			throw new CHttpException(404,"the require obj has not exists.");
		}

		$memberId = tbOrder::model()->findByPk( $model->orderId)->memberId;
		$member = tbProfileDetail::model()->findByPk( $memberId );
		$data['companyname'] = ($member)?$member->companyname:'';

		$data['details'] =  array();
		foreach ( $model->detail as $dval ){
			if(!isset( $data['details'][$dval->orderProductId] ) ){
				$data['details'][$dval->orderProductId] =  $dval->getAttributes(array('orderProductId','singleNumber','color','packingNum','unitRate'));
				if( $data['details'][$dval->orderProductId]['unitRate']<1){
					$data['details'][$dval->orderProductId]['unitRate'] = 1;
				}

			}else{
				$data['details'][$dval->orderProductId]['packingNum'] += $dval->packingNum;
			}
		}

		foreach ( $data['details'] as &$val){
			$val['bulkNumber'] =  Order::unitMod( $val['packingNum'], $val['unitRate'] );
			$val['batchNumber'] = bcsub( $val['packingNum'], $val['bulkNumber'],0);
			$val['batchs'] = floor($val['packingNum']/$val['unitRate']);
			$val['bulk'] = ($val['bulkNumber']>0)?1:0;

		}
		$this->render('print',$data);
	} */

	/**
	* 打印标识，打印格式，确认打印
	* @access 打印
	*/
	public function actionPrint2(){
		$companyname = Yii::app()->request->getPost('companyname');
		$details = Yii::app()->request->getPost('details');
		$details = json_decode($details,true);
		if(empty($details)){
			$this->redirect('/');
		}
		$this->render('print2',array('companyname'=>$companyname,'details'=>$details));
	}


	/**
	* 分拣管理---分拣调度  仓库管理员的功能
	* @access 分拣调度
	*/
	public function actionScheduling(){
		//step1 判断当前账号是否为管理员
		$model =  new WarehouseManager();
		$warehouseId = $model->ManageWarehouse();

		if( empty( $warehouseId ) ){
			exit(  '只有仓库管理员才能进行调度' );
		}

		$Packing = new PackingForm();
		$areas = tbWarehousePosition::model()->getAllZoning( $warehouseId,2 );
		if( Yii::app()->request->isPostRequest ){
			if( $Packing->scheduling( $areas ) ){
				$this->dealSuccess( $this->createUrl( 'scheduling' ) );
			}else{
				$errors = $Packing->getErrors();
				$this->dealError( $errors );
			}
		}

		$condition['orderId'] = Yii::app()->request->getQuery('orderId');
		$condition['singleNumber'] = Yii::app()->request->getQuery('singleNumber');
		$condition['state'] = 0;
		$condition['warehouseId'] = $warehouseId;
		$data = $Packing->search( $condition );
		$data['areas'] = $areas;
		$data['user'] = tbUser::model()->getUsername(Yii::app()->user->id);
		$this->render('scheduling',array_merge($data,$condition));
	}

	/**
	* 分拣管理---待分拣列表  仓库分拣员的功能
	* 如果是仓库管理员，可以查看全部列表，
	* 如果是区域，可以查看此区域内的全部待分拣记录
	* @access 待分拣列表
	*/
	public function actionWaitpacking(){
		//step1 查找当前账号的服务区域,
		$models = tbWarehouseUser::model()->findAll('userId=:userId',array(':userId'=>Yii::app()->user->id ) );
		if( empty( $models ) ){
			exit(  '您不是仓库人员，无权限查看此页面' );
		}

		$pids = array();
		foreach ( $models as $val ){
			$warehouseId = $val->warehouseId;
			if( $val->positionId ){
				$pids[] = $val->positionId;
			}
		}

		if( !empty( $pids ) ){
			$condition['positionId'] = $pids;
		}

		$Packing = new PackingForm();
		$areas = tbWarehousePosition::model()->getAllZoning( $warehouseId,2 );
		$condition['orderId'] = Yii::app()->request->getQuery('orderId');
		$condition['singleNumber'] = Yii::app()->request->getQuery('singleNumber');
		$condition['state'] = 0;
		$condition['warehouseId'] = $warehouseId;
		$data = $Packing->search( $condition );
		$data['areas'] = $areas;
		$data['user'] = tbUser::model()->getUsername(Yii::app()->user->id);

		$this->render('scheduling',array_merge($data,$condition));
	}

	/**
	* @access 确定分拣
	*/
	public function actionConfirmSort(){
			if( Yii::app()->request->isPostRequest ){
				$positionId = Yii::app()->request->getPost('positionId');
				$orderProductId = Yii::app()->request->getPost('orderProductId');
				$unit = Yii::app()->request->getPost('unit');
				$int = Yii::app()->request->getPost('int');
				$remainder = Yii::app()->request->getPost('remainder');
				$singleNumber = Yii::app()->request->getPost('singleNumber');
				//var_dump($singleNumber);die;
				$packing  = new PackingForm();
				if($packing->confirm($positionId,$orderProductId,$unit,$int,$remainder,$singleNumber,$packDetail)){
					$this->dealSuccess( $this->createUrl( 'waitpacking' ) );
				}else{
					$this->dealError( $packing->getErrors() );
					exit(  '确定分拣失败' );
				}



			//	$remark = Yii::app()->request->getPost('remark','test');//订单留言
			// 	$positionTitle= tbWarehousePosition::model()->getWarehouseTitle($positionId[0]);

			// 	//计算整数分拣量
			// 	$intPackingNum = bcmul($unit,$int);
			// 	//添加整料
			// 	$packDetail = new tbPackDetail();
			// 	$packDetail->positionId = $positionId[0];
			// 	$packDetail->orderProductId = $orderProductId;
			// //	$positionTitle= tbWarehousePosition::model()->getWarehouseTitle($positionId[0]);
			// //	$packDetail->remark = $remark;
			// 	$packDetail->positionTitle =tbWarehousePosition::model()->getWarehouseTitle($positionId[0]);
			// 	$packDetail->packingNum = $intPackingNum;
			// 	$packDetail->wholes = 1;
			// 	if( !$packDetail->validate() ){
			//      $errors = $packDetail->getErrors();
			// 		 $this->dealError( $errors );
			// 		 throw new CHttpException( '404',$errors['positionId'][0] );
		 //     }
			// 	//$transaction = Yii::app()->db->beginTransaction();
			// 	if( !$packDetail->save() ){
			// 		//$transaction->rollback();
			// 		$errors = $packDetail->getErrors();
			// 		$this->dealError( $errors );
			// 	}
   //    $packDetail = new tbPackDetail();
   //     //添加零码
   //       foreach ( $remainder as $key => $value ) {
   //       	   $_packDetail = clone $packDetail;
   //       	   $_packDetail->positionId = $positionId[1];
			// 	     $_packDetail->orderProductId = $orderProductId;
			// 	     $_packDetail->positionTitle =tbWarehousePosition::model()->getWarehouseTitle($positionId[1]);
			// 	     $_packDetail->wholes = 0;
			// 	     $_packDetail->packingNum = $value;				     if(!$_packDetail->save()){
			// 					 //$transaction->rollback();
			// 					 $errors = $_packDetail->getErrors();
			// 					 $this->dealError( $errors );
			// 				}
   //       }
        // $transaction->commit();
       //  $this->dealSuccess( $this->createUrl( 'waitpacking' ) );

			}

	}

	/**
	* 分拣管理---分拣记录  仓库分拣员的功能
	* 如果是仓库管理员，可以查看全部列表，
	* 如果是区域，可以查看此区域内的全部待分拣记录
	* @access 分拣记录
	*/
	public function actionPacklist(){
		//step1 查找当前账号的服务区域,
		$models = tbWarehouseUser::model()->findAll('userId=:userId',array(':userId'=>Yii::app()->user->id ) );

		if( empty( $models ) ){
			exit(  '您不是仓库人员，无权限查看此页面' );
		}

		$pids = array();
		foreach ( $models as $val ){
			$warehouseId = $val->warehouseId;
			if( $val->positionId ){
				$pids[] = $val->positionId;
			}
		}

		if( !empty( $pids ) ){
			$condition['positionId'] = $pids;
		}


		$Packing = new PackingForm();
		$areas = tbWarehousePosition::model()->getAllZoning( $warehouseId,2 );
		$condition['orderId'] = Yii::app()->request->getQuery('orderId');
		$condition['singleNumber'] = Yii::app()->request->getQuery('singleNumber');
		$condition['state'] = array(1,2);
		$condition['warehouseId'] = $warehouseId;
		$data = $Packing->search( $condition );
		$data['areas'] = $areas;
		$this->render('packlist',array_merge($data,$condition));
	}


}