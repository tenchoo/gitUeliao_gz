<?php
class todoForPost extends CWidget {
	public $assign;
	public $id;
	
	public function run() {
		$parrams = array('id'=>$this->id);
		if( !$this->assign ) {
			$url = $this->owner->createUrl('assign', $parrams);
			echo CHtml::link('订单匹配',$url);
		}
		else {
			$url = $this->owner->createUrl('assignview', $parrams);
			echo CHtml::link('查看',$url);
		}
	}
}