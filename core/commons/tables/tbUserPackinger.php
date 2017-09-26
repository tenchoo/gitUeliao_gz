<?php
/**
 * 分拣员数据模型
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$userId				分拣员userIdID
 * @property integer	$warehouseId		所属仓库ID
 * @property integer	$state				0正常，1删除
 *
 */


class tbUserPackinger extends CActiveRecord {

	public $packing_roleId;

	static public function model( $className=__CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{user_packinger}}";
	}

	/**
	 * 角色组校验规则
	 * @see CModel::rules()
	 */
	public function rules() {
		return array(
			array('userId,warehouseId,state','required'),
			array('state','in','range'=>array(0,1)),
			array('userId,warehouseId', "numerical","integerOnly"=>true),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'userId' => '分拣员ID',
			'warehouseId' => '所属仓库'
		);
	}

	public function setPackingRoleId(){
		$this->packing_roleId = tbConfig::model()->get( 'packing_roleId' );
	}

	/**
	* 查询/列表
	* @param array $condition 查询的条件
	* @param integer $perSize 每页显示条数
	*/
	public function search( $condition = array(),$perSize = 1 ){
		if( empty( $this->packing_roleId ) ) return array('list'=>array(),'pages'=>null);

		$model = tbRoleGroup::model()->findAll( 'roleId = :roleId and state =:state ',array( ':roleId'=> $this->packing_roleId ,':state'=>0 ) );
		$depPositionId = array_map( function( $i ){ return $i->deppositionId;},$model );
		if( empty( $depPositionId ) ){
			return array( 'list'=>array(),'pages'=>null );
		}

		$criteria = new CDbCriteria;
		if( is_array($condition) ){
			foreach ( $condition as $key=>$val ){
				if( $val=='' ) continue;

				if( $key == 'warehouseId' ){
					if( is_numeric( $val ) && $val >0  ){
						$criteria->join = ' inner join '.$this->tableName().' p on p.userId = t.userId and p.warehouseId = '.$val;
					}
				}else{
					$criteria->compare('t.'.$key,$val);
				}
			}
		}

		$criteria->compare( 't.deppositionId',$depPositionId );
		$criteria->compare( 't.state',0 );
		$model = new CActiveDataProvider('tbUser', array(
			'criteria'=>$criteria,
			'pagination'=>array('pageSize'=>$perSize,'pageVar'=>'page'),
		));

		$data = $model->getData();

		$result['list'] = array_map( function ( $i ){
							$info = $i->attributes;
							$info['warehouseId'] =  ( $i->warehouse )?$i->warehouse->warehouseId:'';
							$info['positionName'] = $i->position->positionName ;
							return $info;
							} ,$data );
		$result['pages'] = $model->getPagination();
		return $result;
	}


	/**
	*  根据仓库ID和关键词查找分拣员，返回数组，包含分拣员userId和真实姓名
	* @param integer $warehouseId 查询的仓库ID
	* @param string $username 查询的真实姓名
	*/
	public function getAllByWare( $warehouseId,$username = '' ){
		$this->setPackingRoleId();
		if( !is_numeric( $warehouseId ) || $warehouseId<1 || empty( $this->packing_roleId )  ){
			return array();
		}
		$model = tbRoleGroup::model()->findAll( 'roleId = :roleId and state =:state ',array( ':roleId'=>$this->packing_roleId ,':state'=>0 ) );
		$depPositionId = array_map( function( $i ){ return $i->deppositionId;},$model );
		if( empty( $depPositionId ) ){
			return array();
		}

		$criteria = new CDbCriteria;
		$criteria->select = 't.userId,t.username';
		$criteria->join = ' inner join '.$this->tableName().' p on p.userId = t.userId and p.warehouseId = '.$warehouseId;
		if( !empty($username) ){
			$criteria->compare( 't.username',$username,true );
		}

		$criteria->compare( 't.deppositionId',$depPositionId );
		$criteria->compare( 't.state',0 );
		$model = tbUser::model()->findAll( $criteria ) ;
		$result['list'] = array_map( function ( $i ){
							return $i->getAttributes( array('userId','username'));
							} ,$model );

		return $result;
	}
}