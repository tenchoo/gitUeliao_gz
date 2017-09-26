<?php
/**
 * 车辆信息
 * @author liang
 * @version 0.1
 *
 * @property int		$vehicleId		车辆编号
 * @property int		$state			状态：0正常，1删除
 * @property timestamp	$createTime
 * @property string		$plateNumber	车牌号
 */

class tbVehicle extends CActiveRecord {

	public static function model($className=__CLASS__) {
		return parent::model( $className );
	}

	public function tableName() {
		return '{{vehicle}}';
	}

	public function primaryKey() {
		return "vehicleId";
	}

	public function rules() {
		return array(
			array('plateNumber', 'required'),
			array('plateNumber', 'length','min'=>'7','max'=>'7'),
			array('state','in','range'=>array(0,1)),
			array('plateNumber', 'safe'),
			array('plateNumber', 'unique'),
		);
	}

	public function attributeLabels() {
		return array(
			'plateNumber' => '车牌号',
			'state' => '状态',
		);
	}

	public function beforeSave() {
		if($this->isNewRecord){
			$this->createTime = new CDbExpression('NOW()');
		}
		return true;
	}

	/**
	* 取得所有车辆
	*/
	public function getAll(){
		$result = array();
		$data = $this->findAll( 'state = 0 ' );
		foreach ( $data as $val ){
			$result[$val->vehicleId] = $val->plateNumber;
		}
		return $result;
	}
}
