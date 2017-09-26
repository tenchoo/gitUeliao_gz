<?php
/**
 * 接口调用登陆
 * 接口调用前需要申请调用的权限，申请通过返回token码，失败返回错误信息
 * 后续的接口调用都需要提供此token码，否则无权限对接口进行操作
 * 刚token过期后，需要重新申请新的token码
 * @author yagas
 * @version 0.1
 * @date 2016-01-14
 */
class AuthController extends CController {
	
	/**
	 * 验证接口调用权限 */
	public function actionAuthentice() {
		
	}
}