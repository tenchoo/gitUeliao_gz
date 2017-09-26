<?php
/**
 * 仓库出入库历史记录表
 * @author yagas
 *
 * @property id    序号        
 * @property singleNumber  单品编码  
 * @property date 结余日期  
 * @property total 结余
 */
class tbWarehouseHistoryTotal extends CActiveRecord {
	

	public static function model($className=__CLASS__) {
		return parent::model( $className );
	}

	public function tableName() {
		return '{{warehouse_history_total}}';
	}

	public function primaryKey() {
		return "id";
	}
}