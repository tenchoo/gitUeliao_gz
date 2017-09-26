<?php
/**
 * 仓库出入库历史记录表
 * @author yagas
 *
 * @property id              
 * @property tag  标记
 * @property source  来源        
 * @property orderId  单号        
 * @property warehouse_in  入库        
 * @property warehouse_out  出库        
 * @property timeline  操作时间  
 * @property operator  操作员     
 * @property surplus  结余
 */
class tbWarehouseHistory extends CActiveRecord {
	

	public static function model($className=__CLASS__) {
		return parent::model( $className );
	}

	public function tableName() {
		return '{{warehouse_history}}';
	}

	public function primaryKey() {
		return "id";
	}
}