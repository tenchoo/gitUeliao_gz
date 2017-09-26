<?php
class tbTaskAction extends CActiveRecord {
	public $taskName;
	public $taskRoute;
	
	public static function model($className=__CLASS__) {
		return parent::model( $className );
	}
	
	public function tableName() {
		return '{{task_action}}';
	}
	
	public function primaryKey() {
		return "actionId";
	}
}