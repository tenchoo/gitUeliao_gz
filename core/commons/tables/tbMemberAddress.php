<?php
/**
 * 商家发货地址
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$addressId
 * @property integer	$memberId			客户ID
 * @property integer	$isDefault			是否默认:0否1是
 * @property integer	$areaId				区域ID
 * @property string		$mobile				手机号码
 * @property string		$name				联系人
 * @property string		$zip				邮政编码
 * @property string		$tel				固定电话
 * @property string		$address			详细地址
 *
 */

 class tbMemberAddress extends CActiveRecord {

	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{member_address}}";
	}

	public function rules() {
		return array(
			array('memberId,areaId,name,mobile,address','required'),
			array('isDefault','in','range'=>array(0,1)),
			array('memberId,areaId', "numerical","integerOnly"=>true),
			array('name,address,tel,zip,mobile','safe'),
			array('name','length','max'=>'10'),
			array('address','length','max'=>'50'),
			array('zip','length','max'=>'10'),
			array('tel','length','max'=>'20'),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'memberId' => '客户ID',
			'isDefault' => '是否默认',
			'areaId' => '区域ID',
			'mobile' => '手机号码',
			'name' => '联系人',
			'zip' => '邮政编码',
			'tel' => '固定电话',
			'address' => '详细地址',

		);
	}

	/**
	* 取得某客户的全部收货地址
	* @param integer  $memberId  客户ID
	*/
	public function getAll( $memberId ){
		return  $this->findAll('memberId=:memberId', array( ':memberId' => $memberId ) );
	}

	/**
	* 取得客户默认收货地址,按isDefault倒序排序
	* @param integer  $memberId  客户ID
	*/
	public function getDefault( $memberId ){
		$model = $this->find( array(
		  'condition' => 'memberId = :memberId',
		  'params' => array( ':memberId'=>$memberId ),
		  'order' =>'isDefault desc',
		));
		return $model;
	}

	/**
	* 取一收货地址
	* @param integer  $memberId  客户ID
	*/
	public function getOne( $memberId ){
		$model = $this->find( array(
		  'condition' => 'memberId = :memberId',
		  'params' => array( ':memberId'=>$memberId ),
		  'order'=>'isDefault desc',
		));
		return $model;
	}

	/**
	* 取得的某一收货地址  编辑收货地址时用
	* @param integer  $addressId
	* @param integer  $memberId  客户ID
	*/
	public function findOne( $addressId,$memberId ){
		$model = $this->findbyPk( $addressId, 'memberId = :memberId',array( ':memberId'=>$memberId ) );
		return $model;
	}

	/**
	* 删除某一收货地址
	* @param integer  $addressId
	* @param integer  $memberId  客户ID
	*/
	public function del( $addressId,$memberId ){
		$result = $this->deleteByPk( $addressId, 'memberId = :memberId ',array( ':memberId'=>$memberId ) );
		return $result;
	}

	/**
	* 计算收货地址总数
	* @param integer  $memberId  客户ID
	*/
	public function getCount( $memberId ){
		$count = $this->count( 'memberId = :memberId ',array( ':memberId'=>$memberId ) );
		return $count;
	}



	/**
	* 保存前的操作
	*/
	protected function beforeSave()	{
		if( $this->isNewRecord && $this->isDefault == '0'  ) {
			if(!$this->getDefault( $this->memberId) ){
				$this->isDefault = 1;
			}
		}

		return true;
	}

	/**
	* 保存后的操作
	*/
	protected function afterSave()	{
		//默认地址只能有一个
		if( $this->isDefault == '1' ) {
			$this->updateAll(array('isDefault'=>'0'),'memberId =:memberId and addressId!=:addressId', array (':memberId'=>$this->memberId,':addressId'=>$this->addressId ));
		}
		return true;
	}

}