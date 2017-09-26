<?php
/**
 * 前端--客户/业务员订单管理
 * @author liang
 * @version 0.1
 * @package CModel
 */
class OrderManager extends CModel {
	public $userType;

	public $memberId;

	public function attributeNames() {
		return array();
	}

	/**
	* 取得订单物流信息
	* @param integer $orderId 订单号
	*/
	public function getLogistics( $orderId ,&$msg ){
		if(empty($this->memberId) || empty($orderId)) return ;

		$criteria = new CDbCriteria;
		$criteria->select = 't.deliveryId,t.orderId,t.logistics,t.logisticsNo,t.address,t2.memberId as userId';
		$criteria->compare('t.orderId', $orderId);
		if($this->userType == tbMember::UTYPE_SALEMAN ){
			$criteria->join = "join {{order}} t2 on ( t.orderId=t2.orderId )";
		}else{
			$condition = ' t2.memberId = '.$this->memberId ;
			$criteria->join = "join {{order}} t2 on ( t.orderId=t2.orderId and $condition)";
		}

		$logistics = array();
		$model = tbDelivery::model()->find( $criteria );
		if( !$model ){
			$msg = Yii::t('glo','not express');
			return ;
		}

		if( $this->userType == tbMember::UTYPE_SALEMAN ){
			$isserve = tbMember::checkServe( $model->userId ,$this->memberId );
			if( !$isserve ){
				$msg = Yii::t('glo','You do not have permission to view this page.');
				return ;
			}
		}

		$tbLogistics = tbLogistics::model()->findOne( $model->logistics );

		$data['title'] = $tbLogistics['title'];
		$data['mark']  = $tbLogistics['mark'];
		$data['logisticsNo'] = $model->logisticsNo;

		$exp = $this->getExpress( $data['mark'],$model->logisticsNo );
		if($exp['status']==0){
			$data['content'] = Yii::t('glo','Query interface error! Please try again later');
		}else{
			$data['content'] = '';
		}
		return $data;
	}


	/* 取得物流信息
	 * @param string $com 物流公司标识
	 * @param string $nu 物流运单号
	 */
	public function getExpress( $com,$nu ){
		$param['id'] = '106617';
		$param['secret'] = '83d07350a8ebd29c9a16af46a844bccf';		//该参数为新增加，老用户可以使用申请时填写的邮箱和接收到的KEY值登录
		$param['com'] = $com;					//要查询的快递公司代码
		$param['nu'] = $nu;					//要查询的快递单号
		$param['lang'] = '';					//en返回英文结果，目前仅支持部分快递（EMS、顺丰、DHL）
		$param['type'] = 'json';						//返回结果类型，值分别为 html | json（默认） | text | xml
		$param['encode'] = 'utf8';						//gbk（默认）| utf8
		$param['order'] = 'desc';				//排序： desc：按时间由新到旧排列， asc：按时间由旧到新排列。 不填默认返回倒序（大小写不敏感）

		$url="http://api.ickd.cn/?id={$param['id']}&secret={$param['secret']}&com={$param['com']}&nu={$param['nu']}&type={$param['type']}&encode={$param['encode']}&ord={$param['order']}&ver=2";


		//建立缓存
		$expcache = 'exp'.$com.$nu;
		if($json = Yii::app()->cache->get($expcache)){
			$exp =  $json;
		}else{
			$str = file_get_contents($url);
			$exp = json_decode($str,true);
			//设置缓存
			Yii::app()->cache->set($expcache,$exp,3600*24);
		}

		return $exp;
	}

