<?php
/**
 * 短信管理
 * @access 短信管理
 * @author liang
 * @package Controller
 * @version 0.1
 *
 */
class PhonesmsController extends Controller {

	/**
	 * 短信列表
	 * @access 短信列表
	 */
	public function actionIndex() {
		$data['account'] =  Yii::app()->request->getQuery('account');
		$c = new CDbCriteria();

		if( $data['account'] ){
			$c->compare('t.account',$data['account']);
		}
		$c->order ='createTime desc';
		$pageSize =  tbConfig::model()->get('page_size');
		$model = new CActiveDataProvider('tbPhoneLog', array(
				'criteria'=>$c,
				'pagination'=>array('pageSize'=>$pageSize,'pageVar'=>'page'),
			));
		$data['list'] = $model->getData();
		$data['pages'] = $model->getPagination();
		$this->render( 'index' ,$data );
	}

	/**
	 * 接口配置
	 * @access 短信接口配置
	 */
	public function actionConfig() {
		$model = tbConfig::model()->findByAttributes( array('key'=>'mobile_sms'));
		$phoneConfig = new phoneConfig();

		if( Yii::app()->request->isPostRequest ){
			$phoneConfig->attributes = Yii::app()->request->getPost('data');
			if( $phoneConfig->save( $model ) ) {
				$this->dealSuccess( $this->createUrl( 'config' ) );
			} else {
				$errors = $phoneConfig->getErrors();
				$this->dealError( $errors );
			}
		}else{
			$phoneConfig->attributes = unserialize( $model->value );
		}


		$this->render( 'config' ,array('data'=>$phoneConfig->attributes) );
	}


	/**
	 * 短信模板
	 * @access 短信模板
	 */
	public function actionTem() {
		$c = new CDbCriteria;
		$c->compare('t.`type`','sms');
		$c->addNotInCondition('t.`key`', array('mobile_sms','sms_default'));//与上面正好相法，是NOT IN

		$model = tbConfig::model()->findAll( $c );

		if( Yii::app()->request->isPostRequest ){
			$data = Yii::app()->request->getPost('data');
			foreach ( $model as $val ){
				if(! array_key_exists( $val->key,$data ) ) continue;
				$val->value = trim( $data[$val->key] );

				if( ! strpos($data[$val->key], '{code}') ){
					$msg = $val->comment.Yii::t('base','must include').' {code}';
					$this->dealError( array('value'=>array('0'=>$msg)) );
					break;
				}
				if( !$val->save()){
					$this->dealError(  $val->getErrors() );
					break;
				}
			}
		}
		$this->render( 'tem' ,array('model'=>$model) );
	}



}