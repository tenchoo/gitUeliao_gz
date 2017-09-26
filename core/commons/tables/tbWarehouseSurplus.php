<?php
/**
 * 异动报表月结余
 * @author liang
 *
 * @property	integer	id    			序号
 * @property	integer	warehouseId		所属仓库ID
 * @property	string	singleNumber	单品编码
 * @property 	date 	date			结余月份
 * @property	number 	total			结余decimal(15,2)
 */

class tbWarehouseSurplus extends CActiveRecord {


	public static function model($className=__CLASS__) {
		return parent::model( $className );
	}

	public function tableName() {
		return '{{warehouse_surplus}}';
	}
}