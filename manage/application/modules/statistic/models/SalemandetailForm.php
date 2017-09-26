<?php
/**
 * 业务员销售统计代理模型
 * 处理获取业务员销售统计查询条件的校验工作
 */
class SalemandetailForm extends SalemanForm
{
    public $saleman; //业务员编号
    public $start; //筛选开始时间
    public $end; //筛选结束时间
    public $member  = 0; //客户编号
    public $product = ''; //产品单品编码

    protected $account; //业务员账号
    protected $quantitys = 0; //销量统计
    protected $prices    = 0; //销售额统计

    /**
     * 查询条件校验规则的定义 */
    public function rules()
    {
        return [
            ['start,end,saleman', 'required', 'message' => Yii::t('warning', 'DATA_NOT_FOUND,{attribute}')],
            ['start,end', 'check_dateformat', 'message' => 'DATA_FORMAT_INVALID,{attribute}'],
            ['saleman,member', 'member_exists', 'message' => 'DATA_ACCOUNT_INVALID,{attribute}'],
            ['product', 'product_exists', 'message' => 'DATA_PRODUCT_INVALID,{attribute}'],
        ];
    }

    /**
     * 字段标题
     */
    public function attributeLabels()
    {
        return [
            'saleman' => '业务员名称',
            'start'   => '开始时间',
            'end'     => '结束时间',
            'member'  => '客户名称',
            'product' => '产品编号',
        ];
    }

    /**
     * 创建查询对象
     */
    protected function &getProcedure()
    {
        $this->start = $this->first_days($this->start);
        $this->end   = $this->last_days($this->end);
        $command     = Yii::app()->db->createCommand("call proc_saleman_detail({$this->saleman},'{$this->start}','{$this->end}',{$this->member},'{$this->product}')");
        return $command;
    }

	public function cacheName(){
		$c = array(
				__CLASS__ ,
				strtotime($this->start),
				strtotime($this->end),
				$this->saleman,
				$this->product,
				$this->member,

			);
		return implode('_',$c );
	}

    /**
     * 获取销售统计数据 */
    public function findAll()
    {
		$this->start = $this->first_days($this->start);
        $this->end   = $this->last_days($this->end);
		$this->end = date("Y-m-d H:i:s",strtotime( $this->end )+86400 ) ; //包含选择的当天

		$cacheName = $this->cacheName();
		$data = json_decode( Yii::app()->cache->get( $cacheName ),true );//获取缓存
		if( !empty($data) && isset( $data['quantitys'] ) && isset( $data['prices'] ) && isset( $data['data'] )){
			$this->quantitys = $data['quantitys'];
			$this->prices    = $data['prices'];
			return $data['data'];
		}else{
			$data = $this->fetchData();
			$cacheData = array( 'data'=>$data,'quantitys'=>$this->quantitys ,'prices'=>$this->prices );
			//缓存数据，一次缓存10分钟
			Yii::app()->cache->set( $cacheName ,CJSON::encode( $cacheData ),600 );
			return $data ;
		}
    }

	protected function fetchData()
    {
		$criteria = new CDbCriteria;
		$criteria->select = 't.orderId,t.memberId';

		if( $this->saleman ){
			$criteria->compare( 't.userId',$this->saleman );
		}

		if( $this->member ){
			$criteria->compare( 't.memberId',$this->member );
		}

		$criteria->compare( 't.state','6' );
		$criteria->compare( 't.hasRefund','0' );

		$criteria->addCondition("t.createTime>='".$this->start."'");
		$criteria->addCondition("t.createTime<'".$this->end."'");

		if( $this->product ){
			$criteria->addCondition( "exists ( select null from {{order_product}} p  where p.`orderId`=t.`orderId` and p.`serialNumber` ='".$this->product."' )");
		}

		$orders = tbOrder::model()->findAll( $criteria );
		$data = $guest = $quantitys = $totalPay = array();
		foreach( $orders as $val ){
			if( !isset( $guest[$val->memberId] ) ){
				$member = tbProfileDetail::model()->findByPk( $val->memberId );
				if( $member ){
					$guest[$val->memberId] = $member->companyname;
				}else{
					$guest[$val->memberId] = '';
				}
			}
			$products = $val->products;
			foreach ( $products as $pval ){
				if(  $this->product && $this->product != $pval->serialNumber ) continue;

				if( $pval->isSample ){
					$total = $pval->price = 0.00;
				}else{
					$total = bcmul( $pval->price,$pval->num,2 );
				}
				$color = explode('-',$pval->singleNumber );
				$data[] = array(
							'orderId' => $pval->orderId,
							'guest'=> $guest[$val->memberId] ,
							'serialNumber' => $pval->serialNumber,
							'singleNumber' =>  array_pop($color),
							'quantity' => Order::quantityFormat( $pval->num ),
							'price' => $pval->price,
							'total' => $total
						);
				$quantitys[] = $pval->num;
				$totalPay[] = $total;
			}
		}
		$this->quantitys = Order::quantityFormat( array_sum ( $quantitys ) );
        $this->prices    = Order::priceFormat( array_sum ( $totalPay )  );
		return $data ;
	}


}
