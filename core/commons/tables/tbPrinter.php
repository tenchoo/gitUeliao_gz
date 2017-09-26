<?php

/**
 * 打印机表模型
 *
 * @property integer	$printerId			打印机ID
 * @property string		$printerSerial		打印机编号
 * @property string		$mark				备注说明
 * @version 0.1
 * @package CActiveRecord
 */

class tbPrinter extends CActiveRecord {

	/**
	 * 构造数据库表模型单例方法
	 * @param system $className
	 * @return static
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{printer}}';
	}

	/**
	 * @return array 模型验证规则.
	 */
	public function rules()	{
		return array(
			array('printerSerial,mark,state','required'),
			array('printerSerial,mark','length','min'=>'2','max'=>'15'),
			array('state','in','range'=>array(0,1)),
			array('printerSerial,mark','safe'),
			array('printerSerial','unique'),
		);
	}

	public function attributeLabels(){
		return array(
			'printerSerial'=>'打印机编号',
			'state'=>'打印机状态',
			'mark'=>'说明',
		);
	}

	public function getAll(){
		$data = $this->findAll( array(
					'condition'=>'state=0',
					'order'=>'state asc,printerSerial asc'
					));
		$list = array();
        foreach($data as $item) {
            $list[$item->printerId] = $item->mark;
        }
		return $list;
	}


}
?>