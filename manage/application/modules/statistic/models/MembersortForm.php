<?php

class MembersortForm extends SalemanForm
{

    public $product;
    public $start;
    public $end;

    /**
     * 字段标题
     */
    public function attributeLabels()
    {
        return [
            'product' => '产品编号',
            'start'   => '开始时间',
            'end'     => '结束时间',
        ];
    }

    /**
     * 查询条件校验规则的定义 */
    public function rules()
    {
        return [
            ['start,end', 'required', 'message' => Yii::t('warning', 'DATA_NOT_FOUND,{attribute}')],
            ['start,end', 'check_dateformat', 'message' => 'DATA_FORMAT_INVALID,{attribute}'],
            ['product', 'product_exists', 'message' => 'DATA_PRODUCT_INVALID,{attribute}'],
        ];
    }
	
	protected function opDay(){
		$this->start = $this->first_days($this->start);
        $this->end   = $this->last_days($this->end);
	}

    protected function fetchData()
    {
        $command     = Yii::app()->db->createCommand("call proc_sellmember_statistic('{$this->start}','{$this->end}','{$this->product}')");
        $result      = $command->queryAll();

        //提取统计数据
        $statistic       = array_pop($result);
        $this->quantitys = Order::quantityFormat($statistic['quantity']);
        $this->prices    = Order::priceFormat($statistic['price']);
        return $result;
    }
}
