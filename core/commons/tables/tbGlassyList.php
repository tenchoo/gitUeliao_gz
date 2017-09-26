<?php
/**
 * 呆滞产品报表---由程序生成数据，不能添加修改
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$id
 * @property integer	$productId		产品ID
 * @property integer	$state			状态：0正常，1已加入尾货销售
 * @property integer	$warehouseId	所属仓库
 * @property decimal	$totalNum		呆滞数量
 * @property timestamp	$lastSaleTime	最后销售时间
 * @property string		$singleNumber	单品编码
 *
 */

 class tbGlassyList extends CActiveRecord {

	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{glassy_list}}";
	}
}