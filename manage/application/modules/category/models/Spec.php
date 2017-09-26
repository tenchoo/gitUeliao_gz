<?php
/**
 * 行业分类规格管理
 * @author liang
 * @version 0.1
 * @package models
 *
 */

class Spec {

	const SPEC_NUM = 2; //规格个数，最多只能添加几个规格。

	/*
	* 取得规格信息
	* @param integer $categoryId 行业分类ID
	* @param integer $withValues 是否取得规格的值
	*/
	public function getSpecinfo( $categoryId ='',$withValues='0' ){
		$criteria = new CDbCriteria;
		$criteria->compare('t.state', 0);
		if( $categoryId ){
			$criteria->compare('t2.categoryId', $categoryId);
			$criteria->join = 'inner join zd_category_spec t2 on( t.specId=t2.specId )'; //连接表
		}
		$model = tbSpec::model()->findAll( $criteria );
		$speclist = array();
		foreach( $model as $val ){
			$key = $val->specId;
			$speclist[$key] = $val->attributes;
			unset( $speclist[$key]['state'] );
		}

		if( !empty( $speclist ) && $withValues == '1' ){
			$criteria2 = new CDbCriteria;
			$criteria2->compare('t.specId', array_keys( $speclist ) );
			$valuemodel = tbSpecvalue::model()->findAll( $criteria2 );
			foreach( $valuemodel as $val ){
				$key  =  $val->specId;
				$key2 =  $val->specvalueId;
				$speclist[$key]['values'][$key2] =  $val->title;

			}
		}
		return $speclist;
	}


	/**
	* 取得当前设置规格
	* @param integer $categoryId 行业分类ID
	*/
	public function getCategorySpec( $categoryId ) {
		if( empty( $categoryId ) ){
			return ;
		}

		$model = tbCategorySpec::model()->findAll( 'categoryId =:categoryId',array( ':categoryId'=>$categoryId ) );
		$data=array();
		foreach ( $model as $val ){
			$data[] = $val->attributes;
		}
		return $data;
	}

	/**
	* 指定一规格配有规格图片
	* @param integer $specId
	*/
	public function Setspecpicture( $specId ){
		if( empty( $specId ) ){
			return false;
		}

		$model = tbSpec::model()->findByPk( $specId );
		if( empty( $model ) ){
			return false;
		}

		$transaction = Yii::app()->db->beginTransaction();
		try {
			tbSpec::model()->updateAll( array('isPicture'=>'0'),'isPicture = 1 and state = 0 ' );
			$model->isPicture = '1';
			$model->update();
			$transaction->commit();
			return true;
		} catch (Exception $e) {
			$transaction->rollback(); //如果操作失败, 数据回滚
			return false;
		}

	}


	/**
	* 删除行业规格
	* @param integer $categoryId
	* @param integer $specId
	*/
	public function delCategorySpec( $categoryId,$specId ){
		if( empty( $categoryId ) || empty( $specId )  ){
			return ;
		}
		$result = tbCategorySpec::model()->deleteAll( 'categoryId =:categoryId and specId =:specId',array( ':categoryId'=>$categoryId,':specId'=>$specId ) );
		return $result;
	}

	/**
	* 新增行业规格
	* @param integer $categoryId
	* @param integer $specId
	*/
	public function addCategorySpec( $categoryId,$specId ){
		if( empty( $categoryId ) || empty( $specId )  ){
			return false;
		}

		//setp1 先检查是否已经添加
		$model = new tbCategorySpec();
		$count = $model->count( 'categoryId=:categoryId',array(':categoryId'=>$categoryId));
		if( $count >= self::SPEC_NUM ){
			return array( 'specId'=>array('0'=>Yii::t('category','Specifications can only add up to {num}',array( '{num}'=> self::SPEC_NUM ) ) ) );
		}

		$exists = $model->exists( 'categoryId = :categoryId and specId = :specId',array(':categoryId'=>$categoryId,':specId'=>$specId) );
		if ( !$exists ){
			$model->specId = $specId;
			$model->categoryId = $categoryId;
			if( !$model->save() ){
				$error = $model->getErrors();
				return $error;
			}
		}
		return true;
	}

