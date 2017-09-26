<?php
/**
 * 设置和读取验证码
 * User: yagas
 * Date: 2016/1/19
 * Time: 11:10
 * @package CModel
 */

class CaptchaStorage extends CModel {
    private $_collection;
    private static $_instance;
    private $_info;

    public function __construct($collection) {
        $this->_collection = $collection;
        if(method_exists($this, 'init')) {
            $this->init();
        }
    }

    public function init() {
        //创建mongo对象实例
        if(is_null(self::$_instance)) {
            self::$_instance = Yii::app()->mongoDB->collection('captcha');
        }
    }

    public function attributeNames() {
        return array('getCaptcha','setCaptcha');
    }

    /**
     * 读取验证码
     * @param $account
     * @return bool|string
     */
    public function getCaptcha($account) {
        $this->_info = $result = self::$_instance->findOne(array('account'=>$account));
        self::$_instance->delete(array('_id'=>$result->_id));
        if($result) {
            $create = $result->time;
            if((time()-$create) < Yii::app()->params['tokenExpire']) {
                return $result->captcha;
            }
        }
        return false;
    }

    /**
     * 存储验证码
     * @param $account
     * @param $value
     * @return bool
     */
    public function setCaptcha($account, $value) {
        $data = [
            'account' => $account,
            'captcha' => $value,
            'time'    => time()
        ];
        self::$_instance->delete(array('account'=>$account));
        $result = self::$_instance->save($data);
        if(is_null($result['err'])) {
            return true;
        }
        return false;
    }

    /**
     * 校验码是否已经过期
     * @return bool
     */
    public function isExpired() {
        if(is_null($this->_info)) {
            return true;
        }

        $create = $this->_info->time;
        if((time()-$create) > Yii::app()->params['tokenExpire']) {
            return true;
        }

        return false;
    }
}
