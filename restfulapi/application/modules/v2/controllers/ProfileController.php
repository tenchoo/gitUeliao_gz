<?php
/**
 * 获取和修改会员信息接口
 * @author yagas
 *
 */
class ProfileController extends Controller {
	
	public function actionIndex() {
		$openId = Yii::app()->request->getQuery('openid');
		if(!is_null($openId)) {
			$profile = Yii::app()->openidCache->get($openId);
			$this->showJson(true, null, $profile);
		}
		return $this->showJson(false, Yii::t('restful','session expired'));
	}
	
	public function actionUpdate() {
		$openId   = $this->getRequestParams('openid');
		$nickname = Yii::app()->request->getPut('nickname');
		$icon     = Yii::app()->request->getPut('icon');
		
		if(!is_null($openId)) {
			if(empty($nickname)){
				$this->showJson(true, Yii::t('msg','Missing parameter'));
			}
			
			$profile = Yii::app()->openidCache->get($openId);
			if($profile) {
				$memberId = $profile['memberId'];

				$member = tbMember::model()->findByPk($memberId);
				if($member instanceof CActiveRecord) {
					$member->nickName = $nickname;
					if(!$member->save()) {
						$error = $member->getErrors();
						$error = array_shift($error);
						Yii::log($error[0], CLogger::LEVEL_ERROR, 'change nickname');
						$this->showJson(false, "failed for update profile");
					}
				}
				
				$info = tbProfile::model()->findByPk($memberId);
				if($info instanceof CActiveRecord) {
					$info->icon = $icon;
					if(!$info->save()) {
						$error = $info->getErrors();
						$error = array_shift($error);
						Yii::log($error[0], CLogger::LEVEL_ERROR, 'change icon');
						$this->showJson(false, "failed for update profile");
					}
				}
				
				$this->showJson(true, "profile update successful");
			}
		}
		return $this->showJson(false, Yii::t('restful','session expired'));
	}
}