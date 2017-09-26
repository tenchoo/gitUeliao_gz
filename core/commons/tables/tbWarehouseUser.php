<?php
/**
 * 分拣区域
 *
 * @property int		$id
 * @property int		$warehouseId  所属仓库ID
 * @property int		$positionId	  分区Id
 * @property int		$userId		  分拣员id
 * @property tinyint	$isManage     是否为负责人，0表示分拣员，1表示负责人
 */
class tbWarehouseUser extends CActiveRecord {


  public static function model($className=__CLASS__) {
    return parent::model( $className );
  }

  public function tableName() {
    return '{{warehouse_user}}';
  }

  public function rules() {
    return array(
      array('warehouseId,positionId,userId', 'required'),
      array('isManage','in','range'=>array(0,1)),
      array('warehouseId,userId', "numerical","integerOnly"=>true,'min'=>1),
	  array('positionId', "numerical","integerOnly"=>true,'min'=>0),
    );
  }

  public function attributeLabels() {
    return array(
      'warehouseId' => '所属仓库ID',
      'positionId' => '分区Id',
      'userId'=>'分拣员id',

    );
  }

  /**
  * 职责
  */
  public function task(){
    return array('0'=>'分拣员','1'=>'负责人');
  }
}