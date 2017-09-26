<?php
/**
* 物流接口管理
* @access 物流接口
* @author liang
* @package Controller
* @version 0.1
*/
class LogisticsController extends Controller {

	/**
	 * 物流接口
	 * @access 物流接口
	 */
	public function actionIndex() {
		$keyword = Yii::app()->request->getQuery('keyword');
		$data = tbLogistics::search( $keyword );
		$this->render( 'index',array( 'list'=>$data['list'],'keyword'=>$keyword,'pages'=>$data['pages'],'page'=>$keyword ) );
	}

	/**
	* 添加/编辑物流接口
	* @access 添加/编辑物流接口
	*/
	public function actionAddedit(){
		$logisticsId = Yii::app()->request->getQuery('logisticsId');
		if( $logisticsId ){
			$model = tbLogistics::model()->findByPk( $logisticsId );
			if( !$model ){
				throw new CHttpException(404,"the require obj has not exists.");
			}
		}else{
			$model = new tbLogistics();
		}

		if( Yii::app()->request->isPostRequest ){
			$model->attributes = Yii::app()->request->getPost('data');
			if( $model->save() ) {
				$from = Yii::app()->request->getQuery('from');
				if( $from ){
					$url = urldecode($from);
				}else{
					$url = $this->createUrl( 'index' );
				}
				$this->dealSuccess( $url  );
			} else {
				$errors = $model->getErrors();
				$this->dealError( $errors );
			}
		}

		$this->render( 'addedit',array( 'data'=>$model->attributes ) );
	}


	/**
	 * 删除物流接口
	 * @access 删除物流接口
	 */
	public function actionDel() {
		$logisticsId = (int) Yii::app()->request->getQuery('id');
		tbLogistics::model()->updateByPk( $logisticsId ,array('isDel'=>'1') );
		$this->dealSuccess( Yii::app()->request->urlReferrer );
	}
}
