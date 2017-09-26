<?php

/**
 * 账号查找工厂类
 */
class AccountFactory {

	/**
	 * 根据账号生成对象
	 * @param string $account 账号
	 */
	private static function createobj( $account ) {
		$validator = new CEmailValidator;
		$validator->allowEmpty=false;
		if ( $validator->validateValue( $account ) ){
			return new AccountEmail();
		} else if( preg_match( Regexp::$mobile,$account ) ){
			return new AccountPhone();
		}
	}

	/**
	* 查找账号信息
	* @param string $returntype 返回类型，当为all时，返回用户全部信息，否则返回用户ID
	*/
	public static function findAccount($account,$returntype=''){
		if( $_obj=self::createobj( $account ) ){
			$user=$_obj->findUser($account);
			if( $user && $returntype!='all' ) {
				return $user['memberId'];
			} else {
				return $user;
			}
		}
	}
}

/**
 * 邮箱账号
 */
class AccountEmail extends Account {

	/**
	* 查找账号信息
	* @param string $account 需查找的账号
	*/
    public function findUser( $account ){
		return tbMember::model()->find('email=:email', array(':email'=>$account));
	}
}

/**
 * 手机账号
 */
class AccountPhone extends Account {
	/**
	* 查找账号信息
	* @param string $account 需查找的账号
	*/
    public function findUser($account){
		return tbMember::model()->find('phone=:phone', array(':phone'=>$account));
	}
}


/**
* 查找账号抽象类
*/
 abstract class Account{
    //抽象方法不能包含函数体
    abstract public function findUser($account);//强烈要求子类必须实现该功能函数
}