<?php
/**
 * 客户月结账单
 *
 * @property integer 	$id
 * @property integer 	$billId		账单ID
 * @property integer 	$orderId	订单ID
 * @property number 	$amount		账单金额
 * @property datetime	$createTime	生成时间
 * @property mark		$mark		说明
 */

class tbMemberBillDetail extends CActiveRecord
{
	/**
	 * 返回基于自身的AR实例
	 * @param string $className 类名
	 * @return tbMemberBillDetail 实例
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string 返回表名
	 */
	public function tableName()
	{
		return '{{member_bill_detail}}';
	}
}