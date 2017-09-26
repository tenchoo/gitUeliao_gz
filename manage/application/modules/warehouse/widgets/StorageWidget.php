<?php
/**
 * Created by PhpStorm.
 * User: yagas
 * Date: 2015/11/26
 * Time: 12:41
 */
class StorageWidget extends CWidget {

    public $root;

    public function run() {
        $action = $this->owner->action->id;
        if( $action === 'pedit' ) {
            $this->showFetchHouse();
        }
        else {
            $this->showAllHouse();
        }
    }

    private function showAllHouse() {
        $data = array('default'=>'请选择');
        echo CHtml::dropDownList('','default',$data,array('class'=>'form-control input-xs cate1'));
        echo CHtml::dropDownList('','default',$data,array('class'=>'form-control input-xs cate2'));
        echo CHtml::dropDownList('','default',$data,array('class'=>'form-control input-xs cate3'));
    }

    private function showFetchHouse() {
        $data = array('default'=>'请选择');
        $storages = tbWarehouse::model()->findAllByAttributes(array('parentId'=>0));
        
        if( $storages ) {
        	$firstOptions = array();
        	foreach ( $firstOptions as $item ) {
        		$row = array(
        			$item->warehouseId,
        			$item->title
        		);
        		array_push( $firstOptions, $row );
        	}
        }

        echo CHtml::dropDownList('',$this->root->warehouseId,$data,array('class'=>'form-control input-xs cate1','disabled'=>'disabled'));
        
        $data = array('default'=>'请选择');
        echo CHtml::dropDownList('','default',$data,array('class'=>'form-control input-xs cate2'));
        echo CHtml::dropDownList('','default',$data,array('class'=>'form-control input-xs cate3'));
    }
}