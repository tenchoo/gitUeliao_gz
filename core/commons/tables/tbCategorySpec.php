<?php
/**
 * 行业分类规格关系表
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$categoryId			行业分类ID
 * @property integer	$specId 			规格ID
 *
 */
class tbCategorySpec extends CActiveRecord {

	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{category_spec}}";
	}

	public function rules() {
		return array(
			array('categoryId,specId','required'),
			array('categoryId,specId','numerical','integerOnly'=>true, 'min'=>1),
		);
	}

	/**
	* 新增行业分类时自动继承父类规格--只能新增行业分类时才能调用,不可重复操作。
	* @param integer $categoryId 新增生成的行业分类ID
	* @param integer $parentId 新分类的父ID
	*/
	public function toExtend( $categoryId,$parentId ){
		if( empty( $categoryId ) || empty( $parentId ) ){
			return false;
		}

		$connection = Yii::app()->db;
		$sql =" INSERT INTO {{category_spec}} (`categoryId`, `specId` ) select  $categoryId,`specId` from {{category_spec}}  where  `categoryId`= $parentId";
		$command=$connection->createCommand($sql);
		return $command->execute();
	}

	/**
	* 继承到所有子类
	* @param integer $categoryId 需要继承到子类的行业分行ID
	* @param array $specIds 指定继承哪几个，非必须，空时为继承所有的规格
	*/
	public function extendAllchildren( $categoryId,$specIds = array() ){
		if( empty( $categoryId ) ){
			return false;
		}

		if( !empty( $specIds ) && is_array( $specIds ) ){
			foreach ( $specIds as $val ){
				if( !is_numeric($val) || $val<1 ){
					return false;
				}

			}
			$extendIds = implode(',',$specIds);
			$wherespec = " and `specId` in ( $extendIds ) ";
		}

		//取得行业分类ID
		$children = tbCategory::model()->getAllLevelChildrens( $categoryId );
		if( empty( $children ) ){
			return true; //无子分类时，返回true;
		}
		$transaction = Yii::app()->db->beginTransaction();
		$i = 0;
		try {
			//先删除原来的再增加
			$criteria = new CDbCriteria;
			$criteria->compare('categoryId', $children);
			$this->deleteAll( $criteria );
			$connection = Yii::app()->db;
			foreach ( $children as $val ){
				$sql =" INSERT INTO {{category_spec}} (`categoryId`, `specId` ) select  $val,`specId` from {{category_spec}}  where `categoryId`= $categoryId $wherespec ";
				$command=$connection->createCommand($sql);
				$i +=$command->execute();
			}

			$transaction->commit();
			return $i;
		} catch (Exception $e) {
			$transaction->rollback(); //如果操作失败, 数据回滚
			return false;
		}

	}
}