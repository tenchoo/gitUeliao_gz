<?php
/**
 * 驾驶员信息表
 * @author liang
 * @version 0.1
 *
 * @property int		$driverId
 * @property int		$gender			性别：0男，1女
 * @property int		$state			状态：0正常，1删除
 * @property timestamp	$createTime
 * @property string		$phone			手机号码
 * @property string		$driverName		驾驶员姓名
 * @property string		$idcard			身份证号码
 */

class tbDriver extends CActiveRecord {

	public static function model($className=__CLASS__) {
		return parent::model( $className );
	}

	public function tableName() {
		return '{{driver}}';
	}

	public function primaryKey() {
		return "driverId";
	}

	public function rules() {
		return array(
			array('driverName,phone', 'required'),
			array('driverName', 'length','min'=>'2','max'=>'10'),
			array('idcard', 'length','min'=>'10','max'=>'18'),
			array('state,gender','in','range'=>array(0,1)),
			array('driverName,phone,idcard', 'safe'),
			array('phone', 'match','pattern'=>Regexp::$mobile),
			array('idcard','unique','criteria'=>array('condition'=>'state = 0') ),

		);
	}

	public function attributeLabels() {
		return array(
			'gender' => '性别',
			'state' => '状态',
			'driverName'=>'驾驶员姓名',
			'phone'=>'手机号码',
			'idcard'=>'身份证号码',
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
			$result[$val->driverId] = $val->driverName;
		}
		return $result;
	}

	/**
	* 查询/列表
	* @param array $condition 查询的条件
	* @param integer $perSize 每页显示条数
	*/
	public function search( $condition = array(),$perSize = 1 ){
		$criteria = new CDbCriteria;
		if( is_array($condition) ){
			foreach ( $condition as $key=>$val ){
				if( $val=='' ) continue;
				$criteria->compare('t.'.$key,$val);
			}
		}
		$criteria->compare('t.state','0');
		$model = new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array('pageSize'=>$perSize,'pageVar'=>'page'),
		));

		$result['list'] = $model->getData();
		$result['pages'] = $model->getPagination();

		$result['list'] = array_map( function($i){
			$i->gender = ($i->gender=='1')?'女':'男';
			return $i->attributes;
		} ,$result['list']);
		return $result;
	}
}
