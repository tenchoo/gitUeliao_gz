<?php
/**
 * 送货员基本信息
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$deliverymanId
 * @property string		$title
 *
 */

 class tbDeliveryman extends CActiveRecord {

	const SALT = 'goujiazi';


	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{delivery_man}}";
	}

	public function rules() {
		return array(
			array('title','required'),
			array('title', 'length', 'max'=>10, 'min'=>1),
			array('title','safe'),
			array('title','unique'),

		);
	}

	public function idMd5( $deliverymanId ){
		return md5( $deliverymanId.self::SALT );
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'title' => '送货员姓名',
		);
	}

	/**
	* 取得全部送货员
	*/
	public function getDeliverymans(){
		$model = $this->findAll( array( 'order'=>'CONVERT(`title` USING gbk) COLLATE gbk_chinese_ci' ) );
		$result = array('0'=>'待定','-1'=>'已分配');
		foreach ( $model  as $val ){
			$result[$val->deliverymanId] = $val->title;
		}
		return $result;
	}

	/**
	* 根据ID取得某个送货员名称
	* @param integer $deliverymanId
	* @override
	*/
	public function getdeliverymanName( $deliverymanId ){
		$model = $this->findByPk( $deliverymanId );
		if( $model ){
			return $model->title;
		}
		return null;
	}
}