<?php
/**
 * 系统消息
 * @author liang
 * @version 0.1
 * @package Controller
 */
class MessageController extends Controller {

	public function init() {
		parent::init();

		if( empty( $this->memberId ) ) {
			$this->message = Yii::t('user','You do not log in or log out');
			$this->showJson();
		}
	}

	/**
	 * 系统消息列表
	 */
	public function actionIndex(){

		$criteria=new CDbCriteria;
		$criteria->compare('memberId',$this->memberId);
		$criteria->compare('state',array(0,1));

		$model = new CActiveDataProvider('tbMessage',array(
					'criteria'=>$criteria,
					'pagination'=>array('pageSize'=>8,'pageVar'=>'page'),
				));
		$data = $model->getData();
		$return['list'] = array_map(function ($i){
							$i->createTime = date('Y/m/d H:i',strtotime( $i->createTime ));
							return $i->attributes;},$data);
		$pages = $model->getPagination();
		$return['page'] = $pages->currentPage + 1;
		$return['totalpage'] = ceil($pages->itemCount/$pages->pageSize);

		$this->data = $return;
		$this->state = true;
		$this->showJson();
	}


	/**
	* 更改信息状态:未读->已读
	* @param integer $id 消息ID
	*/
	public function actionUpdate( $id ){
		$c = tbMessage::model()->updateByPk($id,array('state'=>'1'),'memberId=:memberId and state = 0',array(':memberId'=>$this->memberId));
		if($c) {
			$this->state = true;
		}
		$this->showJson();
	}


	/**
	* 删除会员地址
	* @param integer $id 地址ID
	*/
	public function actionDelete( $id ){
		$c = tbMessage::model()->updateByPk($id,array('state'=>'2'),'memberId=:memberId',array(':memberId'=>$this->memberId));
		if($c) {
			$this->state = true;
		}
		$this->showJson();
	}
}