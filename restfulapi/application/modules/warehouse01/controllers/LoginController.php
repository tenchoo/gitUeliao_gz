<?php

/**
 * 后台账号登陆
 * User: liang
 * Date: 2016/1/19
 * Time: 16:01
 */
class LoginController extends MController {

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

        $authenticate = new MUserIdentity($user, $passwd);
        if ($authenticate->authenticate()) {
			$this->userId = $authenticate->getState('userId');
            $openId = substr(sha1('manage_'.$user . $passwd.rand( 1000,9999)), 0, 20);
            $openId .= '_' . $device;


			$this->getWarehouseId();
            $userInfo = array(
                'openid' => $openId,
                'device' => $device,
                'userId' => $this->userId,
                'usertype' => $this->userType,
				'roles' => $authenticate->getState('roles'),
                'username' => $authenticate->getState('username'),
				'isAdmin' => $authenticate->getState('isAdmin'),
                'os' => $os,
                'version' => $version,
				'serverWarehouseId'=>$this->serverWarehouseId,
				'warehouse'=>$this->getWarehouseTitle(),
            );
            $this->_source = $os;
            Yii::app()->openidCache->add($openId, $userInfo);

            //不将device显示到返回值中
            unset($userInfo['device'],$userInfo['os'],$userInfo['version'],$userInfo['"roles'],$userInfo['"isAdmin']);
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

	private function getWarehouseId(){
		//查找当前服务仓库
		$models = tbWarehouseUser::model()->findAll( 'userId=:userId',array(':userId'=>$this->userId ) );
		if( empty( $models ) ) return ;

		foreach ( $models as $val ){
			$this->serverWarehouseId = $val->warehouseId;

			if( $val->positionId == '0' && $val->isManage == '1' ){
				$this->userType = 'manager';
				return ;
			}

			if( $val->isMerge == '1' ){
				$this->userType = 'merge';
				return ;
			}
		}

		$this->userType = 'packing';
	}
	
	private function getWarehouseTitle(){
		if( !empty( $this->serverWarehouseId ) ){
			$warehouse = tbWarehouseInfo::model()->findByPk( $this->serverWarehouseId );
			if( $warehouse ){
				return $warehouse->title;
			}			
		}		
		return '';		
	}
}