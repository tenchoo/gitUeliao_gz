<?php
/**
 * 属性标题表
 * @author liang
 * @version 0.1
 * @package CActiveRecord

 * @property integer $attrId	属性标题ID
 * @property string  $title		属性标题
 *
 */

class tbAttr extends CActiveRecord {
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
		return "{{attr}}";
	}

	public function rules() {
		return array(
				array('title','required'),
				array('title', 'length', 'max'=>20, 'min'=>2),
				array('title', 'safe'),
				array('title', 'unique'),
		);
	}

	public function attributeLabels() {
		return array(
			'title'=>'属性标题',
		);
	}

	/**
	* 根据ID取得属性标题
	* @param integer $attrId
	*
	*/
	public function titleName( $attrId ){
		if( !empty( $attrId ) ){
			$model = $this->findByPk( $attrId );
			if( $model ){
				return $model->title;
			}
		}
		return '';
	}

	/**
	* 根据属性标题取得属性ID
	* @param integer $title  属性标题
	* @param boolean $add 	若此标题不存在时，是否直接新增生成ID
	*/
	public function getId( $title,$add = false ){
		if( !empty( $title ) ){
			$model = $this->findByAttributes( array('title'=>$title) );
			if( $model ){
				return $model->attrId;
			}else{
				if( $add === true ){
					$model = new self;
					$model->title = $title;
					if( $model->save() ){
						return $model->attrId;
					}
				}
			}
		}
	}
}