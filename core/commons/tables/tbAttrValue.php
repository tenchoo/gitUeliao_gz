<?php
/**
 * 属性值表
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 * @property integer $id	属性值ID
 * @property string  $valueName			属性值
 *
 */


class tbAttrValue extends CActiveRecord {
	/**
	 * 获得模型对象实例
	 * @param unknown_type $className
	 * @return Ambigous <static, unknown, multitype:>
	 */
	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	/**
	 * 获得数据表名
	 * @return string
	 */
	public function tableName() {
		return "{{attr_value}}";
	}

	public function rules() {
		return array(
				array('valueName','required'),
				array('valueName', 'length', 'max'=>10, 'min'=>1),
				array('valueName', 'safe'),
				array('valueName', 'unique'),
		);
	}

	public function attributeLabels() {
		return array(
			'valueName'=>'属性值',
		);
	}

	/**
	* 根据ID取得属性标题
	* @param integer $attributeValueId
	*
	*/
	public function getValueById( $ids ){
		$result = array();

		if( !is_array( $ids ) ){
			$ids = explode( ',',$ids );
		}

		if( !empty( $ids ) ){
			$model = $this->findAllByPk( $ids );

			if( $model ){
				$values = array();
				foreach ( $model as $val ){
					$values[$val->id] = $val->valueName;
				}

				foreach( $ids as $val ){
					if( array_key_exists( $val,$values ) ){
						$result[$val] = $values[$val];
					}
				}
			}
		}

		return $result ;
	}


	public function setIds( $attrValue ){
		if( empty ( $attrValue ) ) return ;

		$attrValue = str_replace( '，',',',$attrValue );//半角
		$attrValue = str_replace( '，',',',$attrValue );//全角

		$attrValue = explode( ',',$attrValue );

		$ids = array();
		foreach ( $attrValue as $val ){
			$val = trim( $val );
			if( !empty( $val ) && $id = $this->getId( $val,true )  ){
				$ids[] = $id;
			}
		}

		return implode( ',', array_unique( $ids ) );
	}

	/**
	* 根据属性值取得ID
	* @param integer $valueName  属性值
	* @param boolean $add 	若此值不存在时，是否直接新增生成ID
	*/
	public function getId( $valueName,$add = false ){
		if( empty( $valueName ) ){
			return null;
		}

		$model = $this->findByAttributes( array('valueName'=>$valueName) );
		if( $model ){
			return $model->id;
		}

		if( $add === true ){
			$model = new self;
			$model->valueName = $valueName;
			if( $model->save() ){
				return $model->id;
			}
		}
	}

	public function getValue($id) {
		$result = $this->findByPk($id);
		if($result) {
			return $result->valueName;
		}
		return null;
	}
}