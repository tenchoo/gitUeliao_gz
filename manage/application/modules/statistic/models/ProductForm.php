<?php
/**
 * 产品销售统计代理模型
 * 处理获取产品销售统计查询条件的校验工作
 */
class ProductForm extends SalemanForm {

	public function attributeLabels() {
		return [
			'product' => '产品编码',
			'start'   => '开始时间',
			'end'     => '结束时间',
			'member'  => '会员名称'
		];
	}

	/**
	 * 查询条件校验规则的定义 */
	public function rules() {
		return [
			['start,end,product', 'required', 'message'=>Yii::t('warning','DATA_NOT_FOUND,{attribute}')],
			['start,end', 'check_dateformat', 'message'=>'DATA_FORMAT_INVALID,{attribute}'],
			['product', 'product_exists', 'message'=>'DATA_PRODUCT_INVALID,{attribute}'],
			['member', 'member_exists', 'message'=>'DATA_ACCOUNT_INVALID,{attribute}'],
		];
	}


	/**
	 * 创建查询对象
	 */
	protected function & getProcedure() {
		$this->start = $this->first_days($this->start);
		$this->end = $this->last_days($this->end);
		$command = Yii::app()->db->createCommand("call proc_product_statistic('{$this->product}','{$this->start}','{$this->end}', {$this->member})");
		return $command;
	}


	public function getAccount() {
		$product = tbProduct::model()->findByAttributes(['serialNumber'=>$this->product]);
        if (!$product) {
            return;
        }

		$productName = $product->serialNumber;
		return $productName;
	}

}