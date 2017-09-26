<?php
/**
 * 仓库---产品调整单管理
 * @access 产品调整单管理
 * @author liang
 * @package Controller
 * @version 0.1
 * @date 2016-07-07
 *
 */
class AdjustController extends Controller {

	/**
	 * 产品调整单
	 * @access 产品调整单
	 */
	public function actionIndex() {
		$condition['createTime1'] = Yii::app()->request->getQuery('createTime1');
		$condition['createTime2'] = Yii::app()->request->getQuery('createTime2');
		$condition['singleNumber'] = trim( Yii::app()->request->getQuery('singleNumber') );
		$condition['adjustId'] = trim(Yii::app()->request->getQuery('adjustId'));
		$AdjustForm = new AdjustForm();
		$pageSize = tbConfig::model()->get( 'page_size' );
		$data = $AdjustForm->search( $condition,$pageSize );
		$this->render('index',array_merge( $data,$condition ));
	}

	/**
	 * 查看产品调整单
	 * @access 查看产品调整单
	 */
	public function actionView() {
		$id = Yii::app()->request->getQuery('id');

		$model = null;
		if( is_numeric($id) && $id>0 ){
			$model = tbWarehouseAdjust::model()->with('detail')->findByPk( $id );
		}

		if( !$model ){
			$this->redirect( $this->createUrl( 'index' ) );
		}
		$data = $model->attributes;
		$data['username'] = tbUser::model()->getUsername( $model->userId );
		$data['unit']	= ZOrderHelper::getUnitName( $model->singleNumber );

		$warehouse = tbWarehouseInfo::model()->getAll();
		foreach( $model->detail as $val ){
			$detail = $val->attributes;
			$detail['positionName'] =  tbWarehousePosition::model()->positionName( $val->positionId,$warehouseId );
			$detail['warehouse'] = array_key_exists( $warehouseId,$warehouse )?$warehouse[$warehouseId]:'';
			$data['detail'][] = $detail;
		}

		$this->render( 'view',$data );
	}

	/**
	 * 创建产品调整单
	 * @access 创建产品调整单
	 */
	public function actionAdd() {
		$data['singleNumber'] = Yii::app()->request->getQuery('singleNumber');

		$AdjustForm = new AdjustForm();
		$AdjustForm->singleNumber = $data['singleNumber'];

		if( Yii::app()->request->isPostRequest ){			
			$AdjustForm->remark = Yii::app()->request->getPost('remark','');
			if( $AdjustForm->add() ) {
				$this->dealSuccess( $this->createUrl( 'index' ) );
			} else {
				$errors = $AdjustForm->getErrors();
				$this->dealError( $errors );
			}
		}

		$data['adjustInfo'] = $AdjustForm->getAdjustInfo();
		if( empty( $data['adjustInfo'] ) ){
			$errors = $AdjustForm->getErrors();
			$this->dealError( $errors );
		}
		
		$this->render('add',$data );
	}

}