<?php
class RequestBuyAction extends CWidget {
	public $orderId;
	public $state;
	public $isBuyList=false;
	
	public function run() {
		if( $this->isBuyList ) {
			return $this->buyList();
		}
		return $this->lists();
	}
	
	/**
	 * 管理列表
	 */
	private function lists() {
		if( !$this->orderId ) {
			return false;
		}
		
		$userRoles = Yii::app()->user->getState( "roles" );
		$access    = Yii::app()->user->checkAccess( 'purchase/requestbuy/validate', array('roles'=> $userRoles) );
		
		echo $this->state( $this->state ),'<br />';
		
		//只有关闭的订单可以进行编辑
		if( $this->state == tbRequestbuy::STATE_CLOSE ) {
			echo CHtml::link( '编辑', $this->owner->createUrl('edit',array('id'=>$this->orderId)));
			echo '<br />';
		}
		
		echo CHtml::link( '查看', $this->owner->createUrl('view',array('id'=>$this->orderId)));
	}
	
	/**
	 * 采购列表
	 */
	private function buyList() {
		if( !$this->orderId ) {
			return false;
		}
		
		$userRoles = Yii::app()->user->getState( "roles" );
		$access    = Yii::app()->user->checkAccess( 'purchase/requestbuy/validate', array('roles'=> $userRoles) );
		
		if( $access and $this->state == tbRequestbuy::STATE_NORMAL ) {
			echo CHtml::link( '加入采购', '#',array('data-id'=>$this->orderId,'data-rel'=>'/purchase/requestbuy/validate','data-toggle'=>'modal','data-target'=>'.add-confirm'));
			echo '<br />';
			echo CHtml::link( '关闭采购', $this->owner->createUrl('close',array('id'=>$this->orderId)));
			echo '<br />';
		}
		
		echo CHtml::link( '查看', $this->owner->createUrl('view',array('id'=>$this->orderId)));
	}
	
	private function state( $state ) {
		switch( $state ) {
			case tbRequestbuy::STATE_CLOSE:
				return Yii::t('order', 'REQUEST_BUY_CLOSE');
				
			case tbRequestbuy::STATE_NORMAL:
				return Yii::t('order', 'REQUEST_BUY_NORMAL');
				
			case tbRequestbuy::STATE_WAITING:
				return Yii::t('order', 'REQUEST_BUY_WAITING');
				
			case tbRequestbuy::STATE_PROCCESSING:
				return Yii::t('order', 'REQUEST_BUY_PROCCESSING');
				
			case tbRequestbuy::STATE_FINISHED:
				return Yii::t('order', 'REQUEST_BUY_FINISHED');
				
			case tbRequestbuy::STATE_CHECKED:
				return Yii::t('order', 'REQUEST_BUY_CHECKED');
		}
		return;
	}
}