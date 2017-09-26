<?php
/**
 * 客户月结账单
 *
 * @property integer 	$billId		账单ID
 * @property integer 	$memberId	客户ID
 * @property number 	$credit		账单金额
 * @property datetime	$createTime	账单生成时间
 */

class tbMemberBill extends CActiveRecord
{
	/**
	 * 返回基于自身的AR实例
	 * @param string $className 类名
	 * @return tbMemberBill 实例
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
		return '{{member_bill}}';
	}

	/**
	 * 关联查询配置
	 */
	public function relations() {
		return array(
			'detail'=>array(self::HAS_MANY,'tbMemberBillDetail','billId'),
		);
	}

	protected function afterFind(){
		$this->createTime = date('Y-m-d',strtotime($this->createTime));
	}
}