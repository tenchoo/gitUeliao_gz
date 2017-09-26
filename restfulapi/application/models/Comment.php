<?php
/**
 * 客户评论
 * @author liang
 * @version 0.1
 * @package CFormModel
 *
 */

class Comment extends CFormModel {

	public $comment;

	/**
	* Declares the validation rules.
	* The rules state that account and password are required,
	* and password needs to be authenticated.
	*/
	public function rules()	{
		return array();
	}


	/**
	* 收货确定
	* @param array $dataArr 收货的数据
	* @param obj $model
	*/
	public function save( $dataArr,$model ){
		//使用事务处理
		$transaction = Yii::app()->db->beginTransaction();
		try {
			$tbComment = new tbComment();
			foreach ($model->products as $val){
				if( !isset($dataArr[$val->orderProductId]) || empty($dataArr[$val->orderProductId]) || mb_strlen(trim($dataArr[$val->orderProductId]))<6){
					$this->addError('packNum',Yii::t('order','Please fill in the comments, the number of words can not be less than 6'));
					return false;
				}

				//保存评论
				$_model = clone $tbComment;
				$_model->content = trim($dataArr[$val->orderProductId]);
				$_model->orderId = $val->orderId;
				$_model->productId = $val->productId;
				$_model->orderProductId = $val->orderProductId;
				$_model->orderProductId = $val->orderProductId;
				$_model->memberId = $model->memberId;
				$_model->specifiaction = $val->specifiaction;

				if( !$_model->save() ){
					$this->addErrors( $_model->getErrors() );
					return false;
				}

			}
			$model->commentState = 1;
			if( !$model->save() ){
				$this->addErrors( $model->getErrors() );
				return false;
			}
			$transaction->commit();
			return true;
		} catch (Exception $e) {
			$transaction->rollback(); //如果操作失败, 数据回滚
			throw new CHttpException(503,$e);
			return false;
		}
	}

}