<?php
/**
 * 系统设置信息
 * @author yagas
 * @package CActiveRecord
 * @version 0.1
 */
class tbConfig extends CActiveRecord {

	const MAX_PAGE_SIZE = 20; //每页显示条数允许最大数量

	public $configId;
	public $key;
	public $value;
	public $comment;


	/**
	 * @return array 模型验证规则.
	 */
	public function rules()
	{
		return array(
			array('value', 'in','on'=>'bool','range'=>array(0,1) ),
			array('value', 'numerical','on'=>'num','min'=>'0'),
			array('value', 'numerical','on'=>'int','integerOnly'=>true,'min'=>'1'),
			array('value','safe'),
			array('value','customRule'),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'value' => '值',
		);
	}

	/**
	* 定制字段值验证
	*/
	public function customRule($attribute,$params){
		if( $this->type != 'sms' && $this->valueType !='bool' ){
			if( empty( $this->$attribute ) ){
				$this->addError($attribute,'值不能为空');
				return false;
			}
		}
		switch ( $this->key ){
			case 'default_saleman_id': //设置默认业务员，检查业务员ID是否正常。
				 $falg = tbMember::model()->exists('memberId = :id and groupId = 1 and state = :state',array(':id'=>$this->$attribute,':state'=>'Normal'));
				 if(!$falg){
					$this->addError($attribute,Yii::t('base','No ID account or account number is not available for this ID'));
					return false;
				}
				break;
			case 'page_size':
			case 'search_tip_size':
				if($this->$attribute > self::MAX_PAGE_SIZE){
					$this->addError($attribute,Yii::t('base','Can not be more than {num}',array('{num}'=>self::MAX_PAGE_SIZE)));
					return false;
				}

		}



	}

	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{config}}";
	}

	public function primaryKey() {
		return "configId";
	}

	public function get( $key ) {
		$conf = $this->find( "`key`=:name", array(':name'=>$key) );
		return $conf? $conf->value : null;
	}

	public function set( $key, $value ) {
		$conf = $this->find( "`key`=:name", array(':name'=>$key) );
		if( !$conf ) {
			return false;
		}
		$conf->value = $value;
		$afterRows = $conf->save();
		return $afterRows>0;
	}

	public function add( $key, $value ) {
		$conf = $this->find( "`key`=:name", array(':name'=>$key) );
		if( $conf ) {
			return false;
		}
		$conf = new tbConfig();
		$conf->value = $value;
		$afterRows = $conf->save();
		return $afterRows>0;
	}

	public function sets( $configs ) {
		if( is_array($configs) && $configs ) {
			foreach( $configs as $key => $value ) {
				$this->set( $key, $value );
			}
			return !$this->hasErrors();
		}
		return false;
	}
}