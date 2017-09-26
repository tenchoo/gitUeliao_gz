<?php
class tbRestfulAccount extends CActiveRecord {
	public $id;
	public $server;
	public $account;
	public $password;
	public $salt;
	public $isEnable;
	public $createTime;
	public $expires;
	
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
	
	public function primaryKey() {
		return 'id';
	}
	
	public function tableName() {
		return "{{restful_account}}";
	}
	
	public function attributeLabels() {
		return [
			'server'     => '服务机构',
			'account'    => '帐号',
			'password'   => '密码',
			'isEnable'   => '是否启用',
			'createTime' => '创建时间',
			'expires'    => '帐号过期时间'
		];
	}

	/**
	 * 对比密码是否相等
	 * @param $password
	 * @return bool
	 */
	public function checkPassword($password) {
		return $this->password === md5($password);
	}

	/**
	 * 对token码进行检验
	 * @param string $token 进行比对的token码
	 * @param string $rand 随机数
	 * @return bool
	 */
	public function checkToken($token, $rand) {
		$accountToken = $this->makeToken($this->account, $this->salt, $rand);
		return $accountToken === $token;
	}

	/**
	 * 生成开发者账号sign码
	 * @param string $account 开发者帐号
	 * @param string $salt 加密盐字符串
	 * @param string $rand 随机码
	 * @return string
	 */
	public function makeToken($account, $salt, $rand) {
		$accountToken = sha1($account.$salt.$rand);
		$token = substr($accountToken, 0, 20);
		return sprintf("%s_%s_%s", $token, $account, $rand);
	}

	/**
	 * 查找
	 * @param $account
	 * @return CActiveRecord|null
	 */
	public function findByAccount($account) {
		return $this->findByAttributes(array(
			'account'  => $account,
			'isEnable' => 1
		));
	}
}