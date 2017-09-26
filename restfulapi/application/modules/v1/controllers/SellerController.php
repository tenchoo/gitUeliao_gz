<?php
/**
 * 供应商登陆接口
 * @author yagas@zeeeda.com
 * @version 0.1
 * @package Controller
 */
class SellerController extends Controllerv1 {

	public function init() {
		$this->authenticateToken();
	}

	/**
	* 商家登陆
	*/
	public function actionCreate() {
		$account  = $this->getRequestParams('account');
		$password = $this->getRequestParams('password');

		$seller   = tbSupplierAccount::model()->find("account=:account", [':account'=>$account]);

		if($seller instanceof CActiveRecord) {
			if($seller->checkPassword($seller->password, $password)) {
				$userInfo = [
					'uid'        => $seller->uid,
					'account'    => $seller->account,
					'type'       => 'seller',
					'supplierId' => $seller->supplierId,
					'time'       => time()
				];
//				$openId = implode('.', $userInfo);
//				$openId = base64_encode(Yii::app()->securityManager->encrypt($openId, SECURITY_MASK));
//				$openId = str_replace(['+','/','='], ['-','_','*'], $openId);
				$openId = $this->createOpenid($userInfo);
				$this->showJson(true, null, ['openid'=>urlencode($openId)]);
			}
			$seller->addError('password', Yii::t('seller', 'invalied account or passowrd'));
		}
		else {
			$seller = tbSupplierAccount::model();
			$seller->addError('account', Yii::t('seller', 'seller account not found'));
		}

		$errors = $seller->getErrors();
		$message = array_pop($errors);
		$this->showJson(false, $message[0]);
	}

	/**
	 * 获取商家信息
	 */
	public function actionIndex() {
		$params = $this->readOpenid();
		if(!$params) {
			$this->showJson(false, Yii::t('seller', 'invalied openid'));
		}

		$userInfo = tbSupplier::model()->find("supplierId=:id", [':id'=>$params['supplierId']]);
		if($userInfo) {
			$sellerInfo = $userInfo->getAttributes(['supplierId','factoryNumber','shortname','contact','phone','adddress']);
			$sellerInfo['uid'] = $params['uid'];
			$this->showJson(true, null, $sellerInfo);
		}
		else {
			$this->showJson(false, Yii::t('seller', 'not found seller info'));
		}
	}

	/**
	 * 商家修改登陆密码
	 */
	public function actionUpdate() {
		$openid      = $this->readOpenid();
		if(!$openid) {
			$this->showJson(false, Yii::t('seller', 'invalied openid'));
		}
		$password    = $this->getRequestParams('password');
		$newpassword = $this->getRequestParams('newpassword');

		$user = tbSupplierAccount::model()->findByPk($openid['uid']);
		if($user && $user->checkPassword($user->password, $password)) {
			$user->password = $newpassword;

			if($user->save()) {
				$this->showJson(true, Yii::t('seller', 'password update successful'));
			}

			$errors = $user->getErrors();
			$error = array_shift($errors);
			$this->showJson(false, Yii::t('seller', $error[0]));
		}
		else {
			$this->showJson(false, Yii::t('seller', 'old password no match'));
		}
	}
}
