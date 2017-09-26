<?php
/**
 * 业务员销售统计代理模型
 * 处理获取业务员销售统计查询条件的校验工作
 */
class SalemanForm extends CFormModel
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
     * 创建查询对象
     */
    protected function &getProcedure()
    {
        $this->start = $this->first_days($this->start);
        $this->end   = $this->last_days($this->end);
        $command     = Yii::app()->db->createCommand("call proc_saleman_statistic({$this->saleman},'{$this->start}','{$this->end}',{$this->member},'{$this->product}')");
        return $command;
    }

	protected function getCount( $time1,$time2 ){
		$where = "o.`createTime`>= '".$time1."' and o.`createTime`< '".$time2."'  AND o.`state`=6 and o.`hasRefund` = 0 ";
		if( $this->member >0 ){
			$where .=" and o.`memberId`='".$this->member."'";
		}

		if( $this->saleman > 0 ){
			$where .=" and o.`userId`='".$this->saleman."'";
		}

		if( empty( $this->product ) ){
			$sql = " SELECT SUM(o.`realPayment`) AS `realPayment` FROM {{order}} o WHERE $where ";
			$command     = Yii::app()->db->createCommand( $sql );
			$result  = $command->queryRow();
			$count['price'] = $result['realPayment'];

			//数量
			$nsql = "SELECT SUM(p.num) AS num FROM {{order_product}} p
					 WHERE p.`state`='0' AND
					 EXISTS ( SELECT NULL FROM {{order}} o WHERE  o.`orderId`=p.`orderId` AND $where )";
			$command     = Yii::app()->db->createCommand( $nsql );
			$result  = $command->queryRow();
			$count['quantity'] = $result['num'];
		}else{
			$sql = " SELECT SUM( `price`*`num` ) as `total` from {{order_product}} p
					 WHERE p.`state`='0' and p.`isSample`='0' AND p.`serialNumber` = '".$this->product."'
					and  EXISTS ( SELECT NULL FROM {{order}} o WHERE  o.`orderId`=p.`orderId` AND $where )";
			$command     = Yii::app()->db->createCommand( $sql );
			$result  = $command->queryRow();
			$count['price'] = $result['total'];

			//数量
			$nsql = "SELECT SUM(p.`num`) AS `num` FROM {{order_product}} p
					 WHERE p.`state`='0' AND  p.`serialNumber` = '".$this->product."'
					 and EXISTS ( SELECT NULL FROM {{order}} o WHERE  o.`orderId`=p.`orderId` AND $where )";
			$command     = Yii::app()->db->createCommand( $nsql );
			$result  = $command->queryRow();
			$count['quantity'] = $result['num'];
		}
		return $count;
	}

    /**
     * 获取销售统计数据 */
    public function findAll()
    {
		$this->opDay();
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

	protected function opDay(){	}

	/**
     * 获取销售统计数据 */
    protected function fetchData()
    {
		$start = strtotime($this->first_days($this->start));
		$end   = strtotime( $this->last_days($this->end) )+86400;//包含选择的当天
		$result = $quantitys = $prices = array();
        for ($index = $start; $index < $end; $index = strtotime('+1 month', $index)) {
			$month = date('Y-m', $index);
			$t = strtotime( $month );
			$t2 =  strtotime( "+1 month", $t );

			if( $t < $start  ){
				$t = $start;
			}

			if( $t2 >  $end ){
				$t2 = $end;
			}

			$time1 =  date('Y-m-d',$t);
			$time2 =  date('Y-m-d',$t2);

			$count = $this->getCount( $time1,$time2 );
			$quantitys[] = $count['quantity'];
			$prices[] = $count['price'];
			$result[] = ['dealTime' => date('Y年m月', $index),
						 'quantity' => Order::quantityFormat( $count['quantity'] ),
						 'price' => Order::priceFormat( $count['price'] )];
		}

		$this->quantitys = Order::quantityFormat( array_sum( $quantitys ) );
        $this->prices    = Order::priceFormat( array_sum( $prices ) );

        return $result;
    }





    /**
     * 销售统计数据进行相应的格式化操作 */
    public function formatter($result)
    {
        $start = strtotime($this->first_days($this->start));
        $end   = strtotime($this->first_days($this->end));
        for ($index = $start; $index <= $end; $index = strtotime('+1 month', $index)) {
            $data = ['dealTime' => date('Y年m月', $index), 'quantity' => Order::quantityFormat(0), 'price' => Order::priceFormat(0)];
            if (!array_key_exists($index, $result)) {
                $result[$index] = $data;
            }
        }
        asort($result);
        return array_values($result);
    }

    /**
     * 获取业务员信息 */
    public function getAccount()
    {
        $account = tbMember::model()->findByPk($this->saleman);
        if (!$account) {
            return;
        }

        $username = $account->profile->username;
        if (empty($username)) {
            $username = '[未命名的用户]';
        }
        return $username;
    }

    /**
     * 获取业务员销量统计信息 */
    public function getQuantitys()
    {
        return $this->quantitys;
    }

    /**
     * 获取业务员销售额统计信息 */
    public function getPrices()
    {
        return $this->prices;
    }

    /**
     * 数据校验器
     * 用以校验业务员账号是否可用
     */
    public function member_exists($attribute, $options)
    {

        if ($attribute == 'member' && empty($this->$attribute) ) {
            return true;
        }
        $account = tbMember::model()->findByPk($this->$attribute);

        if (!is_null($account)) {
            $this->$attribute = $account->memberId;
            return true;
        }
        $this->addError($attribute, Yii::t('warning', $options['message'], ['{attribute}' => $this->attributeLabels()[$attribute]]));
        return false;
    }

    /**
     * 数据校验器
     * 用以校验业务员账号是否可用
     */
    public function product_exists($attribute, $options)
    {
        if ($attribute == 'product' && $this->$attribute == '') {
            return true;
        }

        $this->account = tbProduct::model()->findByAttributes(['serialNumber' => $this->$attribute]);
        if (!is_null($this->account)) {
            return true;
        }
        $this->addError($attribute, Yii::t('warning', $options['message'], ['{attribute}' => $this->attributeLabels()[$attribute]]));
        return false;
    }

    /**
     * 数据校验器
     * 校验提交的日期格式是否正确
     */
    public function check_dateformat($attribute, $options)
    {
        $result = preg_match("/^\\d{4}-\\d{2}(-\\d{2})?$/", $this->$attribute);

        // set label
        $label = $attribute;
        if (array_key_exists($attribute, $this->attributeLabels())) {
            $label = $this->attributeLabels()[$attribute];
        }

        if (!$result) {
            $this->addError($attribute, Yii::t('warning', $options['message'], ['{attribute}' => $this->attributeLabels()[$attribute]]));
        }

		if( $this->start > $this->end ){
			$this->addError($attribute, Yii::t('warning', 'Start time should not be greater than the end time'));
		}

        return $result;
    }

    public function first_days($data)    {

        if ($this->hasErrors('start')) {
            return false;
        }

        if($this->getScenario()==='days') {
        	return $data;
        }

        $data = strtotime($data);
        return date('Y-m-d', $data);
    }

    public function last_days($data)
    {
        if ($this->hasErrors('end')) {
            return false;
        }

        if($this->getScenario()==='days') {
        	return $data;
        }

        $data = strtotime($data);
        $data = strtotime("+1 month -1 day", $data);
        return date('Y-m-d', $data);
    }

}
