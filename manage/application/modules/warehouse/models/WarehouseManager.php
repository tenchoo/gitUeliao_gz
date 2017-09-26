<?php
/**
 * 仓库人员设置
 * @author liang
 * @version 0.1
 * @package CFormModel
 *
 */

class WarehouseManager extends CFormModel {

	//仓库ID
	public $warehouseId;

	//区域ID
	public $positionId;

	/**
	* Declares the validation rules.
	* The rules state that account and password are required,
	* and password needs to be authenticated.
	*/
	public function rules()	{
		return array();
	}

	public function attributeLabels() {
		return array(
			'warehouseId' => '仓库ID',
			'positionId' => '区域ID',
		);
	}

	/**
	* 设置仓库管理员
	*/
	public function save( $data ){
		if( empty( $this->warehouseId ) ){
			return false;
		}

		$model = new tbWarehouseUser();

		//查找是否已是其他仓库的管理员，如果是，暂时不允许同一个人同时是两个仓库的管理员
		if( !empty( $data ) && is_array( $data ) ){
			foreach ( $data as $val ){
				$falg = $model->exists( 'warehouseId !=:wid and positionId=0 and isManage=1 and userId=:userId',
													array(':wid'=>$this->warehouseId,':userId'=>$val ) );
				if( $falg ){
					$username = tbUser::model()->getUsername( $val );
					$this->addError( 'warehouseId',Yii::t( 'warehouse','{username} is already the other warehouse manager, not allowed to manage two warehouses at the same time' ,array('{username}'=>$username) ) );
					return false;
				}
			}
		}

		$model->warehouseId = $this->warehouseId;
		$model->positionId	= 0;
		$model->isManage = 1;

		//使用事务处理，以确保这组数据全部成功插入
		$transaction = Yii::app()->db->beginTransaction();
		try {
			//删除原有的，再重新录入
			$model->deleteAllByAttributes( array('warehouseId'=>$this->warehouseId,'positionId'=>0,'isManage'=>1 ) );

			if( !empty( $data ) && is_array( $data ) ){
				foreach ( $data as $val ){
					$_model = clone $model;
					$_model->userId = $val;
					if( !$_model->save() ){
						$transaction->rollback();
						$this->addErrors( $_model->errors );
						return false;
					}
				}
			}

			$transaction->commit();
			return true;
		} catch (Exception $e) {
			$transaction->rollback(); //如果操作失败, 数据回滚
			throw new CHttpException(503,$e);
			return false;
		}
	}

	/**
	* 取得仓库管理员信息
	*/
	public function getManages(){
		if( empty( $this->warehouseId ) ) return array();

		$models = tbWarehouseUser::model()->findAll( 'warehouseId=:wid and positionId=0 and isManage=1',
													array(':wid'=>$this->warehouseId) );

		return $this->getUsers(  $models  );
	}

	/**
	* 取得仓库人员姓名信息
	*/
	private function getUsers( $models ){
		if( empty( $models ) ) return array();
		$users = array_map( function( $i ){ return $i->userId;} ,$models );
		$userModel = tbUser::model()->findAllByPk( $users );
		return array_map( function( $i ){ return $i->getAttributes( array('userId','username') );} ,$userModel );
	}

	/**
	* 取得仓库管理员所管理的仓库ID
	*/
	public function ManageWarehouse(){
		$userId = Yii::app()->user->id;
		if( empty( $userId ) ) return;

		$model = tbWarehouseUser::model()->find('userId=:userId and positionId=0 and isManage=1',
													array(':userId'=>$userId ) );
		if( $model ){
			return $model->warehouseId;
		}
	}


	/**
	* 增加归单人员，先做简单的，人员上面的约束后面调整补充。
	*/
	public function addMergeUser(){
		if( empty( $this->warehouseId ) ){
			return false;
		}

		$userId = Yii::app()->request->getPost('userId');

		//判断用户是否存在
		$userModel = tbUser::model()->findByPk( $userId );
		if( !$userModel ){
			$this->addError( 'userId',Yii::t('warning','Abnormal parameter') );
			return false;
		}

		//查找仓库是否已有此ID人员记录
		$model = tbWarehouseUser::model()->find( 'warehouseId=:wid and userId= :userId ',
								array(':wid'=>$this->warehouseId,':userId'=>$userId ) );
		if( $model ){
			if( $model->isMerge =='1' ) return true;
			$model->isMerge = 1;
		}else{
			$model = new tbWarehouseUser;
			$model->warehouseId = $this->warehouseId;
			$model->positionId	= 0;
			$model->isManage = 0;
			$model->isMerge = 1;
			$model->userId = $userId;
		}

		if( !$model->save() ){
			$this->addErrors( $model->errors );
			return false;
		}

		return true;
	}

	/**
	* 增加归单人员，先做简单的，人员上面的约束后面调整补充。
	*/
	public function mergeList(){
		if( empty( $this->warehouseId ) ) return array();

		$models = tbWarehouseUser::model()->findAll( 'warehouseId=:wid and isMerge=1',
													array(':wid'=>$this->warehouseId) );

		return $this->getUsers(  $models  );
	}

	/**
	* 删除归单人员，先做简单的，人员上面的约束后面调整补充。
	*/
	public function delMerge( $userId ){
		if( empty( $this->warehouseId ) ) return false;

		return tbWarehouseUser::model()->updateAll( array('isMerge'=>0),
													'warehouseId=:wid and isMerge=1 and userId=:userId',
													array(':wid'=>$this->warehouseId,':userId'=>$userId ) );
	}


}