	/**
	* 取得订单详情
	* @param integer $orderId 订单号
	*/
	public function getOne( $orderId ){
		if(empty($this->memberId) || empty($orderId)) return ;

		if($this->userType == tbMember::UTYPE_SALEMAN ){
			$condition = '';
		}else{
			$condition = ' t.memberId = '.$this->memberId ;
		}
		$model = tbOrder::model()->with('products','batches','paymemt')->findByPk( $orderId ,$condition );
		if( !$model ){
			return null;
		}

		if( $this->userType == tbMember::UTYPE_SALEMAN ){
			$isserve = tbMember::checkServe( $model->memberId,$this->memberId );
			if( !$isserve ){
				return null;
			}
		}
		$info = $model->attributes;
		unset($info['memberId'],$info['userId'],$info['isDel']);

		$info['state'] = (int) $info['state'] ;
		$info['freight'] = number_format( $model->freight,2 );
		$order = new Order();
		$order->userType = $this->userType;
		$order->memberId = $this->memberId;
		$info['orderType'] = $order->orderType( $model->orderType );



		$info['deliveryMethod'] = $order->deliveryMethod( $model->deliveryMethod );
		$info['totalNum'] = 0;

		if( $model->orderType =='2' ){
			//留货订单，查找留货到期日期
			$keep = tbOrderKeep::model()->findByAttributes( array('orderId'=>$model->orderId) );
			$info['keepDate'] =  date('Y/m/d',$keep->expireTime);
			$info['stateTitle'] = '';
		}else{
			//留货订单状态不显示
			if( empty( $info['payModel'] ) && $model->state == '0' ){
				$info['stateTitle'] = '待付款';
			}else{
				$info['stateTitle'] = $order->stateTitle( $model->state );
			}
		}

		$info['products'] = array();
		$productids = array_map( function ( $i ){ return $i->productId;},$model->products);
		$units = tbProduct::model()->getUnitConversion( $productids );

		foreach ( $model->products as $pro ){
			if( !isset($info['products'][$pro->productId])){
				$pro->mainPic = $this->getImageUrl( $pro->mainPic, 200 );
				$pro->price = Order::priceFormat( $pro->price);
				$info['products'][$pro->productId] = $pro->getAttributes(array('productId','title','serialNumber','mainPic','price'));
				$info['products'][$pro->productId]['unit'] = $units[$pro->productId]['unit'];
			}
			$pro->specifiaction = explode(':',$pro->specifiaction);
			$pro->specifiaction = $pro->specifiaction['1'];
			$info['totalNum'] += $pro->num;

			$pro->num = Order::quantityFormat( $pro->num );
			$info['products'][$pro->productId]['detail'][] = $pro->getAttributes(array('num','isSample','packingNum','deliveryNum','specifiaction'));

		}
		$info['products'] = array_values($info['products']);
		$info['totalNum'] = Order::quantityFormat( $info['totalNum'] );
		$info['batches'] = array();
		foreach ( $model->batches as $pro ){
			$info['batches'][] = $pro->attributes;
		}

		$info['paymemt'] = array();
		$payments = tbPayMent::model()->getPayMents();
		foreach ( $model->paymemt as $pro ){
			if( $pro->voucher ){
				$pro->voucher = Yii::app()->params['domain_images'] . $pro->voucher;
			}
			$pro->payTime = date('Y/m/d H:i',strtotime($pro->payTime));
			$pro->type = $payments[$pro->type]['paymentTitle'];
			$info['paymemt'][] = $pro->attributes;
		}

		return $info;

	//	var_dump($info);
	//	$info['member'] = $order->getMemberDetial( $model->memberId );
	//	$info['salesman'] = ( $model->user )?$model->user->username:'';
	}


	/**
	* 取得订单列表
	* @param integer $condition
	* @param array $condition
	*/
	public function getList( $type,$nextid,$condition,$pageSize = 10){
		$tab = $this->tabs( $type );
		if( empty($this->memberId) || !array_key_exists('condition',$tab) ) return array();
		
		$condition =array_merge(  $tab['condition'],$condition );
		$order = new Order ();
		$order->userType = $this->userType;
		$order->memberId = $this->memberId;

		$data = $order->mList( null,$nextid, $condition ,$pageSize );

		$com = array();
		foreach ($data['list'] as &$val){
			$val['pTitle'] = '【'.$val['products']['0']['serialNumber'].'】'.$val['products']['0']['title'];
			$val['mainPic'] = $this->getImageUrl( $val['products']['0']['mainPic'], 200 );
			$val['totalNum'] = 0;
			foreach( $val['products'] as $p ){
				$val['totalNum'] += $p['num'];
			}

			$productId =$val['products']['0']['productId'];
			$val['unit'] = isset( $data['units'][$productId]['unit'] )?$data['units'][$productId]['unit']:'';

			unset($val['paymemt'],$val['products'],$val['member']);
		}

		unset($data['pages'],$data['units']);
		return $data;
	}




