<?php
/**
 * 会员找回登陆密码
 * @author yagas
 *
 */
class PasswordController extends Controller {
	
	/**
	 * 重置登陆密码 */
	public function actionUpdate() {
		$account  = Yii::app()->request->getPut('account');
		$captcha  = Yii::app()->request->getPut('captcha');
        $password = Yii::app()->request->getPut('password');

		$mongoDB = new CaptchaStorage('captcha');
		$token = $mongoDB->getCaptcha($account);

		if(intval($captcha) !== $token) {
			$this->showJson(false, Yii::t('restful', 'invalid captcha value'));
			Yii::app()->end();
		}

        $user = $this->account_exists($account);
        if(is_null($user)) {
            $this->showJson(false, Yii::t('restful', 'not found account'));
			Yii::app()->end();
        }

        $passwordLen = strlen($password);
        if( $passwordLen<6 || $passwordLen>16) {
            $this->showJson(false, Yii::t('restful','Password length of 6-16, must contain data and letters'));
        }

        $user->password = $user->passwordEncode($password);
        if($user->save()) {
            $this->showJson(true, Yii::t('restful', 'reset password successful'));
            Yii::app()->end();
        }
        else {
            $errors = $user->getErrors();
            $error  = array_shift($errors);
            $this->showJson(false, Yii::t('restful', $error[0]));
        }

        $this->showJson(false, Yii::t('restful', 'reset password failed'));
	}

    /**
     * 帐号是否存在
     * @param $account
     * @return mixed
     */
    private function account_exists($account) {
        $user = tbMember::model()->findByAttributes(array('phone'=>$account));
        return $user;
    }
}