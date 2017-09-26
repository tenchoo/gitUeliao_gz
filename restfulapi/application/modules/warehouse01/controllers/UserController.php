<?php
/**
 *
 * @author liang
 * @version 0.1
 * @package Controller
 */
class UserController extends MController {

	/**
	* 退出登录
	*/
	public function actionDelete() {
		$openid = $this->getRequestParams('openid');
		if(is_null($openid)) {
			$this->showJson(false, Yii::t("restful", "Not found {filed}", ['{filed}'=>'openid']));
		}

		$info = Yii::app()->openidCache->get($openid);
		if($info) {
			Yii::app()->openidCache->delete($openid);
			return $this->showJson(true, Yii::t('restful','logout successfully'));
		}
		return $this->showJson(false, Yii::t('restful','faild to logout'));
	}

}