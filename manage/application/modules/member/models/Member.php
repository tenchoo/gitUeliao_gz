<?php
/**
 * 客户管理
 * @author liang
 * @package CFormModel
 */
class Member extends CFormModel {

	public $phone,$password,$repassword,$username;

	/**
	 * 表单校验规则
	 *
	 * @see CModel::rules()
	 */
	public function rules() {
		return array(
			array('phone,username','required'),
			array('password,repassword','required','on'=>'insert'),
			array('phone', 'match', 'pattern'=>Regexp::$mobile, 'message'=>Yii::t('base','Mobile phone number format is not correct')),
			array('username','length','min'=>2,'max'=>10),
			array('password', 'length', 'min'=>6, 'max'=>16,'tooLong'=>Yii::t('base','Password length of 6-16, must contain data and letters')),
			array('repassword', 'compare', 'compareAttribute'=>'password','message'=>Yii::t('base','The two passwords not match')),
			array ('phone','unique','className' => 'tbMember','attributeName' => 'phone','on'=>'insert,edit'),
			array('username,password','safe'),

		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签
	*/
	public function attributeLabels() {
		return array(
			'phone' => '手机号码',
			'username' => '姓名',
			'password'=>'密码',
			'repassword'=>'重复密码',
		);
	}

	public function editSaleman( $data,$model ){
		$this->attributes = $data;
		if( $model->isNewRecord ){
			$this->scenario = 'insert';
		}else if( $this->phone != $model->phone  ){
			$this->scenario = 'edit';
		}

		if(!$this->validate()){
			return false;
		}

		//使用事务处理，以确保这组数据全部保存成功
		$transaction = Yii::app()->db->beginTransaction();
		try {
			if( $this->scenario == 'insert'){
				$model->nickName = $this->username;
				$model->state='Normal';
				$model->register=date('Y-m-d H:i:s');
				$model->ip=Yii::app()->request->userHostAddress;
				$model->code=$model->setrandomCode(); //此语句必须在密码加密之前
				$model->password=$model->passwordEncode($this->password);
				$model->groupId = 1; //业务员
				$model->phone = $this->phone;

				if( !$model->save() ){
					$this->addErrors( $model->getErrors() );
					return false;
				}

				$Detail=new tbProfileDetail();
				$Detail->memberId = $model->memberId;
				$Detail->tel = '';
				$Detail->brand = '';
				$Detail->corporate = '';
				$Detail->companyname = '';
				$Detail->mainproduct = '';
				$Detail->gm = '';
				$Detail->pdm = '';
				$Detail->designers = '';
				$Detail->cfo = '';
				$Detail->address = '';
				$Detail->stallsaddress = '';
				$Detail->save();
			}else {
				$model->phone = $this->phone;
			//	$model->nickName = $this->username;
				if( !$model->save() ) {
					$this->addErrors( $model->getErrors() );
					return false;
				}
			}

			if( isset($model->profile) && !empty( $model->profile )){
				if( $model->profile->username != $this->username ){
					$model->profile->username = $this->username;
					if( !$model->profile->save() ){
						$this->addErrors( $model->profile->getErrors() );
						return false;
					}
				}
			}else{
				$profile = new tbProfile();
				$profile->memberId = $model->memberId;
				$profile->username = $this->username;
				$profile->birthdate = '';
				$profile->qq = '';
				if( !$profile->save() ){
					$this->addErrors( $profile->getErrors() );
					return false;
				}
			}

			$transaction->commit();
			return true;
		} catch (Exception $e) {
			$transaction->rollback(); //如果操作失败, 数据回滚
			throw new CHttpException(503,$e);
			return false;
		}
	}

	/**
	 * 查找客户列表
	 * @param  array $condition 查找条件
	 * @param  string $order	排序
	 * @param  integer $pageSize 每页显示条数
	 */
	public static function search( $condition = array() ) {
		$pageSize = tbConfig::model()->get( 'page_size' );
		$criteria=new CDbCriteria;
		if(!empty($condition)){
			foreach ($condition as $key=>$val){
				if( $val == '' ){
					continue ;
				}
				$criteria->compare('t.'.$key,$val);
			}
		}

		$criteria -> select = 'memberId, phone, register, userId, groupId,level,nickName,state';
		$criteria->with = 'profile';
		$criteria->order = 't.register DESC'; //默认为时间倒序
		$model = new CActiveDataProvider('tbMember', array(
			'criteria'=>$criteria,
			'pagination'=>array('pageSize'=>$pageSize),
		));

		$data = $model->getData();
		$result['list'] = array();
		$result['pages'] = $model->getPagination();

		if( $data ){
			foreach ( $data as $key => $val ){
				$result['list'][$key] = $val->attributes;
				if( $val->profile ){
					$result['list'][$key]['username'] = $val->profile->username;
				}else{
					$result['list'][$key]['username'] = '';
				}
			}
		}
		return $result;
	}



	/**
	 * 所有客户
	 * @param  array $condition 查找条件
	 * @param  string $order	排序
	 * @param  integer $pageSize 每页显示条数
	 */
	public static function memberList( $keyword,$companyname = null ) {
		$keyword = trim( $keyword );
		$pageSize = tbConfig::model()->get( 'page_size' );
		$criteria = new CDbCriteria;
		$criteria -> select = 't.memberId, t.phone,t.state, t.register, t2.title as groupId,level,t3.username as userId,t4.companyname as nickName';
		$criteria->addCondition("t.groupId > 1");
		if(!empty($keyword)){
			$criteria->compare('t.phone',$keyword,true);
		}

		$criteria->join = 'left join {{group}} t2 on ( t.groupId = t2.groupId )
						   left join {{profile}} t3 on ( t.userId=t3.memberId)
				       left join {{profile_detail}} t4 on( t.memberId=t4.memberId)'; //连接表
		if( !empty( $companyname ) ){
			$criteria->addSearchCondition('t4.companyname', $companyname);
		}

		$criteria->order = 't.register DESC'; //默认为时间倒序
		$model = new CActiveDataProvider('tbMember', array(
			'criteria'=>$criteria,
			'pagination'=>array('pageSize'=>$pageSize),
		));

		$data = $model->getData();
		$result['list'] = array();
		$result['pages'] = $model->getPagination();

		if( $data ){
			foreach ( $data as $key => $val ){
				$result['list'][$key]['memberId'] = $val->memberId;
				$result['list'][$key]['phone'] = $val->phone;
				$result['list'][$key]['companyname'] = $val->nickName;
				$result['list'][$key]['register'] = $val->register;
				$result['list'][$key]['group'] = $val->groupId;
				$result['list'][$key]['level'] = $val->level;
				$result['list'][$key]['saleman'] = $val->userId;
				$result['list'][$key]['state'] = $val->state;
			}
		}
		return $result;
	}




	/**
	 * 待审核列表
	 * @param  array $condition 查找条件
	 * @param  string $order	排序
	 * @param  integer $pageSize 每页显示条数
	 */
	public static function checkList( $keyword ) {
		$pageSize = tbConfig::model()->get( 'page_size' );
		$criteria = new CDbCriteria;
		$criteria -> select = 't.memberId,t.isCheck,t.state, t.phone,t2.companyname as nickName,t3.username as userId';
		$criteria->addCondition("t.groupId > 1");
		if(!empty($keyword)){
			$criteria->compare('t.phone',$keyword,true);
		}
		$criteria->join = 'left join {{profile_detail}} t2 on( t.memberId=t2.memberId)
						   left join {{profile}} t3 on( t.userId=t3.memberId)'; //连接表

		$criteria->compare('t.isCheck',array('0','2'));
		$criteria->order = 't.register DESC'; //默认为时间倒序
		$model = new CActiveDataProvider('tbMember', array(
			'criteria'=>$criteria,
			'pagination'=>array('pageSize'=>$pageSize),
		));

		$data = $model->getData();
		$result['list'] = array();
		$result['pages'] = $model->getPagination();

		if( $data ){
			foreach ( $data as $key => $val ){
				$result['list'][$key]['memberId'] = $val->memberId;
				$result['list'][$key]['phone'] = $val->phone;
				$result['list'][$key]['companyname'] = $val->nickName;
				$result['list'][$key]['saleman'] = $val->userId;
				$result['list'][$key]['isCheck'] = $val->isCheck;
				$result['list'][$key]['state'] = $val->state;
			}
		}
		return $result;
	}



	/**
	* 冻结member
	* @param integer/array $memberId 要标识冻结的memberId
	*/
	public static function frozen( $memberId ){
		$count = tbMember::model()->updateByPk($memberId,array('state'=>'Disabled'));
		return $count;
	}

	/**
	* 解冻member
	* @param integer/array $memberId 要标识冻结的memberId
	*/
	public static function thaw( $memberId ){
		$count = tbMember::model()->updateByPk($memberId,array('state'=>'Normal'));
		return $count;
	}

	/**
	* 重置会员密码
	* @param integer/array $memberId 要标识删除的memberId
	*/
	public static function Resetpassword( $memberId ){
		$model = tbMember::model()->findByPk( $memberId );
		if( !$model ) return false;

		$randStr = str_shuffle( '0123456789ABCDEFGHIJKLMNPQRSTUVWXYZabcdefghijklmnpqrstuvwxyz' );
		$code    = substr($randStr,0,8);
		$model->password = $model->passwordEncode( $code  );
		if( $model->save() ){
			//发短信通知客户密码已修改。
			$c = new CDbCriteria;
			$c->compare('t.`key`',array('sms_resetPassword','sms_default'));
			$smsTem = tbConfig::model()->findAll( $c );
			foreach ( $smsTem as $item ){
				$bodys[$item->key] = $item->value;
			}

			$body = !empty($bodys['sms_resetPassword'])?$bodys['sms_resetPassword']:$bodys['sms_default'];
			$body = str_replace('{code}', $code, $body);

			//发短信通知客户密码已修改。
			$send = new SendCaptcha($body);
			$result = $send->send( $model->phone );
			return true;
		}
		return false;
	}


	/**
	 * 所有信用客户
	 * @param  array $condition 查找条件
	 * @param  string $order	排序
	 * @param  integer $pageSize 每页显示条数
	 */
	public static function creditMemberList( $keyword ) {
		$keyword = trim( $keyword );

		$pageSize = tbConfig::model()->get( 'page_size' );
		$criteria = new CDbCriteria;
		$criteria->select = 't.*';
		if(!empty($keyword)){
			$criteria->compare('m.phone',$keyword,true);
		}

		$criteria->join = 'inner join {{member}} m on ( t.memberId = m.memberId )';

		$criteria->order = 't.createTime desc';
		$model = new CActiveDataProvider('tbMemberCredit', array(
			'criteria'=>$criteria,
			'pagination'=>array('pageSize'=>$pageSize),
		));

		$result['list'] = array();
		$result['pages'] = $model->getPagination();

		$data = $model->getData();
		if( empty($data) ){
			return $result;
		}

		$list = array();
		foreach ( $data as $val ){
			$list[$val->memberId] = $val->attributes;
		}

		$c = new CDbCriteria;
		$c->compare( 't.memberId', array_keys( $list ) );
		$c->select = 't.memberId, t.state, t.phone, t3.username as userId,t4.companyname as nickName';
		$c->join = '  left join {{profile}} t3 on ( t.userId=t3.memberId)
				       left join {{profile_detail}} t4 on( t.memberId=t4.memberId)'; //连接表

		$memberInfo = tbMember::model()->findAll( $c );

		foreach ( $memberInfo as $val ){
			$list[$val->memberId]['phone'] = $val->phone;
			$list[$val->memberId]['companyname'] = $val->nickName;
			$list[$val->memberId]['saleman'] = $val->userId;

			$usedCredit =  tbMemberCreditDetail::usedCredit( $val->memberId,'' );
			$list[$val->memberId]['usedCredit'] = $usedCredit;
			$list[$val->memberId]['validCredit'] = bcsub ( $list[$val->memberId]['credit'], $usedCredit,2 );
			$list[$val->memberId]['memberState'] = $val->state;
		}

		$result['list'] = array_values( $list );

		return $result;
	}
}