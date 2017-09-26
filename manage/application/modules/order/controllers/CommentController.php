<?php
/**
* 客户反馈
* @access 客户反馈
* @author liang
* @package Controller
* @version 0.1
*/
class CommentController extends Controller {

	/**
	 * 客户反馈
	 * @access 客户反馈
	 */
	public function actionIndex() {
		$condition['orderId'] = Yii::app()->request->getQuery('orderId');
		$condition['createTime1'] = Yii::app()->request->getQuery('createTime1');
		$condition['createTime2'] = Yii::app()->request->getQuery('createTime2');
		$data = Comment::getList( $condition );
		$this->render( 'index' ,array('list'=>$data['list'],'pages'=>$data['pages'],'condition'=>$condition));
	}

	/**
	* 解释反馈
	* @access 解释反馈
	*/
	public function actionEdit(){
		if( Yii::app()->request->isPostRequest ){
			$commentId = Yii::app()->request->getQuery('commentId');
			$model = tbComment::model()->findByPk( $commentId );
			$model->reply = Yii::app()->request->getPost('reply');
			$model->replyTime = new CDbExpression('NOW()');
			if( $model->save() ) {	
				$this->dealSuccess( '' );
			} else {
				$errors = $model->getErrors();
				$this->dealError( $errors );
			}
		}else{
			$this->redirect( $this->createUrl( 'index' ) );
		}
	}
	
	/**
	 * 删除客户反馈
	 * @access 删除客户反馈
	 */
	public function actionDel() {
		$commentId = Yii::app()->request->getQuery('commentId');
		tbComment::model()->updateByPk( $commentId ,array('state'=>'1') );
		$this->dealSuccess( Yii::app()->request->urlReferrer );
	}
}
