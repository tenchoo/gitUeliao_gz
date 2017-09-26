<?php
/**
 * 产品销售明细代理模型
 * 处理获取产品销售明细查询条件的校验工作
 */
class ProductdetailForm extends SalemandetailForm
{

    public function attributeLabels()
    {
        return [
            'product' => '产品编码',
            'start'   => '开始时间',
            'end'     => '结束时间',
            'member'  => '会员名称',
        ];
    }

    /**
     * 查询条件校验规则的定义 */
    public function rules()
    {
        return [
            ['start,end,product', 'required', 'message' => Yii::t('warning', 'DATA_NOT_FOUND,{attribute}')],
            ['start,end', 'check_dateformat', 'message' => 'DATA_FORMAT_INVALID,{attribute}'],
            ['product', 'product_exists', 'message' => 'DATA_PRODUCT_INVALID,{attribute}'],
            ['member', 'member_exists', 'message' => 'DATA_ACCOUNT_INVALID,{attribute}'],
        ];
    }

	public function cacheName(){
		$c = array(
				__CLASS__ ,
				strtotime($this->start),
				strtotime($this->end),
				$this->product,
				$this->member
			);
		return implode('_',$c );
	}

    /**
     * 创建查询对象
     */
    protected function &getProcedure()
    {
        $this->start = $this->first_days($this->start);
        $this->end   = $this->last_days($this->end);
        $command     = Yii::app()->db->createCommand("call proc_product_detail('{$this->product}','{$this->start}','{$this->end}', {$this->member})");
        return $command;
    }

    public function getAccount()
    {
        $product = tbProduct::model()->findByAttributes(['serialNumber'=>$this->product]);
        if (!$product) {
            return;
        }

        $productName = $product->serialNumber;
        return $productName;
    }

    protected function fetchData()
    {
		$criteria = new CDbCriteria;
		$criteria->compare( 't.state','0' );
		$criteria->compare( 't.serialNumber',$this->product );

		$owhere = 'o.state = 6 and o.hasRefund =0 and o.createTime>='."'".$this->start."' and o.createTime<'".$this->end."'";

		if( $this->member ){
			$owhere .= " and o.memberId = '".$this->member."'";
		}

		$criteria->addCondition( "exists ( select null from {{order}} o  where o.`orderId` = t.`orderId` and $owhere )");

		$models = tbOrderProduct::model()->findAll( $criteria );
		$data = $quantitys = $totalPay = array();
		foreach( $models as $val ){
			if( $val->isSample ){
				$total = $val->price = 0.00;
			}else{
				$total = bcmul( $val->price,$val->num,2 );
			}
			$color = explode('-',$val->singleNumber );
			$data[] = array('orderId' => $val->orderId,
							'serialNumber' => $val->serialNumber,
							'singleNumber' =>  array_pop($color),
							'quantity' => Order::quantityFormat( $val->num ),
							'price' => $val->price,
							'total' => $total );
			$quantitys[] = $val->num;
			$totalPay[] = $total;
		}

		$this->quantitys = Order::quantityFormat( array_sum ( $quantitys ) );
        $this->prices    = Order::priceFormat( array_sum ( $totalPay )  );
		return $data ;
    }
}
