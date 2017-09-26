<?php
/**
 * V1版本接口
 */
class V2Module extends CWebModule {
	public function init() {
		//注册对应该版本接口模型目录
		Yii::import('v2.models.*');
	}
}