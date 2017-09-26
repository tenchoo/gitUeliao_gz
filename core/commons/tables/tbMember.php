<?php
/**
 * 客户数据库表
 *
 * The followings are the available columns in table '{{member}}':
 * @property integer $memberId
 * @property integer $groupId		客户组：1业务员,2客户
 * @property integer $userId		所属业务员ID
 * @property string  $state			enum('Normal','Disabled','Deleted')
 * @property integer $level			会员等级
 * @property integer $priceType		价格类型：0散剪价，1大货价
 * @property integer $isCheck		审核状态：0未审，1审核通过，2审核不通过
 * @property integer $isMonthlyPay	 是否月结
 * @property string $password		密码
 * @property string $code			盐值
 * @property string $ip				注册时的IP
 * @property string $phone			手机号码
 * @property string $nickName		呢称
 * @property string $email
 * @property string $paypassword	支付密码
 * @property date $register  0000-00-00 00:00:00 注册时间
 * @property string $payModel		支付方式,原方案是可订制客户的支付方式种类，以系列化方式存储，前台调用配置了的方式。未使用上，暂时保留当时方案字段结构。
 * @property string $monthlyType	月结方式，如：30天月结、60天月结
 */

class tbMember extends CActiveRecord
{

	public $paypsword;

	//表单验证串，防止非法提交
	public $formHash;

	//用户类型：客户和业务员
	const UTYPE_SALEMAN = 'saleman';
	const UTYPE_MEMBER = 'member';



	/**
	 * 返回基于自身的AR实例
	 * @param string $className 类名
	 * @return Member 实例
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string 返回表名
	 */
	public function tableName()
	{
		return '{{member}}';
	}

	/**
	 * @return array 模型验证规则.
	 */
	public function rules()
	{
		return array(
			array('password','required'),
			array('nickName', 'required','on'=>'edit'),
			// array('password', 'length', 'min'=>6, 'max'=>16,'tooLong'=>Yii::t('restful','Password length of 6-16, must contain data and letters')),
			array('phone', 'match', 'pattern'=>Regexp::MOBILE, 'message'=>Yii::t('reg','Mobile phone number format is not correct')),
			array('email', 'email'),
			array('nickName,phone,register,ip,state', 'safe'),
			array('phone','unique', 'message'=>Yii::t('reg', 'account has exists'))
		);
	}

	/**
	 * 关联查询配置
	 */
	public function relations() {
		return array(
			'profile'=>array(self::HAS_ONE,'tbProfile','memberId'),
			'profiledetail'=>array(self::HAS_ONE,'tbProfileDetail','memberId'),
			'levelinfo'=>array(self::HAS_ONE,'tbLevel','','on'=>'level=levelId')
		);
	}

	/*
	*@密码验证
	*@param $password 密码串
	*@param $type 加密类型 1.登录密码 2.支付密码
	*/
	public function checkPassword( $password , $type=1 ){
		if( $type == 1 ){
			return md5( md5( $password ).$this->code ) === $this->password;
		}else{
			return md5( md5( $password.$this->code ).$this->code ) === $this->paypsword;
		}
	}

	/*
	*@密码加密
	*@param $password 密码串
	*@param $type 加密类型 1.登录密码 2.支付密码
	*/
	public function passwordEncode( $password , $type=1 ){
		if( $type == 1 ){
			return md5( md5( $password ).$this->code );
		}else{
			return md5( md5( $password.$this->code ).$this->code );
		}
	}

	/**
	*@ 设置密码匹配的盐值
	*/
	public static function setrandomCode(){
		$randStr = str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890');
		return substr($randStr,0,6);
	}

	/**
	 * 默认匿称生成算法
	 * @param string $str
	 * @return string
	 */
	public static function half_replace($str){
		$encoding = 'UTF8';
		$len = mb_strlen($str,$encoding );
		$sstr = mb_substr( $str, 0, ceil($len/4), $encoding ).str_repeat('*',floor($len/2));
		$sstr .= mb_substr( $str, ceil($len/4)+floor($len/2), $len, $encoding );
		return $sstr;
	}

	/**
	* 检查账号是否合法,注册账号只允许手机号  ---email 和---
	*/
	public static function checkAccountValid( $account ) {
		if( preg_match( Regexp::MOBILE,$account ) ){
			return true;
		}

		return false;
		/* if ( empty ( $account ) ) {
			return false ;
		}

		$validator = new CEmailValidator;
		$validator->allowEmpty=false;
		if ( $validator->validateValue( $account ) ) {
			return true;
		} else if( preg_match( Regexp::$mobile,$account ) ){
			return true;
		}
		return false; */
	}

