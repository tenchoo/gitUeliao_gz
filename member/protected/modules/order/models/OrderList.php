<?php
/**
 * 前端--客户/业务员订单管理
 * @author liang
 * @version 0.1
 * @package CModel
 */
class OrderList extends CModel {
	public $userType;
	public $memberId;

	public function attributeNames() {
		return false;
	}

	public function __construct(){
		$this->userType =  Yii::app()->user->getState('usertype');
		$this->memberId = Yii::app()->user->id;
	}


	/**
	* 取得订单列表
	* @param integer $condition
	* @param array $condition
	*/
	public function getList( $type,$condition,$pageSize = 10){
		$order = new Order ();
		$tab = $order->tabs( $type );
		$condition =array_merge(  $tab['condition'],$condition );
		
		if( $this->userType =='member' ){
			$condition['memberId'] = $this->memberId ;
		}
		
		$data = $order->search( $condition,'t.createTime DESC', $pageSize );

		$com = array();
		if( $this->userType =='saleman' ) {
			foreach ($data['list'] as &$val){
				if(isset( $com[$val['memberId']] )){
					$val['companyname'] =  $com[$val['memberId']];
					continue;
				}
				$detail = tbProfileDetail::model()->findByPk( $val['memberId'] );
				if( $detail ){
					$val['companyname'] = $com[$detail->memberId] =  $detail->companyname;
				}else{
					$val['companyname'] =  '';
				}
			}
		}

		$order->setButtons( $data['list'] );

		unset( $data['units'] );
		return $data;
	}

	/**
	 * 计算总数
	 * @param  array $condition 查找条件
	 */
	public function orderCounts( $type ){
		$order = new Order ();
		$tab = $order->tabs( $type );

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
				if( is_null($val) ){
					continue ;
				}
				if( $key =='is_string' ){
					$criteria->addCondition( $val );//直接传搜索条件
				}else{
					$criteria->compare('t.'.$key,$val);
				}				
			}
		}

		$count = tbOrder::model()->count( $criteria );
		return $count;
	}

	public function tabs( $type='' ){
		$order = new Order ();
		return $order->tabs( $type );
	}
}
?>
