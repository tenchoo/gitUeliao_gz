<?php
/**
 * 产品收藏记录
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$id
 * @property integer	$productId	产品ID
 * @property integer	$memberId	客户ID
 * @property timestamp	$createTime
 *
 */

 class tbProductCollection extends CActiveRecord {

	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{product_collection}}";
	}

	public function init(){
		$this->createTime = new CDbExpression('NOW()');
	}

	public function rules() {
		return array(
			array('productId,memberId','required'),
			array('memberId,productId', "numerical","integerOnly"=>true),
		);
	}


	public function relations(){
		return array(
			'product'=>array(self::BELONGS_TO,'tbProduct','productId','select'=>'price,unitId,title,serialNumber,mainPic'),
		);
	}

	/**
	* 取消收藏,从产品收藏表中删除相应的记录。可批量删除。
	* @param string  $productId 需删除的产品ID串，例1,2,3,4
	*/
	public static function cancleCollection( $productId,$memberId ){
		if( empty($productId) || empty($memberId)  ) return false;
		$c = new CDbCriteria;
		$c->compare('productId',explode(',',$productId ) );
		$c->compare('memberId',$memberId );

		if( self::model()->deleteAll($c) ) return true;
		return false;
	}

	/**
	* 增加收藏
	* @param integer  $productId
	*/
	public static function addCollection( $productId,$memberId ){
		if( !is_numeric($productId) || $productId<1 || empty($memberId) ) return false;

		//查找产品是否存在。
		$exists = tbProduct::model()->exists('productId = :id',array(':id'=>$productId));
		if(!$exists) return false;

		if( self::checkCollection( $productId,$memberId ) ){
			return true;
		}

		$model = new tbProductCollection;
		$model->productId = $productId;
		$model->memberId = $memberId;
		return $model->save();
	}

	/**
	* 检查是否已收藏
	* @param integer  $productId
	*/
	public static function checkCollection( $productId,$memberId ){
		if( !is_numeric($productId) || $productId<1 || empty($memberId) ) return false;
		$exists = self::model()->exists('productId = :id and memberId = :mid',
							array(':id'=>$productId,':mid'=>$memberId));
		return $exists ;
	}

	/**
	* 收藏列表
	* @param integer  $pageSize
	*/
	public static function getList( $memberId ,$pageSize = '1' ){
		if( !is_numeric($pageSize) || $pageSize<1 || empty($memberId) ) return false;
		$criteria=new CDbCriteria;
		$criteria->compare('t.memberId',$memberId );
		$criteria->order = 't.createTime DESC';
		$criteria->with = 'product';

		$model = new CActiveDataProvider('tbProductCollection',array(
					'criteria'=>$criteria,
					'pagination'=>array('pageSize'=>$pageSize,'pageVar'=>'page'),
				));
		$data = $model->getData();
		$return['list'] = array();
		foreach ( $data as $val ){
			if(!empty($val->product)){
				$info = $val->product->getAttributes('productId','price','unitId','title','serialNumber','mainPic');
				$info['favTime'] = date('Y/m/d',strtotime( $val->createTime ));
				array_push($return['list'],$info);
			}
		}
		$return['pages']= $model->getPagination();
		return $return;
	}


}