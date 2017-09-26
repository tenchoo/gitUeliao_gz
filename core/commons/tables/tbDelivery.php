<?php
/**
 * 产品销售订单发货单
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$deliveryId			发货单ID
 * @property integer	$orderId			订单ID
 * @property integer	$userId				后台操作人userId
 * @property integer	$state				状态：0未收货，1已收货
 * @property integer	$logistics			物流公司编号
 * @property integer	$receivedType		确认收货来源；0前台确认收货，1后台确认收货。
 * @property integer	$receivedUserId		收货操作人userId
 * @property timestamp	$createTime			发货时间
 * @property timestamp	$receivedTime		收货操作时间
 * @property string		$logisticsNo		物流编号(快递运单号)
 * @property string		$address			收货地址
 *
 */
 class tbDelivery extends CActiveRecord {

	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{order_delivery}}";
	}

	public function primaryKey() {
        return 'deliveryId';
    }

	public function relations(){
		return array(
			'order'=>array(self::BELONGS_TO,'tbOrder','orderId'),
			'detail'=>array(self::HAS_MANY,'tbDeliveryDetail','deliveryId'),
			'operator'=>array(self::BELONGS_TO,'tbUser','', 'on' => 't.userId=operator.userId','select'=>'username'),
		);
	}

	public function rules() {
		return array(
			array('orderId,address','required'),
			array('logistics,logisticsNo','required','on'=>'logistics'),
			array('orderId,logistics', "numerical","integerOnly"=>true),
			array('logisticsNo,address', "safe"),
			array('orderId', "unique"),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'deliveryId' => '发货单ID',
			'orderId' => '订单ID',
			'logistics' => '物流公司',
			'logisticsNo' => '物流编号',
			'address' => '收货地址',
		);
	}


	/**
	 * 查找订单
	 * @param  array $condition 查找条件
	 * @param  integer $pageSize 每页显示条数
	 */
	public static function search( $condition = array() ,$order = '',$pageSize = 10 ,$userType = '' ){
		$criteria = new CDbCriteria;
		$criteria->select = 't.deliveryId,t.orderId,t.state';

		if(!empty($condition)){
			foreach ($condition as $key=>$val){
				if( $val == '' ){
					continue ;
				}

				if( $key =='createTime1' ){
					$criteria->addCondition("t2.createTime>'$val'");
				}else if( $key =='createTime2' ){
					$createTime2 = date("Y-m-d H:i:s",strtotime( $val )+86400 ) ; //包含选择的当天
					$criteria->addCondition("t2.createTime<'$createTime2'");
				}else if( $key =='orderId' ){
					$criteria->compare('t.'.$key,$val,true);
				}else{
					$criteria->compare('t.'.$key,$val);
				}
			}
		}

		if( $userType =='saleman' ){
			$criteria->addCondition( 't2.userId = '.Yii::app()->user->id);
			$criteria->order = ' t.createTime desc ';
		}else if( $userType =='member' ){
			$criteria->addCondition( 't2.memberId = '.Yii::app()->user->id);

			$criteria->order = ' t.createTime desc ';
		}else{
			$criteria->order = ' t.createTime ASC ';
		}

		$criteria->addCondition( 't2.state not in (0,6,7)');
		$criteria->join = 'inner join {{order}} t2 on ( t.orderId=t2.orderId )'; //连接表
		$model = new CActiveDataProvider('tbDelivery', array(
			'criteria'=>$criteria,
			'pagination'=>array('pageSize'=>$pageSize,'pageVar'=>'page'),
		));

		$data = $model->getData();
		$result = array();
		$orderIds = $orders= array();
		foreach ( $data as $key=>$val ){
			$orderIds[] = $val->orderId;
			$result[$key]['deliveryId'] = $val->deliveryId;
			$result[$key]['deliveryState'] = $val->state;
			$result[$key]['orderId'] = $val->orderId;
		}

		if( !empty( $orderIds ) ){
			$criteria = new CDbCriteria;
			$criteria->compare('t.orderId',$orderIds);
			$orderModel = tbOrder::model()->with('user')->findALL($criteria);
			foreach ( $orderModel as $key=>$val ){
				$orders[$val->orderId] = $val->attributes;
				$orders[$val->orderId]['username'] = '';
				$orders[$val->orderId]['paymemt'] = array();
				foreach( $val->products as $pval ){
					$orders[$val->orderId]['products'][] = $pval->attributes;
				}
				if( $val->user ){
					$orders[$val->orderId]['username'] = $val->user->username;
				}

				foreach( $val->paymemt as $payval ){
					$orders[$val->orderId]['paymemt'][] = $payval->attributes;
				}
			}
		}
		foreach ( $result as &$val ){
			$id = $val['orderId'];
			$val = array_merge( $val,$orders[$id]);
		}

		$return['list'] = $result;
		$return['pages'] = $model->getPagination();
		return $return;
	}

	/**
	 * 保存前的操作
	 */
	protected function beforeSave(){
		if($this->isNewRecord){
			$this->createTime = new CDbExpression('NOW()');
			$this->userId = Yii::app()->user->id;
		}
		return true;
	}

	/**
	 * 取得物流信息
	 * @param  integer $orderId		订单ID
	 * @param  integer $deliveryId	发货单ID
	 */
	public function getLogistics( $orderId = '',$deliveryId= '' ){
		if( empty( $orderId ) &&  empty( $deliveryId ) ) return ;

		$criteria = new CDbCriteria;
		if( $orderId ){
			$criteria->compare('orderId', $orderId);
		}

		if( $deliveryId ){
			$criteria->compare('deliveryId', $deliveryId);
		}

		$logistics = array();
		$model = $this->findAll( $criteria );

		foreach ( $model as $key=>$val ){
			$logistics[] = $this->getLogisticsInfo( $val->logistics,$val->logisticsNo,$val->address );
		}

		return $logistics;
	}

	/**
	 * 取得物流详情
	 * @param  integer $logistics	物流公司编号
	 * @param  integer $logisticsNo	物流编号
	 * @param  string $address		收货地址
	 */
	public function getLogisticsInfo( $logistics,$logisticsNo,$address ){
		$data = array('logistics'=>$logistics,'logisticsNo'=>$logisticsNo,'address'=>$address);
		$tbLogistics = tbLogistics::model()->findOne( $logistics );
		if( $tbLogistics ){
			$data['com'] = $tbLogistics['title'];
			$m = Yii::app()->params['SLD_member'];
			$memberApi = new ApiClient($m,'service');
			$url = $memberApi->createUrl('/ajax/default/index',array('action'=>'express','com'=>$tbLogistics['mark'],'nu'=>$logisticsNo));
			$content = $memberApi->fetchUrl($url,null,false);
			$content = json_decode( $content,true );
			if( $content['state'] == true ){
				$data['detail'] = $content['data'];
			}
			return $data;
		}
	}



}