<?php
/**
 * 仓库管理---归单人员调度
 * @author liang
 * @version 0.1
 * @package CFormModel
 */
class MergeForm extends CFormModel {
	//仓库ID
	public $warehouseId;

 	/**
	* @access 归单人员调度
	*/
	public function scheduling( $users ){
		$userId = Yii::app()->request->getPost('userId');
		if( !array_key_exists( $userId, $users ) ){
			$this->addError( 'userId',Yii::t('warning','Abnormal parameter') );
			return false;
		}

		$ids = explode(',',Yii::app()->request->getPost('ids') );
		if(  empty( $ids ) ){
			$this->addError( 'userId',Yii::t('warehouse','No data') );
			return false;
		}
		foreach( $ids as $val ){
			if( (int)$val != $val && $val<1 ){
				$this->addError( 'userId',Yii::t('warning','Abnormal parameter') );
				return false;
			}
		}

		tbOrderMerge::model()->updateByPk( $ids, array('mergeUserId'=>$userId),
									'warehouseId=:wid and state = 0',
									array(':wid'=>$this->warehouseId ) );
		return true;
	}


	/**
	 * 分拣单列表 -- 后台
	 * @param  array $condition 查找条件
	 * @param  string $order  排序
	 */
	public function search( $condition = array() ){
		$pageSize = tbConfig::model()->get( 'page_size' );
		$criteria = new CDbCriteria;

		if(!empty($condition)){
			foreach ($condition as $key=>$val){
				if( $val === '' || is_null( $val ) ){
					continue ;
				}
				if( $key =='string' ){
					$criteria->addCondition($val);
				}else{
					$criteria->compare('t.'.$key,$val);
				}
			}
		}

		$criteria->order = 't.createTime ASC';
		$model = new CActiveDataProvider('tbOrderMerge', array(
			'criteria'=>$criteria,
			'pagination'=>array('pageSize'=>$pageSize,'pageVar'=>'page'),
		));

		$data = $model->getData();
		$return['list'] = array();
		if( $data ){
			foreach ( $data as $val) {
				$info = $val->attributes;
				$info['type'] = $val->actionTypes( $val->actionType);
				$return['list'][] = $info;
			}
		}
		$return['pages'] = $model->getPagination();
		return $return;
	}

	public function doneList( $condition = array() ){
		$data = $this->search( $condition );

		$warehouse = tbWarehouseInfo::model()->getAll();

		$OrderModel = new tbOrder();
		$OrderClass = new Order();
		foreach ( $data['list'] as &$val ){
			$order = $OrderModel->findByPk( $val['orderId'] );
			if( !$order ){
				$val['orderTime'] = '';
				$val['Dwarehouse'] = '';
				$val['deliveryMethod'] = '';
				$val['companyname'] = '';
			}else{
				$val['orderTime'] = $order->createTime;
				$val['Dwarehouse'] = isset( $warehouse[$order->warehouseId] )?$warehouse[$order->warehouseId]:'';
				$val['deliveryMethod'] = $OrderClass->deliveryMethod( $order->deliveryMethod );

					$val['sortHouseId']= isset($order->warehouseId )?$order->warehouseId:'';//发货仓库ID

				$member = $OrderClass->getMemberDetial( $order->memberId );
				$val['companyname'] = $member['companyname'];
			}
		}
		return $data;
	}



	public function getOrderInfo( $id,$state = null ){
		if( !is_numeric( $id ) || $id<1 ) return ;

		$model = tbOrderMerge::model()->findByPk( $id );
		if( !$model ) return;

		return $this->setData( $model );
	}

	public function setData( $model ){
		$data = $model->attributes;
		$OrderModel = tbOrder::model()->findByPk( $model->orderId );

		$OrderClass = new Order();

		$warehouse = tbWarehouseInfo::model()->getAll();

		$data['orderTime'] = $OrderModel->createTime;
		//发货仓库ID
		$data['DwarehouseId'] = $OrderModel->warehouseId;
		$data['Dwarehouse'] = isset( $warehouse[$OrderModel->warehouseId] )?$warehouse[$OrderModel->warehouseId]:'';
		$data['deliveryMethod'] = $OrderClass->deliveryMethod( $OrderModel->deliveryMethod );
		$data['memo'] = $OrderModel->memo;
		$data['order'] = $OrderModel->attributes;

		$member = $OrderClass->getMemberDetial( $OrderModel->memberId );
		$data['companyname'] = $member['companyname'];

		$data['products'] = array_map( function ( $i ){
								return $i->attributes;
							},$OrderModel->products);
		$data['warehouses'] = $warehouse;
		return $data ;
	}
}