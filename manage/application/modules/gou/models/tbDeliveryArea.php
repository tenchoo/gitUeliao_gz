<?php
/**
 * 送货区域
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$areaId
 * @property string		$title
 *
 */

 class  tbDeliveryArea extends CActiveRecord {

	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{delivery_area}}";
	}

	public function rules() {
		return array(
			array('title','required'),
			array('title', 'length', 'max'=>10, 'min'=>1),
			array('title','safe'),
			array('title','unique'),

		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'title' => '送货区域',
		);
	}

	/**
	* 取得全部送货区域
	*/
	public function getDeliveryAreas(){
		$model = $this->findAll(
				array( 'order'=>'title asc' )
			);
		$result = array('0'=>'未定','-1'=>'已划分');
		foreach ( $model  as $val ){
			$result[$val->areaId] = $val->title;
		}
		return $result;
	}

	/**
	* 根据ID取得某个送货区域名称
	* @param integer $areaId
	* @override
	*/
	public static function gettitle( $areaId ){
		$model = self::model()->findByPk( $areaId );
		if( $model ){
			return $model->title;
		}
		return null;
	}

	/**
	* 取得全部送货区域并统计其信息
	* @param integer $state  订单的送货状态，统计使用
	*/
	public function getAllArea( $state ){
		$data = $this->findAll( array( 'order'=>'title asc' ) );

		$data = tbDeliveryArea::model()->findAll( array( 'order'=>'title asc' ) );
		$order = new tbDeliveryOrder();
		$result['states'] = $order->getStates();
		$result['state'] = array_key_exists( $state ,$result['states'] )? (int)$state : null;

		$counts = $order->groupArea( $result['state'] );
		$result['list'] =  array();
		$classAttr = array('0'=>'alert-warning','1'=>'alert-success','2'=>'alert-danger','3'=>'alert-info');
		foreach( $data as $val ){
			$val = $val->attributes;
			if( array_key_exists( $val['areaId'],$counts) ){
				$val = array_merge( $val,$counts[$val['areaId']] );
			}else{
				$val['c'] = $val['total'] = '';
				$val['deliverymanId'] = '0';
			}
			$m = substr( $val['title'], 0, 1 ) % 4 ;
			$val['class'] = isset( $classAttr[$m] )? $classAttr[$m]:'';
			$result['list'][] = $val;
		}

		$total = array_sum(  array_map( function ( $i ){ return $i['total'];},$counts ) );
		$c = array_sum(  array_map( function ( $i ){ return $i['c'];},$counts ) );



		if( isset( $counts['0'] ) ){
			$total_no = $counts['0']['total'];
			$c_no = $counts['0']['c'];

			$total_y = $total-$total_no;
			$c_y = $c-$c_no;

		}else{
			$total_y = $total;
			$c_y = $c;
			$c_no = $total_no = '';
		}


		$result['tongji'] = array(
						array('title'=>'全部','c'=>$c,'total'=>$total,'class'=>'alert-warning'),
						array('title'=>'已划分','c'=>$c_y,'total'=>$total_y,'class'=>'alert-success'),
						array('title'=>'待定的','c'=>$c_no,'total'=>$total_no,'class'=>'alert-danger'),
					);

		//新增按业务员统计的数据 2016/09/29
		$mems =  tbDeliveryman::model()->getDeliverymans();
		unset( $mems[-1] );

		//这一句必须放在前面
		$result['mems'] = $mems ;

		$memCounts = $order->groupMens( $result['state'] );
		$classAttr = array('0'=>'alert-info','1'=>'alert-success','2'=>'alert-warning');

		//需要转一下排序，按中文顺序排序
		foreach ( $memCounts as $key=>$val ){
			if( array_key_exists( $val['deliverymanId'] , $mems ) ){
				$val['deliveryman'] = $mems[$val['deliverymanId']];

				$mems[$val['deliverymanId']] = $val;
			}
		}

		$i = 0;
		foreach ( $mems as $key=>&$val){
			if( !is_array( $val) ){
				unset( $mems[$key] );
			}else{
				$val['class'] = ($val['deliverymanId']>0)?$classAttr[$i%2]:'alert-danger';
				$i++;
			}

		}
		
		if( isset( $mems['0'] )){
			array_push($mems, array_shift( $mems ));
		}
		
		$result['memCounts'] = $mems ;
		return $result;
	}

}