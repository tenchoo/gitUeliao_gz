<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Regexp
 *
 * @author Administrator
 */
class Regexp {
	
	const REALNAME = '/^[\x{4e00}-\x{9fa5}A-Za-z0-9_.·\s ]+$/u';
	
	const QQ = "/^[1-9]*[1-9][0-9]*$/";
	
	const MOBILE = '/^1[34578][0-9]{9}$/';
	//put your code here
	/**
     * 验证真实姓名
     */
    public static $realname = '/^[\x{4e00}-\x{9fa5}A-Za-z0-9_.·\s ]+$/u';
    /**
     * 浮点数
     */
    public static $decmal = "/^([+-]?)\\d*\\.\\d+$/";
    /**
     * 正浮点数
     */
    public static $decmal1 = "/^[1-9]\\d*.\\d*|0.\\d*[1-9]\\d*$/";
    /**
     * 负浮点数
     */
    public static $decmal2 = "/^-([1-9]\\d*.\\d*|0.\\d*[1-9]\\d*)$/";
    /**
     * 浮点数
     */
    public static $decmal3 = "/^-?([1-9]\\d*.\\d*|0.\\d*[1-9]\\d*|0?.0+|0)$/";
    /**
     * 非负浮点数（正浮点数 + 0）
     */
    public static $decmal4 = "/^[1-9]\\d*.\\d*|0.\\d*[1-9]\\d*|0?.0+|0$";
    /**
     * 非正浮点数（负浮点数 + 0）
     */
    public static $decmal5 = "/^(-([1-9]\\d*.\\d*|0.\\d*[1-9]\\d*))|0?.0+|0$/";
    /**
     * 整数
     */
    public static $intege = "/^-?[1-9]\\d*$/";
    /**
     * 正整数
     */
    public static $intege1 = "/^[1-9]\\d*$/";
    /*
     * 负整数
     */
    public static $intege2 = "/^-[1-9]\\d*$/";
    /**
     * 数字
     */
    public static $num = "/^([+-]?)\\d*\\.?\\d+$/";
    /**
     * 正数（正整数 + 0）
     */
    public static $num1 = "/^[1-9]\\d*|0$/";
    /**
     * 负数（负整数 + 0）
     */
    public static $num2 = "/^-[1-9]\\d*|0$/";
    /**
     * 仅ACSII字符
     */
    public static $ascii = "/^[\\x00-\\xFF]+$/";
    /**
     * 仅中文
     */
    public static $chinese = "/^[\\u4e00-\\u9fa5]+$/";
    /**
     * 颜色
     */
    public static $color = "/^[a-fA-F0-9]{6}$/";
    /**
     * 日期
     */
    public static $date = "/^\\d{4}(\\-|\\/|\.)\\d{1,2}\\1\\d{1,2}$/";
    /**
     * 邮件
     */
    public static $email = "/^\\w+((-\\w+)|(\\.\\w+))*\\@[A-Za-z0-9]+((\\.|-)[A-Za-z0-9]+)*\\.[A-Za-z0-9]+$/";
    /**
     * 身份证
     */
    public static $idcard = "/(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/";
    /**
     * ip地址
     */
    public static $ip4 = "/^(25[0-5]|2[0-4]\\d|[0-1]\\d{2}|[1-9]?\\d)\\.(25[0-5]|2[0-4]\\d|[0-1]\\d{2}|[1-9]?\\d)\\.(25[0-5]|2[0-4]\\d|[0-1]\\d{2}|[1-9]?\\d)\\.(25[0-5]|2[0-4]\\d|[0-1]\\d{2}|[1-9]?\\d)$/";
    /**
     * 字母
     */
    public static $letter = "/^[A-Za-z]+$/";
    /**
     * 小写字母
     */
    public static $letter_l = "/^[a-z]+$/";
    /**
     * 大写字母
     */
    public static $letter_u = "/^[A-Z]+$/";
    /**
     * 手机
     */
    public static $mobile = '/^1[34578][0-9]{9}$/';
    /**
     * 电话号
     */
    public static $tel = "/(^(86)\-(0\d{2,3})\-(\d{7,8})\-(\d{1,4})$)|(^0(\d{2,3})\-(\d{7,8})$)|(^0(\d{2,3})\-(\d{7,8})\-(\d{1,4})$)|(^(86)\-(\d{3,4})\-(\d{7,8})$)/";
    /**
     * 非空
     */
    public static $notempty = "/^\\S+$/";
    /**
     * 密码
     */
    public static $password = "/^[A-Za-z0-9_-]+$/";
    /**
     * 图片
     */
    public static $picture = "(.*)\\.(jpg|bmp|gif|ico|pcx|jpeg|tif|png|raw|tga)$/";
    /*
     * QQ号码
     */
    public static $qq = "/^[1-9]*[1-9][0-9]*$/";
    /**
     * 压缩文件
     */
    public static $rar = "(.*)\\.(rar|zip|7zip|tgz)$/";
    /**
     * url
     */
    public static $url = "^http[s]? = \\/\\/([\\w-]+\\.)+[\\w-]+([\\w-./?%&=]*)?$/";
    /**
     * 用户名
     */
    public static $username = "/^[A-Za-z0-9_\\-\\u4e00-\\u9fa5]+$/";
    /**
     * 邮编
     */
    public static $zipcode = "/^\\d{6}$/";


	/**
	* 检查账号是否合法,注册账号只允许email 和手机号
	*/
	public function checkAccountValid( $account ) {
		$validator = new CEmailValidator;
		$validator->allowEmpty=false;
		if ( $validator->validateValue( $account ) ) {
			return true;
		} else if( preg_match( self::$mobile,$account ) ){
			return true;
		}
		return false;
	}

}

?>
