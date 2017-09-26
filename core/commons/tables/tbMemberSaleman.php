<?php

/**
 * 会员--业务员扩展信息表
 *
 * @property integer	$memberId
 * @property integer	$printerId		打印机编号
 * @version 0.1
 * @package CActiveRecord
 */

class tbMemberSaleman extends CActiveRecord {


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
		return '{{member_saleman}}';
	}

	/**
	 * @return array 模型验证规则.
	 */
	public function rules()	{
		return array(
			array('printerId,memberId','required'),
			array('printerId,memberId', 'numerical','integerOnly'=>true),
		);
	}

	public function attributeLabels(){
		return array(
			'printerId'=>'打印机编号',
		);

	}
}
?>