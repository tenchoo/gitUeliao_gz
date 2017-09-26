<?php
/**
 * V1版本接口
 */
class V1Module extends CWebModule {
	public function init() {
		//注册对应该版本接口模型目录
		Yii::import('v1.models.*');
	}
}