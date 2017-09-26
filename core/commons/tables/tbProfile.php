<?php

/**
 * 会员个性化信息表
 * @package CActiveRecord
 *
 * @property integer $memberId
 * @property integer $sex
 * @property string $username
 * @property string $icon
 * @property string $qq
 * @property timestamp $birthdate
 */
class tbProfile extends CActiveRecord {

	public function init() {
		$this->username = '';
		$this->icon = '';
		$this->qq = '';
		$this->birthdate = '0000-00-00';
	}

	/**
	 * 构造数据库表模型单例方法
	 * @param system $className
	 * @return static
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{profile}}';
	}

	/**
	 * @return array 模型验证规则.
	 */
	public function rules()
	{
		return array(
			array('username', 'match', 'pattern'=>Regexp::REALNAME,'message'=>Yii::t('user','realname is not valid')),
			array('qq', 'match', 'pattern'=>Regexp::QQ, 'message'=>Yii::t('user','QQ number is not valid')),
			array('sex', 'in','range'=>array(0,1)),
			array('username,icon', 'safe'),
			array('username','length','max'=>'15'),
			array('icon','length','max'=>'100'),
		);
	}

	
/* 	public function getUsername( $memberId ){
		$model = $this->findByPk( $memberId );
		$result = array();
		if( !empty($model )){
			if(is_array( $model )){
				foreach ( $model as $val ){
					$result[$val->memberId] =$val->username;
				}
			}else{
				$result[$model->memberId] =$model->username;
			}
		}
		return $result;
	} */
	
	/**
	* 取得真实姓名
	* @param integer/array $memberId
	*/
	public function getMemberUserName( $memberId ) {
		$userProfile = $this->findByPk( $memberId );
		if( $userProfile ) {
			return $userProfile->username;
		}
		return '';
	}

	 /**
	 * 保存前的操作
	 */
	protected function beforeSave(){
		if( $this->birthdate == '' ){
			$this->birthdate = '0000-00-00';
		}
		return true;
	}
}
?>