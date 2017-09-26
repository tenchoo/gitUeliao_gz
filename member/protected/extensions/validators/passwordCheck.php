<?php
/**
 * @author Carlos Yuan 
 * @version $Id$
 * 验证密码是否规范：
 *
 */
class passwordCheck extends CValidator
{
	protected function validateAttribute($object,$attribute)
	{
		$value = $object->$attribute;
		if(strlen($value) > strlen(trim($value))){
			$message=Yii::t('reg','The password can not use Spaces before and after');
			$this->addError($object,$attribute,$message);
		}
		$pattern = '/([a-z]+[0-9]+)|([0-9]+[a-z]+)/i';
		if(preg_match($pattern,$value)){
			return $value;
		}else{
			$message=$this->message!==null?$this->message:'请输入6-16个字符,密码需字母和数字组合';
			$this->addError($object,$attribute,$message);
		}
	}

/**
 *客户端验证
 */
	public function clientValidateAttribute($object,$attribute)
	{
		$pattern = '/([a-z]+[0-9]+)|([0-9]+[a-z]+)/i';			
		$condition="!value.match({$pattern})";			
		return "	
			if(".$condition.") {
				messages.push(".CJSON::encode('请输入6-16个字符,密码需字母和数字组合').");
			}
		";
	}
}