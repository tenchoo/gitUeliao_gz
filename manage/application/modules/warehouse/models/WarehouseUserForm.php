<?php
/**
 * 仓库分拣员设置
 * @author xiaomo
 * @version 0.1
 * @package CFormModel
 */
class WarehouseUserForm extends CFormModel {

	public $warehouseId;

	//区域ID
	public $positionId;

  /**
  * 分拣列表
  */
  public function getlist($positionId){
      $criteria = new CDbCriteria();
      $criteria->compare ('positionId' , $positionId );
      $positions = tbWarehouseUser::model()->findAll( $criteria );
      //职责
      $data['list'] = array_map( function($v){
              $user = tbUser::model()->getUsername($v->userId);
              return array(
                            'id'=>$v->id,
                            'user'=>$user,
							'userId'=>$v->userId,
                            'isManage'=>$v->isManage,
                            'createTime'=>$v->createTime,

                    );
                  } ,$positions );
      return $data;

  }

	public function delPackingUser( $userId ){
		if( empty( $this->warehouseId ) || empty( $this->positionId ) ) return false;

		$user = tbWarehouseUser::model()->find( 'warehouseId=:w and positionId=:p and userId=:userId',
										array( ':w'=>$this->warehouseId,':p'=>$this->positionId,':userId'=>$userId,)
										);

		if( !$user ) return false;
		if( $user->isMerge == '1' ){
			$user->positionId = 0;
			return $user->save();
		}else{
			return $user->delete();
		}
	}

	/**
	* 添加分拣员--初始版本，后续调整规则 
	*/
	public function addPackingUser(){
		if( empty( $this->warehouseId ) || empty( $this->positionId ) ) return false;

		$userId = Yii::app()->request->getPost('userId');

		//判断用户是否存在
		$userModel = tbUser::model()->findByPk( $userId );
		if( !$userModel ){
			$this->addError( 'userId',Yii::t('warning','Abnormal parameter') );
		}

		$model = new tbWarehouseUser();

		//查找其是否为仓库管理员，若是，不允许增加为分拣员
		$isManage = $model->exists( 'userId= :userId and positionId = 0 and isManage = 1' ,
									array(':userId'=>$userId ) );

		if( $isManage ){
			$this->addError( 'userId',Yii::t('warehouse','{name} for the warehouse manager, can not be added to the sorting clerk',array('{name}'=>$userModel->username) ) );
			return false;
		}

		//查找是否已经添加为此仓库的分拣员
		$has = $model->exists( 'userId= :userId and positionId = :p and userId=:userId',
							 array( ':w'=>$this->warehouseId,':p'=>$this->positionId,':userId'=>$userId ) );

		if( $has ){
			$this->addError( 'userId',Yii::t('warehouse','{name} has been added',array('{name}'=>$userModel->username) ) );
			return false;
		}

		$model->warehouseId = $this->warehouseId;
		$model->positionId	= $this->positionId;
		$model->isManage = 0;
		$model->isMerge = 0;
		$model->userId = $userId;

		if( !$model->save() ){
			$this->addErrors( $model->errors );
			return false;
		}

		return true;
	}
	
	/**
	* 设置分拣负责人
	*/
	public function setMamage( $userId ){
		if( empty( $this->warehouseId ) || empty( $this->positionId ) ) return false;
		
		$model = new tbWarehouseUser();
		$model->updateAll( array( 'isManage'=>'0' ),
							 'warehouseId=:w and positionId=:p',
							 array( ':w'=>$this->warehouseId,':p'=>$this->positionId )
						);
		$model->updateAll( array( 'isManage'=>'1' ),
							 'warehouseId=:w and positionId=:p  and userId=:userId',
							 array( ':w'=>$this->warehouseId,':p'=>$this->positionId,':userId'=>$userId )
						);
		return true;
	}
}