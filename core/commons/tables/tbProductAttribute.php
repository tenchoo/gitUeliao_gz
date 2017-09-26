<?php
/**
 * 商品属性表
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$productId			产品ID
 * @property integer	$attrId 			属性标题ID
 * @property string		$attrValue		 	属性值
 *
 */
class tbProductAttribute extends CActiveRecord {

	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{product_attribute}}";
	}

	public function rules() {
		return array(
			array('productId,attrId,attrValue','required'),
			array('productId,attrId','numerical'),
			array('attrValue','safe'),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'productId' => '产品ID',
			'attrId' => '属性标题ID',
			'attrValue' => '属性值',
		);
	}

	protected function beforeSave()	{
		$this->setId();
		return true;
	}

	protected function afterSave()	{
		//清除缓存
		$cacheName = $this->cacheName( $this->productId );
		Yii::app()->cache->set( $cacheName,null );

		return parent::afterSave();
	}

	private function cacheName( $productId ){
		return 'productAttribtes_'.$productId;
	}


	private function setId(){
		$this->attrValue = tbAttrValue::model()->setIds( $this->attrValue );
	}

	/**
	 * 获取产品属性信息
	 * @param integer $productId
	 * @return array
	 */
	public function productAttribtes( $productId ) {

		$cacheName = $this->cacheName( $productId );
		$data = Yii::app()->cache->get($cacheName);//获取缓存

		if( empty($data) ){
			$data = $this->getAttribtesByProductId( $productId );
			Yii::app()->cache->set( $cacheName,$data,0 );
		}
		return $data;
	}

	private function getAttribtesByProductId( $productId ) {

		$product = tbProduct::model()->find( array(
								'select'=>'categoryId',
								'condition'=>'productId = :productId',
								'params'=> array(':productId'=>$productId)
								) );
		if( !$product ){
			return array();
		}

		$cateAttri = tbAttribute::model()->findAll( array(
								'select'=>'setGroupId,attrId',
								'condition'=>'categoryId = :categoryId and state = 0',
								'params'=> array(':categoryId'=>$product->categoryId)
								)  );
		if( empty( $cateAttri ) ){
			return array();
		}

		$groups = array();
		foreach ( $cateAttri as $val ){
			$groups[$val->attrId] = $val->setGroupId;
		}

		$result = array();
		$tbAttrValue = new tbAttrValue();
		$tbAttr = new tbAttr();
		$model = $this->model()->findAll( 'productId = :productId',array(':productId'=>$productId) );
		foreach ( $model as $val ){
			$info = array();
			$info['title'] 		= $tbAttr->titleName( $val->attrId );
			$attrValue =  $tbAttrValue->getValueById( $val->attrValue );
			$info['attrValue']	= implode( ',',$attrValue );
			$info['setGroupId']		= array_key_exists( $val->attrId,$groups )?$groups[$val->attrId]:0;
			$result[] = $info;
		}

		return $result;
	}


}