<?php
/**
 * 当前位置组件
 * User: yagas
 * Date: 2016/3/16
 * Time: 9:57
 */
class whereis extends CWidget {

    public function run() {
        if(!is_null($this->owner->breadcrumb)) {
            $breadcrumb = explode(',', $this->owner->breadcrumb);
            echo '<ol class="breadcrumb">';
            foreach($breadcrumb as $menu) {
                echo CHtml::tag('li',array(),$menu);
            }
            echo '</ol>';
            return;
        }

        $menuInfo = str_split($this->owner->getRouteId(), 3);
        $ids = [];
        $base = '';
        foreach($menuInfo as $item) {
			
			
            $base .= array_shift($menuInfo);
            $id = str_pad($base, 12, '0');
            if(!array_search($id, $ids)) {
                array_push($ids, $id);
            }
        }

        $menus = tbSysmenu::model()->findAllByPk($ids);
        echo '<ol class="breadcrumb">';
        foreach($menus as $menu) {
			if( $menu->type == 'menu' ){
				$word =  CHtml::link( $menu->title,$this->owner->createUrl( $menu->route ) ); 
			}else{
				$word = $menu->title;
			}           
            echo CHtml::tag('li',array(),$word);
        }
        echo '</ol>';
    }
}