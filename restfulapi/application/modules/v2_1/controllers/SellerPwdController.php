<?php
/**
 * 供应商修改登陆密码
 * User: yagas
 * Date: 2016/5/25
 * Time: 19:06
 * @version 0.1
 */

class SellerPwdController extends Controller {

    private static $collections = [];

    public function init() {
//		$this->authenticateToken();
    }

    /**
     * 首先进行字段非空校验
     * 对短信验证码进行校验
     * 校验两次输入的密码是否一致
     * 用户是否存在
     * 重置密码是否成功
     */
    public function actionUpdate() {
        $fileds = array_fill_keys(['phone','code','password','comfirmpassword'], null);
        foreach($fileds as $key=>$v) {
            $this->checkParam($key);
            $fileds[$key] = $this->getRequestParams($key);
        }
        extract($fileds);

        if(strnatcmp($password, $comfirmpassword)!==0) {
            $this->showJson(false, Yii::t('restful', 'comfirmpassword no match'));
        }

        $captcha = $this->getCaptcha($phone);
        if(!$captcha || strnatcmp($captcha['captcha'], $code)!==0) {
            $this->showJson(false, Yii::t('restful', 'invalid captcha value'));
        }

        $user = tbSupplierAccount::model()->findByPhone($phone);
        if(is_null($user)) {
            $this->showJson(false, Yii::t('restful', 'not found account'));
        }

        $user->password = $password;
        if(!$user->save()) {
            $errors = $user->getErrors();
            $error = array_shift($errors);
            $this->showJson(false, Yii::t('restful', $error[0]));
        }

        $this->mongoDB('captcha')->remove(['account'=>$phone]);
        $this->showJson(true, Yii::t('restful', 'reset password successful'));
    }

    /**
     * 验证字段不能为空
     * @param $filed
     */
    private function checkParam($filed) {
        if(!$this->getRequestParams($filed)) {
            $this->showJson(false, Yii::t('restful','Not found {filed}', ['{filed}'=>$filed]));
        }
    }

    /**
     * 获取mongoDB数据实例
     * @param string $collection 数据集合
     * @param string $hostInfo 主机信息array('mongodb://localhost:27017', 'dbname')
     * @return CMongoDB
     */
    public function & mongoDB($collection, $hostInfo=null) {
        if(!array_key_exists($collection, self::$collections)) {
            $mongoDB = Yii::app()->mongoDB->getMongoDB();
            self::$collections[$collection] = $mongoDB->selectCollection(Yii::app()->mongoDB->dbname, $collection);
        }

        return self::$collections[$collection];
    }
}