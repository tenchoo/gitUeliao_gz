<?php
/**
 * 供应商，厂家信息
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$supplierId				供应商，厂家ID
 * @property integer	$state					状态：0正常，删除
 * @property string		$factoryNumber			工厂编号
 * @property string		$shortname				工厂名称
 * @property string		$contact				联系人
 * @property string		$phone					联系电话
 * @property string		$adddress				联系地址
 *
 */

 class tbSupplier extends CActiveRecord {

	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{supplier}}";
	}

	public function rules() {
		return array(
			array('shortname,factoryNumber,contact,phone','required'),
			array('shortname,factoryNumber,contact,phone,adddress','safe'),
            array('factoryNumber','numerical', "integerOnly"=>true),
            array('shortname', 'length', 'max'=>50),
            array('factoryNumber', 'length', 'max'=>15),
			array('factoryNumber','unique'),
			array('shortname','unique','criteria'=>array('condition'=>'state = 0')),

		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'supplierId' => '供应商，厂家ID',
			'shortname' => '工厂名称',
			'factoryNumber'=>'工厂编号',
			'contact'=>'联系人',
			'phone'=>'联系电话',
			'adddress'=>'联系地址',

		);
	}

	/**
	* 根据厂家名称查找用户,模糊匹配查找,联想搜索
	* @param string $keyword
	*/
	public function searchbyName( $keyword ){
		if( empty( $keyword ) ){
			return;
		}
		$criteria=new CDbCriteria;
		$criteria->compare('state','0');
		$criteria->addSearchCondition('shortname', $keyword);
		$criteria->limit = 10;
		$model = $this->findAll( $criteria );

		$result = array();
		foreach( $model as $val ){
			$result[$val->supplierId]['id'] = $val->supplierId;
			$result[$val->supplierId]['title'] = $val->shortname;
		}

		return $result ;
	}

}
