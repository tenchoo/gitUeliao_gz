<?php
class orderBuyTodo extends CWidget {
	public $orderId;
	public $state;
	public $route;
	
	public function run() {
		$links = array();
		
		if( in_array($this->state,array(tbOrderPurchasing::STATE_NORMAL)) ) {
			$link_edit = CHtml::link('发货 ', $this->make($this->route.'/post',array('id'=>$this->orderId)) );
			array_push( $links, $link_edit );
			
			$link_close = CHtml::link('取消采购单 ', $this->make($this->route.'/cancle',array('id'=>$this->orderId)) );
			array_push( $links, $link_close );
		}
		
		$link_view = CHtml::link('查看采购单 ', $this->make($this->route.'/view',array('id'=>$this->orderId)) );
		array_push( $links, $link_view );
		echo $this->state( $this->state ),'<br />';
		echo implode('<br/>', $links);
	}
	
	public function make($route,$params=array()) {
		return $this->owner->createUrl($route,$params);
	}
	
	public function state( $stateNumber ) {
		switch ( $stateNumber ) {
			case tbOrderPurchasing::STATE_NORMAL:
				$msg = 'ORDER_BUY_NORMAL';
				break;
				
			case tbOrderPurchasing::STATE_FINISHED:
				$msg = 'ORDER_BUY_FINISHED';
				break;

			case tbOrderPurchasing::STATE_CHECKED:
				$msg = 'ORDER_BUY_CHECKED';
				break;
			case tbOrderPurchasing::STATE_CLOSE:
				$msg = 'ORDER_BUY_CLOSE';
				break;
				
				
		}
		
		return Yii::t('order',$msg);
	}
}