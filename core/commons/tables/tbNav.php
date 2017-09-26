<?php
/**
 * 菜单数据表模型
 * @author yagas
 * @package CActiveRecord
 * @version 0.1
 *
 */
class tbNav extends CActiveRecord {
	
	public static function model($className=__CLASS__) {
		return parent::model( $className );
	}
	
	public function tableName() {
		return '{{nav}}';
	}
	
	public function primaryKey() {
		return "navId";
	}
	
	/**
	 * 数据校验规则
	 * @see CModel::rules()
	 */
	public function rules() {
		return array(
			array('parentId,title', 'required', 'message'=>Yii::t('base','fill {attribute} value,pliease.')),
		);
	}
	
	/**
	 * 查找菜单组编号
	 * @param integer $menuId
	 * @return integer
	 */
	public function findRoot( $menuId ) {
		$menu = $this->findByPk( $menuId );
		if( $menu ) {
			if( $menu->parentId == 0 ) {
				return $menu->menuId;
			}
			return $this->findRoot( $menu->parentId );
		}
	}
}