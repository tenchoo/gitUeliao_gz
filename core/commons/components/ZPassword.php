<?php
/**
 * 密码加密算法
 * 对不同类型密码采用不同的加密算法进行加密处理
 * @author yagas<yagas@sina.com>
 * @url http://blog.csdn.net/yagas
 * @version 0.1
 * @package CModel
 * @example:
 * $passwd = new ZPassword( ZPassword::UserPassword );
 * $passwd->encode( "123456" );
 * $passwd->ckechPassword( "xxxxxx", "123456" );
 */
class ZPassword extends CModel {
	/**
	 * 密码盐长度
	 * @var int
	 */
	private $_satlsLen = 5;

	/**
	 * 盐在密文中的偏移值
	 * @var int
	 */
	private $_offset = 10;

	/**
	 * 加密算法名称
	 * @var string
	 */
	private $_passwordType;

	/**
	 * 会员登陆密码
	 * @var string
	 */
	const UserPassword  = "sha224";

	/**
	 * 登陆员登陆密码
	 * @var string
	 */
	const AdminPassword = "snefru256";

	/**
	 * 支付密码
	 * @var string
	 */
	const PayPassword   = "haval128,3";

	public function __construct( $passwordType ) {
		$this->_passwordType = $passwordType;
	}

	public function attributeNames() {
		return array();
	}

	/**
	 * 加密字符串
	 * @param string $password 需要进行加密的字符串
	 * @param string $satls    加密盐
	 * @return string          密文
	 */
	public function encode( $password, $satls=null ) {
		if( is_null( $satls ) ) {
			$satls = '';
			while( strlen( $satls ) < $this->_satlsLen ) {
				$i      = mt_rand( 65, 90 );
				$satls .= chr( $i );
			}
		}

		$satls        = strtolower( $satls );
		$password     = hash( $this->_passwordType, $password.$satls );
		$password     = md5( $password );
		$newPassword  = substr( $password, 0, $this->_offset );
		$newPassword .= $satls . substr( $password, $this->_offset );
		return substr( $newPassword, 0, 32 );
	}

	/**
	 * 验证密码是否正确
	 * @param string $securtyString 密钥
	 * @param string $password      密码
	 * @return boolean
	 */
	public function checkPassword( $securtyString, $password ) {
		$satls    = substr( $securtyString, $this->_offset, $this->_satlsLen );
		$password = $this->encode( $password, $satls );
		return $securtyString == $password;
	}
}