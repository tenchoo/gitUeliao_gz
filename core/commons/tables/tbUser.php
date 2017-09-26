<?php
/**
 * 用户帐号信息
 * @author yagasx
 * @version 0.2
 *
 * @property int    $userId   用户编号
 * @property int    $isAdmin  是否超管
 * @property string $account  登陆帐号
 * @property string $password 登陆密码
 * @property string $username 用户名
 * @property string $email    用户电子邮箱地址
 * @property integer $printerId 打印机ID
 */
class tbUser extends CActiveRecord {

	private $_update_password = false;
	protected $_password; //原始密码

	public function init() {
		parent::init();
		$this->isAdmin = 0;
		$this->email = '';

		$this->attachEventHandler('onAfterSave', ['tbUserrole','ESaveUserRoleInfo']);
	}

	public static function model($className=__CLASS__) {
		return parent::model( $className );
	}

	public function tableName() {
		return '{{user}}';
	}

	public function primaryKey() {
		return "userId";
	}

	public function relations() {
		return array(
			'position' => array(self::BELONGS_TO,'tbDepPosition','depPositionId','select'=>'positionName'),
			'roles' => array(self::MANY_MANY, 'tbRole', 'db_userRole(userId, roleId)','select'=>'roleName'),
			'warehouse' => array(self::HAS_ONE, 'tbUserPackinger', 'userId','select'=>'warehouseId'),
		);
	}

	public function rules() {
		return array(
			array('account,username', 'required'),
			array('account', 'length', 'max'=>20, 'min'=>2),
			array('username', 'length', 'max'=>12, 'min'=>2),
			array('account', 'length','on'=>'insert'),
			array('password', 'required','on'=>'insert'),
			array('gender','in','range'=>array(0,1)),
			array('printerId,departmentId,depPositionId', "numerical","integerOnly"=>true,'min'=>1),
			array('account', 'unique', ),
			array('email', 'email', 'message'=>Yii::t('base','Invalid email address')),
			array('account,password,username', 'safe'),
		);
	}

	public function attributeLabels() {
		return array(
			'username' => '员工姓名',
			'gender' => '性别',
			'departmentId'=>'所属部门',
			'depPositionId'=>'职位',
			'password' => '密码',
			'account'=>'手机/账号',
		);
	}

	/**
	 * 新添加的会员进行密码加密
	 * 密码非空的时候才进行加密更新，否则写入原始密码
	 */
	protected function beforeSave() {
		$password = $this->encodePwd($this->password);

		if( $this->isNewRecord || $this->_update_password ) {
			$this->password = $password;
		}else {
			$this->password = $this->_password;
		}
		return parent::beforeSave();
	}

	protected function afterFind() {
		$this->_password = $this->password;
		parent::afterFind();
	}

	public function encodePwd($password){
		$passwd = new ZPassword( ZPassword::AdminPassword );
		return $passwd->encode( $password );
	}

	/**
	 * 搜索用户名
	 * @param unknown $keyword
	 * @param string $role
	 */
	public function findByUsername( $keyword, $role=null, $limit=20 ) {
		$sql = "select userId,username from {{user}} left join {{userRole}} using(userId) where username like :username limit 0,20";
		if( !is_null($role) ) {
			$sql .= " and roleId=:roleId";
		}

		$cmd = $this->getDbConnection()->createCommand( $sql );
		$cmd->bindValue( ':username', $keyword.'%', PDO::PARAM_STR );

		if( !is_null($role) ) {
			$cmd->bindValue( ':roleId', $role, PDO::PARAM_INT );
		}
		return $cmd->queryAll();
	}

	/**
	 * 更新用户密码
	 */
	public function isUpdatePassword() {
		$this->_update_password = true;
	}

	/**
	* 根据 userId 取得业务员名称
	* @param integer $userId
	*/
	public function getUsername( $userId ){
		if( empty($userId ) ){
			return;
		}
		$user = $this->findByPk( $userId );
		if( $user ){
			return  $user->username;
		}
	}

	/**
	* 获取所有用户
	*
	*/
   public function getAll(  ){
		$result = array();
		$model = $this->findAll();
		foreach ($model as $key=>$val){
			$result[$val->userId] =  $val->username;
		}
		return $result;
   }

}
