<?php
/**
 * 财务收款管理--撤销收款
 * @access 财务收款管理
 * @author liang
 * @package Controller
 * @version 0.1
 *
 */
class UndoController extends Controller {

	/**
	 * @access 待审核撤销收款
	 */
	public function actionIndex() {
		$model = new ReceivablesForm();
		$data = $model->undoList( array('state'=>0) );
		$this->render( 'index' ,$data );
	}

	/**
	 * @access 已审核撤销收款
	 */
	public function actionList() {
		$conditon['memberId'] = Yii::app()->request->getQuery('memberId');
		if( !is_numeric( $conditon['memberId'] ) || $conditon['memberId'] <= '0' ){
			$conditon['memberId'] = $memberName = '';
		}else{
			$memberName =  Yii::app()->request->getQuery('memberName');
		}
		$conditon['t1'] = Yii::app()->request->getQuery('t1');
		$conditon['t2'] = Yii::app()->request->getQuery('t2');
		$conditon['state'] = array(1,2);

		$model = new ReceivablesForm();
		$data = $model->undoList( $conditon );
		$data['memberName'] = $memberName;
		$this->render( 'list' ,array_merge( $data,$conditon) );
	}

	/**
	 * @access  申请撤销收款
	 */
	public function actionAdd() {
		$id = Yii::app()->request->getQuery('id');
		$form = new ReceivablesForm();
		$data = $form->getUndoInfo( $id );
		if( !$data ){
			throw new CHttpException( '404', 'Not found record' );
		}

		if( Yii::app()->request->isPostRequest && empty( $data['undoModel'] ) ) {
			if( $form->undo( $data['recordsId'] ) ) {
				if(!$url = urldecode(Yii::app()->request->getQuery('from'))){
					$url = $this->createUrl('add',array('id'=>$id));
				}
				$this->dealSuccess( $url );
			}else{
				$this->dealError( $form->getErrors() );
			}
		}
		$this->render( 'add',$data );
	}


	/**
	 * @access  撤销申请审核
	 */
	public function actionCheck() {
		$id = Yii::app()->request->getQuery('id');
		$model = null;
		if( is_numeric( $id ) && $id>0 ){
			$model = tbDepositRecordsUndo::model()->findByPk( $id ,'state = 0 ' );
		}

		if( !$model ){
			$this->redirect( $this->createUrl('index') );
		}

		$data = $model->attributes;

		$records = $model->records;
		$data['records'] = $records->attributes;
		$member = $records->member;
		$data['companyname'] = $member->companyname;

		$form = new ReceivablesForm();
		if( Yii::app()->request->isPostRequest ) {
			if( $form->check( $model ) ) {
				if(!$url = urldecode(Yii::app()->request->getQuery('from'))){
					$url = $this->createUrl('index');
				}
				$this->dealSuccess( $url );
			}else{
				$this->dealError( $form->getErrors() );
			}
		}
		$this->render( 'check',$data );
	}

	/**
	 * @access  查看撤销申请审核
	 */
	public function actionView() {
		$id = Yii::app()->request->getQuery('id');
		$model = null;
		if( is_numeric( $id ) && $id>0 ){
			$model = tbDepositRecordsUndo::model()->findByPk( $id );
		}

		if( !$model ){
			$this->redirect( $this->createUrl('index') );
		}

		$data = $model->attributes;
		$data['stateTitle'] = $model->stateTitle();
		$records = $model->records;
		$data['records'] = $records->attributes;
		$member = $records->member;
		$data['companyname'] = $member->companyname;
		$this->render( 'view',$data );
	}



}