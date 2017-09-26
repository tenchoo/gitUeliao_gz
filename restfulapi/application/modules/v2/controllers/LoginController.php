<?php

/**
 * 会员登陆
 * User: yagas
 * Date: 2016/1/19
 * Time: 16:01
 */
class LoginController extends Controller {

    /**
     * 取得用户信息
     * @param string account 帐号
     * @param string password 密码
     * @param string device 设备号
     */
    public function actionCreate() {
        $user    = Yii::app()->request->getPost('account');
        $passwd  = Yii::app()->request->getPost('password');
        $device  = Yii::app()->request->getPost('device');
        $os      = Yii::app()->request->getPost('os');
        $version = Yii::app()->request->getPost('ver');
        $cid     = Yii::app()->request->getPost('cid');

        if (empty($user) || empty($passwd) || empty($device)) {
            goto failed_to_login;
        }

        $authenticate = new UserIdentity($user, $passwd);
        if ($authenticate->authenticate()) {

            $openId = substr(sha1($user . $passwd), 0, 20);
            $openId .= '_' . $device;

            $userInfo = array(
                'openid' => $openId,
                'device' => $device,
                'memberId' => $authenticate->getState('memberId'),
                'usertype' => $authenticate->getState('usertype'),
                'nickName' => $authenticate->getState('nickName'),
                'os' => $os,
                'version' => $version,
                'icon' => Yii::app()->params['domain_images'] . $authenticate->getState('icon'),
            );
            $this->_source = $os;

            //记录登陆设备CID信息
            tbMemberDevice::log($authenticate->getState('memberId'), $cid, $authenticate->getState('usertype'), $os);

            Yii::app()->openidCache->add($openId, $userInfo);

            //不将device显示到返回值中
            unset($userInfo['device']);
            $this->showJson(true, Yii::t('restful', 'logined'), $userInfo);
            Yii::app()->end();
        }else{
			if( $authenticate->errorCode == UserIdentity::ERROR_DISABLE_LOGIN ){
				$this->message = Yii::t('user','Your account is frozen, please contact customer service.');
			}else {
				$this->message = Yii::t('user','The user name or password is wrong');
			}
		}

        failed_to_login:
        $this->state   = false;
		if( empty( $this->message )){
			$this->message = Yii::t('restful', 'invalid user or password value');
		}       
        $this->showJson();
    }
}