	/**
	* 取得客户的价格类型
	* @param integer $memberId
	*/
	public function getPriceType( $memberId ){
		$model = $this->findByPk($memberId);
		if( $model && $model->priceType == '1'){
			return 1;
		}
		return 0;
	}

	/**
	* 取得客户是否月结
	* @param integer $memberId
	*/
	public function getMonthlyPay( $memberId ){
		$model = $this->findByPk($memberId);
		if( $model && $model->isMonthlyPay ) return true;
		return false;
	}

	/**
	* 取得客户支付方式
	* @param integer $memberId
	*/
	public function getPayMentType( $memberId ){
		$model = $this->findByPk($memberId);
		if( $model ){
			$result['payModel'] = unserialize( $model->payModel );
			$result['monthlyType'] =  $model->monthlyType ;
			return $result;
		}
	}

	/**
	* 根据手机号查找用户,模糊匹配查找,联想搜索
	* @param string $keyword
	*/
	public function search( $keyword ,$limit = '10' ){
		if( empty( $keyword ) || !is_numeric( $keyword ) ){
			return;
		}
		$criteria=new CDbCriteria;
		$criteria->compare('state','Normal');

		$criteria->addNotInCondition('groupId', array(1));//只查找客户

		$userType = Yii::app()->user->getState('usertype');
		if( $userType == 'saleman'){
			//业务员只能查找自己服务的客户
			$userId[] = Yii::app()->user->id ;
			if( tbConfig::model()->get( 'default_saleman_id' ) == $userId['0'] ){
				$userId[] = 0;
			}

			$criteria->compare('userId',$userId);
		}

		$criteria->addSearchCondition('phone', $keyword);
		$criteria->limit = $limit;
		$model = $this->findAll( $criteria );

		$result = array();
		foreach( $model as $val ){
			$result[$val->memberId]['id'] = $val->memberId;
			$result[$val->memberId]['title'] = $val->phone;
		}

		return $result ;
	}

	/**
	* 查找自己所服务的全部客户
	* @param integer $userId 业务员ID
	* @param string $keyword 匹配企业简称
	*/
	public function searchServes( $userId,$keyword = null ){
		if( empty( $userId ) || !is_numeric( $userId ) ){
			return;
		}

		$t = $this->tableName();
		$cTable = tbProfileDetail::model()->tableName();
		$sql = " select t.memberId,t.nickName,c.shortname from $t t, $cTable c where c.memberId = t.memberId and t.state='Normal' and t.groupId!=1 and t.userId = $userId ";

		if( $keyword ){
			$sql .= ' and c.shortname like "%'.$keyword.'%"' ;
		}
		
		$cmd = $this->getDbConnection()->createCommand( $sql );
		$data = $cmd->queryAll();
		
		$result = array();
		if( is_array($data) ){
			foreach ( $data as $val ){
				$companyname = ($val['shortname'])?$val['shortname']:$val['nickName'];
				$result[$val['memberId']] = array('memberId'=>$val['memberId'],'companyname'=>$companyname );
			}
		}
		return $result;
	}

	/**
	* 取得业务员列表
	*
	*/
	public function getSaleman(){
		$criteria=new CDbCriteria;
		$criteria->compare('state','Normal');
		$criteria->compare('groupId','1');
		$criteria->select = 'memberId';
		$criteria->with = 'profile';
		$model = $this->findAll( $criteria );
		$result = array();
		foreach( $model as $val ){
			if($val->profile){
				$result[$val->memberId] =$val->profile->username;
			}
		}
		return $result ;
	}

	/**
	* 判断客户当前是否是某业务员服务
	*
	*/
	public static function checkServe( $memberId,$userId ){
		if( empty($memberId) || empty ( $userId ) ) return false;

		//如果是系统业务员
		if( tbConfig::model()->get( 'default_saleman_id' ) == $userId ){
			$userId = array(0,$userId);
		}else{
			$userId = $userId;
		}

		$c = new CDbCriteria;
		$c->compare('memberId',$memberId);
		$c->compare('userId',$userId);
		//$c->compare('groupId','2');
		return tbMember::model()->exists( $c );
	}

	/**
	 * 通过手机号码获取会员信息
	 * @param $phone
	 * @return static
	 */
	public function findByPhone($phone) {
		return $this->find("phone=:phone", [':phone'=>$phone]);
	}
}