	/**
	 * 获取图片相对于网站的路径
	 * @param string $url  存储路径
	 * @param number $size 缩略图大小
	 * @return string
	 */
	public function getImageUrl( $url, $size=100 ) {
		return Yii::app()->params['domain_images'] . $url . '_' . $size;
	}

	/**
	 * 计算总数
	 * @param  array $condition 查找条件
	 */
	public function orderCounts( $type ){
		$tab = $this->tabs( $type );
		if( empty($this->memberId) || !array_key_exists('condition',$tab) ) return 0;

		$condition = $tab['condition'];
		$criteria = new CDbCriteria;
		if(  $this->userType == tbMember::UTYPE_SALEMAN ){
			$userId[] =  $this->memberId ;
			if( tbConfig::model()->get( 'default_saleman_id' ) == $this->memberId ){
				$userId[] = 0;
			}
			$userId = implode(',',$userId);
			$criteria->join .= ' left join {{member}}  m on (m.memberId = t.memberId and m.userId in ('.$userId.') )';
			$criteria->addCondition("m.memberId is not null ");
		}else {
			$condition['memberId'] = $this->memberId;
		}

		if(!empty($condition)){
			foreach ($condition as $key=>$val){
				if( is_string($val)) $val = trim($val);
				if( is_null($val) || ( $val != '0' && $val == '' ) ){
					continue ;
				}
				
				switch( $key ){
					case 'createTime1':
						$criteria->addCondition("t.createTime>'$val'");
						break;
					case 'createTime2':
						$createTime2 = date("Y-m-d H:i:s",strtotime( $val )+86400 ) ; //包含选择的当天
						$criteria->addCondition("t.createTime<'$createTime2'");
						break;
					case 'orderId':
						$criteria->compare('t.'.$key,$val,true);
						break;
					case 'is_string':
						$criteria->addCondition( $val );//直接传搜索条件
						break;
					default:
						$criteria->compare('t.'.$key,$val);
						break;
				}
			}
		}

		$count = tbOrder::model()->count( $criteria );
		return (int)$count;
	}

	public function tabs( $type='' ){
		$tabs = array(
			'0'=>array('title'=>'所有订单','condition'=>array( 'isDel'=>'0' )),
			'1'=>array('title'=>'待审核','condition'=>array( 'state'=>'0','is_string'=>' t.payModel != 0 ' )),
			'2'=>array('title'=>'备货中','condition'=>array( 'state'=>'1' )),
			'3'=>array('title'=>'备货完成','condition'=>array( 'state'=>'2' )),
			'4'=>array('title'=>'待发货','condition'=>array( 'state'=>'3' )),
			'5'=>array('title'=>'待确认收货','condition'=>array( 'state'=>'4' )),
			'6'=>array('title'=>'待付款','condition'=>array( 'payModel'=>'0','state'=>array(0,1,2),'is_string'=>' t.orderType != 2 ' ))

			/* '0'=>array('title'=>'所有订单','condition'=>array( 'isDel'=>'0' )),
			'1'=>array('title'=>'待审核','condition'=>array( 'state'=>'0','payModel'=>array(1,2) )),
			'2'=>array('title'=>'备货中','condition'=>array( 'state'=>'1' ,'payState'=>array(1,2))),
			'3'=>array('title'=>'备货完成','condition'=>array( 'state'=>'2' )),
			'4'=>array('title'=>'待发货','condition'=>array( 'state'=>'3' )),
			'5'=>array('title'=>'待收货','condition'=>array( 'state'=>'4' )),
			'6'=>array('title'=>'待付款','condition'=>array( 'payModel'=>'0','state'=>array(0,1,2) )) */
			//'6'=>array('title'=>'待结算','condition'=>array( 'payState'=>array(0,1) ))
		);
		if( isset( $tabs[$type] ) ){
			return $tabs[$type];
		}else if( $type == ''){
			return $tabs;
		}
	}
}
?>
