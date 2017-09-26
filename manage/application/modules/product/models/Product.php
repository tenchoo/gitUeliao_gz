<?php
/**
 * 产品管理
 * @version 0.1
 * @package model
 */
class Product {

	/**
	* 产品列表
	* @param integer/array $state 产品状态
	* @param array $conditions 过滤条件
	* @param pageSize $pageSize 每页显示条数
	*/
	public static function getList($state, $conditions = array(),$pageSize=2 ){
		$criteria = new CDbCriteria;
		$criteria->select = "productId,type,baseProductId,title,mainPic,serialNumber,price,tradePrice,state,updateTime";
		if( $state == '2' ){
			$criteria->addNotInCondition('state', array(0,1));
		}else{
			$criteria->compare('state', $state);
		}
		
		$criteria->order = "productId DESC";

		foreach ( $conditions as $key=>$val ){
			if($val=='') continue;
			if( $key == 'hasVoice' && $val ){
				$sound = tbProductSound::model()->tableName();
				$criteria->addCondition( "exists (select null from $sound s where t.productId = s.productId and state = 0  )" );
			}else if( $key == 'serialNumber' ) {
				$criteria->compare($key, $val,true);
			}else {
				$criteria->compare($key, $val);
			}
		}

		if( $order = Yii::app()->request->getQuery('order') ) {
			$field = substr($order,0,-2);
			$order = substr($order,-2)=='up'? 'ASC':'DESC';
			$criteria->order = sprintf("%s %s",$field,$order);
		}

		$model = new CActiveDataProvider('tbProduct', array(
			'criteria'=>$criteria,
			'pagination'=>array('pageSize'=>$pageSize,'pageVar'=>'page'),
		));

		$data = $model->getData();
		$list = array();
		foreach ( $data as $key=>$val ){
			$val->updateTime = date('Y-m-d H:i',strtotime($val->updateTime));
			$list[$key] = $val->attributes;
			$list[$key]['total'] = tbWarehouseProduct::model()->productCount( $val->productId );
			//$list[$key]['total'] = tbProductStock::model()->getTotal( $val->productId );
			$list[$key]['dealCount'] = tbOrderProduct::model()->dealCount( $val->productId );

			/** if( $val['type'] == tbProduct::TYPE_CRAFT ){
				$list[$key]['editUrl'] = Yii::app()->createUrl('/product/publish/saleinfo',array('id' => $val->productId ));
			}else{
				$list[$key]['editUrl'] = Yii::app()->createUrl('/product/publish/edit',array('id' => $val->productId ));
			} */


		}

		$product['data']  = $list;
		$product['pages'] = $model->getPagination();
		return $product;
	}

	/**
	* 修改产品状态
	* @param integer $state 产品状态
	* @param integer/array $productId 产品ID
	*/
	public static function changeState( $state,$productId ){
		if(!in_array($state,array(0,1,2)) || empty($productId) ){
			return false;
		}

		$set = array('state'=>$state,'updateTime'=>date('Y-m-d H:i:s'));
		if($state=='0'){
			$set['salesTime'] = date('Y-m-d H:i:s');//上架时间
		}
		$count = tbProduct::model()->updateByPk($productId,$set);
		return $count;
	}
}