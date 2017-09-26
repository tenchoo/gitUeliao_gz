<?php
/**
 * 仓库信息
 * @author liang
 * @version 0.1
 * @package Controller
 */
class WarehouseController extends MController {

	/**
	* 一次性返回该仓库的所有分区和仓位
	示例：
	{"state":true,"message":"","data":{"wid":"1","title":"白沟总仓","areas":[
	{"id":"2","title":"A区","positions":[{"positionId":"2","title":"AL092"},{"positionId":"4","title":"AL092"}]},
	{"id":"3","title":"B区","positions":[{"positionId":"2","title":"BL092"},{"positionId":"4","title":"BL092"}]}
	]}}
	*/
	public function actionIndex(){
		if( empty( $this->serverWarehouseId ) ){
			$this->notFound();
		}

		$warehouse = tbWarehouseInfo::model()->findByPk( $this->serverWarehouseId );
		if( !$warehouse ){
			$this->notFound();
		}

		$data['wid']   = $warehouse->warehouseId ;
		$data['title'] = $warehouse->title;


		//取得当前仓库下的所有分区和仓位
		$positions = tbWarehousePosition::model()->findAll( array(
							'condition'=>'warehouseId=:w and state=:s',
							'params'=>array(':w'=>$warehouse->warehouseId,':s'=>'0'),
							'order'=>' parentId asc'
							)  );
		$areas = array();
		foreach ( $positions as $val ){
			if( $val->parentId == '0' ){
				$areas[$val->positionId] = array( 'areaId'=>$val->positionId,'title'=>$val->positionId,'positions'=>array() );
			}else{
				$areas[$val->parentId]['positions'][] =  array( 'positionId'=>$val->positionId,'title'=>$val->positionId );
			}
		}

		$data['areas'] = array_values( $areas );
		$this->state = true;
		$this->data = $data;
		$this->showJson();
	}
}