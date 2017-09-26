<?php
/**
 * 获取已选择待采购产品数量
 * @author yagas
 * @package CAction
 *
 */
class OrderBuyListCount extends CAction implements IAction {
	
	public function run() {
		$this->controller->checkAccess( 'default/purchase/add' );
		
		$count = tbOrderPurchase2::model()->countByAttributes( array('userId'=>Yii::app()->user->id,'state'=>tbOrderPurchase::STATE_NORMAL) );
		$ajax = new AjaxData(true,null,array('count'=>$count));
		return $ajax;
	}
}