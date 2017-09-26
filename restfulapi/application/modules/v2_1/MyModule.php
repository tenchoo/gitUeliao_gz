<?php
/**
 * V1版本接口
 */
class MyModule extends CWebModule {
	public function init() {
		//注册对应该版本接口模型目录
		Yii::import('v2_1.models.*');
	}
}