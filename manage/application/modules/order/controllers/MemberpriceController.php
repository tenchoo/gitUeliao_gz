<?php
/**
* 批发价格申请管理
* @access  批发价格管理
* @author liang
* @package Controller
* @version 0.1
*/
class MemberpriceController extends Controller {


	/**
	* 批发价格申请管理
	* @access 批发价格申请管理
	*/
	public function actionIndex() {
		$state = Yii::app()->request->getQuery('state');
		$keyword = trim( Yii::app()->request->getQuery('keyword') );

		$condition = array( 'isDel'=>array(0) );
		if( $state ){
			$condition['state'] = $state;
		}

		if( $keyword ){
			if( is_numeric( $keyword ) ){
				$condition['applyPriceId'] = $keyword;
			}else{
				$condition['companyName'] = $keyword;
			}
		}

		$data = tbMemberApplyPrice::model()->search( $condition ,10 );
		$data['state'] = $state;
		$data['keyword'] = $keyword;

		$this->render( 'index' ,$data);
	}

	/**
	* @access 批发价格审核
	*/
	public function actionCheck(){
		$id =  Yii::app()->request->getQuery('id');
		$model = tbMemberApplyPrice::model()->with('saleman')->findByPk( $id,'state = :state',array( ':state'=>'0' ) );

		if( !$model ){
			throw new CHttpException(404,"the require obj has not exists.");
		}

		if( Yii::app()->request->isPostRequest ){
			//增加操作记录
			$op = new tbMemberApplyPriceOp();

			$oldPrice  = $model->applyPrice;
			$model->applyPrice =  Yii::app()->request->getPost('applyPrice');
			$model->applyPrice = str_replace(',','',$model->applyPrice );

			if( $oldPrice != $model->applyPrice ){
				$op->remark = ' 批发价格由'.$oldPrice.'修改为'.$model->applyPrice.'; ';
			}

			$model->state = Yii::app()->request->getPost('state'); //编辑后状态设为待审核
			$op->remark .= Yii::app()->request->getPost('remark');

			switch ( $model->state ){
				case '1':
					$op->code = 'pass';

					break;
				case '2':
					$op->code = 'notpass';
					break;
				default:
					 $model->state = '';
			}

			$op->isManage = 1;
			$op->applyPriceId = $model->applyPriceId;
			$op->userId = Yii::app()->user->id;

			if( $model->save() ){
				$op->save();
				$this->dealSuccess( $this->createUrl('index') );
			}else{
				$this->dealError( $model->errors );
			}
		}

		$product =  $model->product->getAttributes( array('unitId','title','serialNumber','mainPic','price') );
		$data = array_merge( $model->attributes, $product );

		$data['unitName'] = '';
		$unit =  tbUnit::model()->findByPk ( $product['unitId'] );
		if( $unit ){
			$data['unitName'] = $unit->unitName;
		}
		$data['saleman'] = ( $model->saleman )?$model->saleman->username:'';

		$data['companyname'] = ( $model->company )? $model->company->shortname:'';
		$this->render( 'check',$data );
	}


	/**
	* @access 申请失效
	* 只有审核通过的产品才能申请失效
	*/
/* 	public function actionInvalid(){
		$id =  Yii::app()->request->getQuery('id');
		$model = new tbMemberApplyPrice();
		if( $model->invalid( $id ) ){
			$url = Yii::app()->request->urlReferrer;
			$this->dealSuccess( $url );
		}else{
			$this->dealError( $model->errors );
		}
	} */

	/**
	* @access 删除申请
	*/
	public function actionDel(){
		$id =  Yii::app()->request->getQuery('id');
		$model = new tbMemberApplyPrice();
		if( $model->del( $id ) ){
			$url = Yii::app()->request->urlReferrer;
			$this->dealSuccess( $url );
		}else{
			$this->dealError( $model->errors );
		}
	}

	/**
	* @access 批发价格查看
	*/
	public function actionView(){
		$id =  Yii::app()->request->getQuery('id');
		$model = tbMemberApplyPrice::model()->with('saleman')->findByPk( $id );

		if( !$model ){
			throw new CHttpException(404,"the require obj has not exists.");
		}

		$product =  $model->product->getAttributes( array('unitId','title','serialNumber','mainPic','price') );
		$data = array_merge( $model->attributes, $product );

		$data['unitName'] = '';
		$unit =  tbUnit::model()->findByPk ( $product['unitId'] );
		if( $unit ){
			$data['unitName'] = $unit->unitName;
		}
		$data['saleman'] = ( $model->saleman )?$model->saleman->username:'';
		$data['companyname'] = ( $model->company )? $model->company->shortname:'';
		$data['oplog'] =  tbMemberApplyPriceOp::getOP( $id );
		$this->render( 'view',$data );
	}



}
