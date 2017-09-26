<?php
/**
 * 物流公司配置信息
 * @author liang
 * @version 0.1
 * @package CAction
 */
class ajaxExpressinfo extends CAction {

	public function run() {		
		$model = ExpressCom::model()->findAll();
		$data = json_decode(CJSON::encode($model),TRUE);
		$json = new AjaxData( true ,null,$data);
		echo $json->toJson();
		Yii::app()->end();
	}
}