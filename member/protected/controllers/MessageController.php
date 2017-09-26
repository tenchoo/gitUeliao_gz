<?php
/**
 * 会员消息页面
 * @author morven
 * @package member
 */
class MessageController extends Controller {
	
	public function init() {
		parent::init();
		$this->routeFlag = "member::/message";
	}

	/**
	 * 消息列表
	 */
	public function actionIndex() {
		//查询条件
		$criteria=new CDbCriteria;
		$criteria->compare('memberId',Yii::app()->user->id);
		$criteria->compare('state',array(0,1));

		$model = new CActiveDataProvider('tbMessage',array(
					'criteria'=>$criteria,
					'pagination'=>array('pageSize'=>20),
				));
		$data = $model->getData();
		$pages = $model->getPagination();
		$this->render('index',array('model'=>$data,'pages'=>$pages));
	}

	/**
	 * 物流消息
	 */
	public function actionLogistics() {
		//查询条件
		$criteria=new CDbCriteria;

		$criteria->compare('t.isDel',0);
		$criteria->join = 'inner join {{order}} t2 on(t.orderId=t2.orderId and t2.memberID= '.Yii::app()->user->id.')'; //连接表
		$criteria->with ='product';
		$model = new CActiveDataProvider('tbLogisticsMessage',array(
					'criteria'=>$criteria,
					'pagination'=>array('pageSize'=>20),
				));
		$data = $model->getData();
		$pages = $model->getPagination();
		$this->render('logistics',array('model'=>$data,'pages'=>$pages));
	}



	/**
	 * 更改信息状态:未读->已读
	 * @param integer $id
	 */
	public function actionView(){
		$id = Yii::app()->request->getQuery('id');
		if( is_numeric($id) && $id>0 ){
			$c = tbMessage::model()->updateByPk($id,array('state'=>'1'),'memberId=:memberId and state = 0',array(':memberId'=>Yii::app()->user->id));
		}
		$url = $this->createUrl( 'index' );
		$this->dealSuccess($url);
	}

	/**
	 * 删除,更改信息状态:未读/已读->删除
	 * @param integer $id
	 */
	public function actionDelete(){
		$ids = Yii::app()->request->getParam('ids');
		if(!is_array($ids)){
			$ids = explode(',',$ids);
		}
		if( !empty($ids) ){
			$c = tbMessage::model()->updateByPk($ids,array('state'=>'2'),'memberId=:memberId',array(':memberId'=>Yii::app()->user->id));
		}
		$url = $this->createUrl( 'index' );
		$this->dealSuccess($url);
	}
}