	/**
	* 保存规格值
	* @param array $specForm 保存提交的数据
	*/
	public function setValue( $specForm,$model='' ){
		if( empty( $specForm ) || !is_array( $specForm ) ){
			return ;
		}
		if ( empty( $model ) ){
			$model = new tbSpecvalue();
		}

		$model->attributes = $specForm;
		if( $model->save() ){
			return true;
		} else {
			$error = $model->getErrors();
			return $error;
		}
	}

	/**
	* 删除规格值
	* @param array $specvalueIds 要删除的IDS
	*/
	public function delValue( $specvalueIds ){
		if( empty( $specvalueIds ) || !is_array( $specvalueIds ) ) {
			return false;
		}

		foreach( $specvalueIds as $val ){
			if(!is_numeric($val) && $val< 1 ){
				return ;
			}
		}
		$ids = implode(',',$specvalueIds);

		$result = tbSpecvalue::model()->deleteByPk ( $specvalueIds,'hasProduct = 0' );
		return $result;
	}

	/**
	* 根据主键查找规格
	* @param integer $specId 规格ID
	*/
	public function getSpedByPk( $specId ) {
		if( empty( $specId ) ){
			return ;
		}
		$model = tbSpec::model()->findbyPk( $specId );
		return $model;
	}

	/**
	* 根据主键查找值
	* @param integer $id  主键值
	*/
	public function getSpedValueByPk( $id ){
		if( empty( $id ) ){
			return ;
		}
		$model = tbSpecvalue::model()->findbyPk( $id );
		return $model;
	}


	/**
	* 保存规格
	* @param array $specForm 保存提交的数据
	*/
	public function specSave( $specForm ){
		if( empty( $specForm ) || !is_array( $specForm ) ){
			return ;
		}
		$model = new tbSpec();
		$transaction = Yii::app()->db->beginTransaction();
		try {
			if( isset( $specForm['edit'] ) && is_array( $specForm['edit'] ) ) {
				foreach ( $specForm['edit'] as $key =>$val ){
					$_model = clone $model;
					$_model->isNewRecord = false;
					$_model->attributes = $val;
					$_model->specId = $key;
					if( !$_model->save() ){
						$error = $_model->getErrors();
						return $error;
					}

				}
			}
			if( isset( $specForm['add'] ) && is_array( $specForm['add'] ) ) {
				foreach ( $specForm['add'] as $key =>$val ){
					$_model = clone $model;
					$_model->attributes = $val;
					if( !$_model->save() ){
						$error = $_model->getErrors();
						return $error;
					}
				}
			}

			$transaction->commit();
			return true;
		} catch (Exception $e) {
			$transaction->rollback(); //如果操作失败, 数据回滚
			return false;
		}
	}

	/*
	* 规格标删
	* @param integer $specId 规格ID
	*/
	public function specDel( $specId ){
		if( empty( $specId ) ){
			return false;
		}
		$model = tbSpec::model()->findbyPk( $specId );
		if( !$model ) {
			return false;
		}

		$model->state = '1';
		if( $model->save() ){
			//删除所有与相关联的行业分类
			tbCategorySpec::model()->deleteAll( 'specId =:specId',array( ':specId'=>$specId ) );
			return true;
		} else {
			$error = $model->getErrors();
			return $error;
		}

	}


	/*
	* 取得规格值列表
	* @param integer $specId 规格ID
	*/
	public function getSpecValuelist( $specId,$keyword,$pageSize='10' ){
		if( empty( $specId ) ){
			return ;
		}
		$criteria=new CDbCriteria;
		$criteria->compare('t.specId',$specId);
		if(!empty( $keyword )){
			if( is_numeric( $keyword )){
				$criteria->addSearchCondition('t.serialNumber', $keyword);
			}else{
				$criteria->addSearchCondition('t.title', $keyword,true);
			}
		}
		$criteria->order = "serialNumber ASC"; //排序

		$model = new CActiveDataProvider('tbSpecvalue', array(
			'criteria'=>$criteria,
			'pagination'=>array('pageSize'=>$pageSize),
		));

		$data = $model->getData();

		$result['list'] = array_map( function($i){return $i->attributes;},$data);
		$result['pages'] = $model->getPagination();
		return $result;
	}

	/**
	* 取得色系
	*/
	public function getColorSeries(){
		return tbSetGroup::model()->getList( 2 );
	}



}