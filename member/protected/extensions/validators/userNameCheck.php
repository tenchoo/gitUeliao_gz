<?php
/**
 * @author Carlos Yuan
 * @version $Id$
 * 验证用户名为字母下划线开头，只包含字母数字下划线。
 */
class userNameCheck extends CValidator
{
	protected function validateAttribute($object,$attribute){
		$account=$object->$attribute;

		//检查账号是否合法
		if( tbMember::checkAccountValid( $account ) ) {

			//查找账号是否存在
			if( AccountFactory::findAccount( $account ) ){
				$this->addError($object,$attribute,Yii::t('reg', 'The accout already exists'));
			} else {
				return $account;
			}
		} else {
			$this->addError($object,$attribute,Yii::t('reg', 'accout must be email or phone'));
		}
	}
}