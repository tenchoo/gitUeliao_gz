<?php
/**
 * 发货地址
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$addressId
 * @property integer	$isDefaultSend		是否默认发货地址
 * @property integer	$isDefaultReturn	是否默认退货地址
 * @property integer	$areaId				区域ID
 * @property string		$phoneNumber		手机号码
 * @property string		$contactPerson		联系人
 * @property string		$zipCode			邮政编码
 * @property string		$fixedTelephone		固定电话
 * @property string		$companyName		公司名称
 * @property string		$address			详细地址
 *
 */

 class tbAddress extends CActiveRecord {

	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{address}}";
	}

	public function rules() {
		return array(
			array('areaId,contactPerson,address','required'),
			array('isDefaultSend,isDefaultReturn','in','range'=>array(0,1)),
			array('areaId', "numerical","integerOnly"=>true),
			array('contactPerson,address,companyName,phoneNumber,zipCode,fixedTelephone','safe'),

		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'isDefaultSend' => '是否默认发货地址',
			'isDefaultReturn' => '是否默认退货地址',
			'areaId' => '区域ID',
			'phoneNumber' => '手机号码',
			'contactPerson' => '联系人',
			'zipCode' => '邮政编码',
			'fixedTelephone' => '固定电话',
			'companyName' => '公司名称',
			'address' => '详细地址',

		);
	}

	/**
	* 取得全部地址列表
	*/
	public function getAll(){
		return	$this->findAll();
	}
}