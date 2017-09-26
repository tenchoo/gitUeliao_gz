<?php
/**
 * 显示客服列表
 * @author liang
 * @package CWidget
 * @version 0.1.1
 */
class CSList extends CWidget {
	public $num;

	public function run(){
		ob_start();

		$model = tbCS::model()->findAll('state = 1');
		foreach ( $model as $val ){
			echo '<li>'.$val->kefu($val->csAccount,$val->type,$val->csName).'</li>';
		}
		ob_end_flush();
	}

}