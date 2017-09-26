<?php
/**
 * Created by PhpStorm.
 * User: yagas
 * Date: 2015/11/26
 * Time: 9:40
 */

class MemberHelper extends CFormModel {

	public $oldpassword,$repassword,$password;

    public function rules()
    {
        return array(
			array('oldpassword,repassword,password','required'),
			array('password','length', 'min'=>6, 'max'=>16,'tooLong'=>Yii::t('base','Password length of 6-16, must contain data and letters')),
			array('repassword','compare','compareAttribute'=>'password','message'=>Yii::t('base','The two passwords not match')),
			array('oldpassword,repassword,password','safe'),
			);
    }


	public function attributeLabels() {
		return array(
			'oldpassword' => '原密码',
			'password' => '新密码',
			'repassword' =>'确认密码',
		);
	}

    /**
     * 获取用户名称
     * @param integer $memberId 会员ID
     * @return string
     */
    public static function nickname( $memberId ) {
        $member = tbMember::model()->with('profile')->findByPk( $memberId );
        if( is_null($member) ) {
            return '-';
        }

        if( !is_null($member->profile) && !empty($member->profile->username) ) {
            return $member->profile->username;
        }
        else {
            return $member->nickName;
        }
    }

	public function changePassword(){
		if( !$this->validate() ) {
			return false ;
		}

		if( $this->password == $this->oldpassword ){
			$this->addError('oldpassword',Yii::t('base','The new password cannot be the same as the old one'));
			return false;
		}

		$userId = Yii::app()->user->id;
		$model = tbUser::model()->findByPk( $userId );

		//判断旧密码是否正确
		$pwd = new ZPassword( ZPassword::AdminPassword );
		if( !$pwd->checkPassword( $model->password, $this->oldpassword) ) {
			$this->addError('oldpassword',Yii::t('base','the oldpassword is not match'));
			return false;
		}

		//保存新密码
		$model->isUpdatePassword();
		$model->password = $this->password;
		if( $model->save() ){
			return true;
		}else{
			$this->addErrors( $model->getErrors() );
		}
	}
}