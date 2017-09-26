<?php
/**
 * 产品销售排行榜
 * @package SalemanForm
 */
class ProductsortForm extends SalemanForm
{

    /**
     * 字段标题
     */
    public function attributeLabels()
    {
        return [
            'product' => '产品编号',
            'member'  => '客户名称',
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
            ['member', 'member_exists', 'message' => 'DATA_ACCOUNT_INVALID,{attribute}'],
        ];
    }

	protected function opDay(){
		
	}


	protected function fetchData()
    {
		$start = $this->first_days($this->start);
		$end   = date('Y-m-d',strtotime( $this->last_days($this->end) )+86400);//包含选择的当天
		$where = "o.`createTime`>= '".$start."' and o.`createTime`< '".$end."'  AND o.`state`=6 and o.`hasRefund` = 0 ";
		if( $this->member >0 ){
			$where .=" and o.`memberId`='".$this->member."'";
		}

		if( $this->saleman > 0 ){
			$where .=" and o.`userId`='".$this->saleman."'";
		}

		$pwhere = 'p.`state`=0 AND p.`isSample`=0';
		if( empty( $this->product ) ){
			$select = ' p.`serialNumber` ';
			$group  = 'p.`serialNumber`';
			$limit  = 'limit 50';
		}else{
			$select = ' p.`singleNumber` as `serialNumber`';
			$group  = 'p.`singleNumber`';
			$limit  = '';
			$pwhere .= ' AND  p.`serialNumber` = \''.$this->product.'\'';

		}
		//取得数量
		$nsql = "SELECT SUM( `price`*`num` ) as `total` ,SUM(p.num) AS num, $select
				FROM {{order_product}} p
				WHERE $pwhere
				 and EXISTS ( SELECT NULL FROM {{order}} o WHERE  o.`orderId`=p.`orderId` AND $where )
				group by $group  order by num desc $limit";

		$command     = Yii::app()->db->createCommand( $nsql );
		$result  = $command->queryAll();
		return $result;
    }


    public function getAccount()
    {
        $account = tbProduct::model()->findByAttributes(['serialNumber'=>$this->product]);
        if (!$account) {
            return;
        }

        $username = $account->serialNumber;
        if (empty($username)) {
            $username = '[未命名的用户]';
        }
        return $username;
    }
}
