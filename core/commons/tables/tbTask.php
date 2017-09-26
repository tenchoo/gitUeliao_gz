<?php
class tbTask extends CActiveRecord {
	public $taskId;
	public $taskName;
	public $taskRoute;
	public $type = 'action';
	public $orderList = 0;
	public $parentId  = 0;
	
	public static function model($className=__CLASS__) {
		return parent::model( $className );
	}
	
	public function tableName() {
		return '{{task}}';
	}
	
	public function primaryKey() {
		return "taskId";
	}
	
	/**
	 * 通过动作ID获取动作地图结构
	 * @param array $taskAray
	 * @return array
	 */
	public function loadToTaskMap( $taskAray ) {
		$ids = array();
		$map = array();
		
		foreach ( $taskAray as $item ) {
			array_push( $ids, $item['taskId'] );
		}
		$tasks = tbTask::findAllByPk( $ids );
		if( $tasks ) {			
			foreach( $tasks as $task ) {
				$map[ $task->parentId ]['actions'][] = array('taskId'=>$task->taskId,'taskName'=>$task->taskName,'taskRoute'=>$task->taskRoute);
			}
		}
		
		$ControlNames = tbTask::findAllByPk( array_keys( $map ) );
		if( $ControlNames ) {
			foreach ( $ControlNames as $control ) {
				$map[ $control->taskId ]['taskName'] = $control->taskName;
			}
		}
		
		return $map;
	}
	
	public function taskTree( $parentId=0, $filters=array() ) {
		$criteria            = new CDbCriteria();
		$criteria->condition = "parentId=:pid";
		$criteria->params    = array( ':pid'=>$parentId );
		$criteria->order     = "orderList ASC";
		$result = $this->findAll( $criteria );
		foreach ( $result as &$item ) {
			$item = $item->getAttributes( array('taskId','taskName'));
			if( in_array( $item['taskId'],$filters)){
				$item['ischoose'] = true;				
			}else{
				$item['ischoose'] = false;	
			}
			$item['children'] = $this->taskTree( $item['taskId'], $filters );
		}
		return $result;
	}
}