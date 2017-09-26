<?php
/**
 * 呆滞级别
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$id
 * @property integer	$conditions		呆滞条件,单位为月
 * @property TIMESTAMP	$createTime		生成时间
 * @property TIMESTAMP	$updateTime		最后更新时间
 * @property string		$title			级别名称
 *
 */

 class tbGlassyLevel extends CActiveRecord {

	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{glassy_level}}";
	}

	public function rules() {
		return array(
			array('title,conditions','required'),
			array('conditions', "numerical","integerOnly"=>true,'min'=>'1'),
			array('title,conditions,logo','safe'),
			array('title,conditions','unique'),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'title' => '级别名称',
			'conditions'=>'默认时长'
		);
	}

	/**
	 * 保存前的操作
	 */
	protected function beforeSave(){
		if($this->isNewRecord){
			$this->createTime = new CDbExpression('NOW()');
		}
		$this->updateTime = new CDbExpression('NOW()');
		return true;
	}
}