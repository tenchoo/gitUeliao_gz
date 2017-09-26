<?php
/**
 * ajax 产品收藏
 * @author liang
 * @version 0.1
 * @param string optype 操作类型,add 增加收藏，check 查看是否收藏,cancle 取消收藏
 * @package CAction
 */
class ajaxCollection extends CAction {
	private $_state = false ;
	private $_e;

	public function run() {
		if( Yii::app()->user->getIsGuest() ) {
			$this->_e = Yii::t('user','You do not log in or log out');
			goto end;
		}

		$productId = Yii::app()->request->getQuery('productId');
		$optype = Yii::app()->request->getParam('optype');
		$func = $optype.'Collection';
		if( method_exists ( 'tbProductCollection',$func ) ) {
			$this->_state = call_user_func(array('tbProductCollection',$func),$productId,Yii::app()->user->id);
		}

		end:
		$json=new AjaxData($this->_state,$this->_e);
		echo $json->toJson();
		Yii::app()->end();
	}
}