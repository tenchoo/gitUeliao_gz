<?php

/**
 * 产品特殊工艺配置表
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$craftId
 * @property integer	$hasLevel			是否分等级
 * @property string		$craftCode			工艺代表编号
 * @property string		$parentCode			上级编号
 * @property string		$title				工艺名称
 *
 */

class tbCraft extends CActiveRecord {

	public function init() {
		$this->parentCode = '';
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
		return '{{craft}}';
	}

	/**
	 * @return array 模型验证规则.
	 */
	public function rules()
	{
		return array(
			array('title,craftCode,hasLevel','required'),
			array('craftCode,parentCode','length','max'=>'3'),
			array('title','length','max'=>'10','min'=>'2'),
			array('hasLevel', 'in','range'=>array(0,1)),
			array('parentCode,craftCode,title', 'safe'),
			array('craftCode', 'unique'),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'title' => '工艺名称',
			'hasLevel' => '是否分等级',
			'parentCode' => '上级编号',
			'craftCode' => '工艺编号',
		);
	}

	/**
	* 删除工艺,有子分类不允许删除
	* @param integer $id 工艺ID
	* @return boolean
	*/
	public function del( $id ){
		if( !is_numeric( $id ) || $id< 1 ) return false;

		$model = $this->findByPk( $id );
		if ( !$model ) return false;

		if( empty( $model->parentCode ) ){
			$hasLevel = $model->exists('parentCode = :p',array(':p'=>$model->craftCode));
			if( $hasLevel ) return false;
		}

		return $model->delete();
	}

	/**
	* 取得所有工艺
	*/
	public function getAllCraft(){
		$models = $this->findAll( array(
						'order'=>'parentCode ASC,craftCode ASC'
					));
		$list = array();
		foreach ( $models as $val ){
			if ( !empty( $val->parentCode ) && isset( $list[$val->parentCode] ) ) {
				$list[$val->parentCode]['childs'][] = $val->attributes;
			} else {
				$list[$val->craftCode] = $val->attributes;
			}
		}
		return $list;
	}
	
	/**
	* 取得指定code工艺信息
	* @param array $codes
	*/
	public function getCrafts( $codes ){
		$crafts = tbCraft::model()->findAllByAttributes(array('craftCode'=>$codes ));
		$result = $parent = array();
		foreach ( $crafts as $val ){
				$result[$val->craftCode] = $val->getAttributes( array('craftCode','title','icon','parentCode') );
				if ( !empty( $val->parentCode ) ){
					 $parent[] =  $val->parentCode;
				}
			}
		if( !empty( $parent ) ){
			$parents = $this->getCrafts( $parent );
			foreach ( $result as &$val ){
				if( isset( $parents[$val['parentCode']] )){
					$val['title'] = $parents[$val['parentCode']]['title'].$val['title'];
				}
			}
		}
		return $result;
	}
}
?>