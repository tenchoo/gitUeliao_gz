<?php
class GuestOrderAction extends CWidget {
	public $orderId;
	public $state;
	public $type = 'validate';
	
	public function run() {
		if( $this->type == 'validate' ) {
			return $this->validate();
		}
		return $this->onlyView();
	}


	private function onlyView() {
		if( !$this->orderId ) {
			return false;
		}

		echo CHtml::link( '查看', $this->owner->createUrl('view',array('id'=>$this->orderId)));
	}

	private function validate() {
		if( !$this->orderId ) {
			return false;
		}

		$userRoles = Yii::app()->user->getState( "roles" );
		$access    = Yii::app()->user->checkAccess( 'purchase/order/validate', array('roles'=> $userRoles) );

		if( $access and $this->state == tbRequestbuy::STATE_NORMAL ) {
			echo CHtml::link( '加入采购', '#',array('data-id'=>$this->orderId,'data-rel'=>'/purchase/order/validate','data-toggle'=>'modal','data-target'=>'.add-confirm'));
			echo '<br />';
			echo CHtml::link( '关闭采购', $this->owner->createUrl('close',array('id'=>$this->orderId)));
			echo '<br />';
		}

		//只有关闭的订单可以进行编辑
		if( $this->state == tbRequestbuy::STATE_CLOSE ) {
			echo CHtml::link( '编辑', $this->owner->createUrl('edit',array('id'=>$this->orderId)));
			echo '<br />';
		}

		echo CHtml::link( '查看', $this->owner->createUrl('view',array('id'=>$this->orderId)));
	}
}