<?php
/**
 * 浮点格式验证器
 * @author yagas
 *
 */
class CFloatValidator extends CValidator {
	
	public $point;
	
	private $pattern = "/^\d+(\.\d%s)?$/";
	
	public $mix;
	
	public $max;
	
	public function validateAttribute($object, $attribute) {
		$value=$object->$attribute;
		
		if(!is_float(floatval($value))) {
			$message=$this->message!==null?$this->message:Yii::t('yii','{attribute} must be a float.');
			$this->addError($object,$attribute,$message);
			return false;
		}
		
		if($this->point) {
			$pattern = sprintf($this->pattern, '{1,'.$this->point.'}');
		}
		else {
			$pattern = sprintf($this->pattern, '+');
		}
		

		if(!preg_match($pattern, $value)) {
			$message=$this->message!==null?$this->message:Yii::t('yii','{attribute} point not match.');
			$this->addError($object,$attribute,$message);
			return false;
		}
		
		
		if($this->mix && floatval($value)<$this->mix) {
			$message=$this->message!==null?$this->message:Yii::t('yii','{attribute} mix than {mix}',array('{mix}'=>$this->mix));
			$this->addError($object,$attribute,$message);
			return false;
		}
		
		if($this->max && floatval($value)>$this->max) {
			$message=$this->message!==null?$this->message:Yii::t('yii','{attribute} max than {mix}',array('{mix}'=>$this->mix));
			$this->addError($object,$attribute,$message);
			return false;
		}
		
		return true;
	}
}