<?php
/**
 * 管理后台工作台--待处理任务,统计对应项
 * @author liang
 * @version 0.1
 */
class WorkCount{

	/**
	* 待审核客户
	*/
	public static function memberCheck(){
		return tbMember::model()->count('groupId>1 and state in(\'Normal\',\'Disabled\') and isCheck in(0,2)');
	}

	/**
	* 待审核订单
	*/
	public static function orderCheck(){
		return tbOrder::model()->count('state=0 and orderType in(0,1)');

	}

	/**
	* 备货完成订单
	*/
	public static function packingcomplete(){
		return tbOrder::model()->count('state = 2');
	}

	/**
	* 待财务确定订单
	*/
	public static function waitconfirmpayment(){
		return tbOrder::model()->count('t.PayState<2 and t.state in(3,4,5,6)');
	}

	/**
	* 待发货订单
	*/
	public static function waitdelivery(){
		return tbOrder::model()->count('t.state = 3 and ( t.PayState>=2 or t.payModel =4 )');
	}


	/**
	* 待确认收货订单
	*/
	public static function waitconfirm(){
		return tbOrder::model()->count('state =4');
	}

	/**
	* 价格申请审核
	*/
	public static function applyprice(){
		return tbOrderApplyprice::model()->count('state = 0');
	}

	/**
	* 取消订单审核
	*/
	public static function applyclose(){
		return tbOrderApplyclose::model()->count('state = 0');
	}

	/**
	* 修改订单审核
	*/
	public static function applychange(){
		return tbOrderApplychange::model()->count('state = 0');
	}

	/**
	* 待收款订单
	*/
	public static function waitpayment(){
		return tbOrder::model()->count('payState<2');
	}

	/**
	* 待采购订单
	*/
	public static function waitPurchase(){
		return tbOrderPurchase2::model()->count('state=0 and isAssign=0');
	}

	/**
	* 待发货采购单
	*/
	public static function waitDeliveryPurchase(){
		return tbOrderPurchasing::model()->count('state=0');
	}

	/**
	* 待匹配订单
	*/
	public static function assignwait(){
		$table = tbOrderPurchase2::model()->tableName();
		$sql = "select count(distinct orderId) from $table where isAssign=0 and state = 2";
		$cmd = Yii::app()->db->createCommand($sql);
		$result = $cmd->queryScalar();
		return intval($result);


		//return tbOrderPurchase2::model()->unAssignCount();
	}

	/**
	* 待审核内部请购单
	*/
	public static function requestbuyCheck(){
		return tbRequestbuy::model()->count('state=0');
	}

	/**
	* 待审核留货单
	*/
	public static function keepCheck(){
		return tbOrderKeep::model()->count('state=0 and buyState=0');
	}

	/**
	* 留货订单延期申请
	*/
	public static function keepDelay(){
		return tbOrderKeepDelay::model()->count('state=0');
	}

	/**
	* 待入库订单
	*/
	public static function waitToWarehouse(){
		return tbOrderPost2::model()->count('state=2');
	}

	/**
	* 待分配分拣订单
	*/
	public static function waitDistribution(){
		return tbOrderDistribution::model()->count('state=0');
	}

	/**
	* 待分拣订单
	*/
	public static function waitPacking(){
		return tbPacking::model()->count('state=0');
	}

	/**
	* 待调拔订单
	*/
	public static function waitAllocation(){
		return tbAllocation::model()->count('state = 0');
	}

	/**
	* 待确认调拨订单
	*/
	public static function waitConfirmAllocation(){
		return tbAllocation::model()->count('state =1');
	}

	/**
	* 待回复询盘
	*/
	public static function inquiry(){
		return tbInquiry::model()->count('hasNew = 1');
	}

	/**
	* 待发货订单
	*/
	public static function factoryDelivery(){
		return tbOrderPurchasing::model()->count('state = 0');
	}
}