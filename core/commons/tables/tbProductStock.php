<?php
/**
 * 产品规格的库存和价格
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$stockId
 * @property integer	$productId				产品ID
 * @property integer	$state					状态：0正常，1已取消
 * @property integer	$depositRatio			支付订金比例，为百分比
 * @property integer	$adjustRatio			调整单比例，为千分比
 * @property integer	$glassTime				默认呆滞时长，单位为小时
 * @property integer	$safetyStock			安全库存
 * @property string		$singleNumber			单品编码
 * @property string		$relation				产品规格组合
 *
 */

 class tbProductStock extends CActiveRecord {

	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{product_stock}}";
	}

	public function relations() {
		return array(
			'product' => array(self::BELONGS_TO,'tbProduct','productId')
		);
	}

	public function rules() {
		return array(
			array('productId,singleNumber,relation,depositRatio,safetyStock','required'),
			array('productId,safetyStock','numerical','integerOnly'=>true,'min'=>0),
			array('safetyStock','numerical','integerOnly'=>true,'min'=>0,'max'=>'100000'),
			array('depositRatio','numerical','integerOnly'=>true,'min'=>0,'max'=>'100'),
			array('adjustRatio','numerical','integerOnly'=>true,'min'=>0,'max'=>'1000'),
			array('glassTime','numerical','integerOnly'=>true,'min'=>0,'max'=>'10000'),
			array('singleNumber,relation','safe'),
			array('singleNumber','unique'),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'productId' => '产品ID',
			'state' => '状态',
			'safetyStock' => '安全库存',
			'singleNumber' => '单品编码',
			'relation'=>'产品规格组合',
			'depositRatio'=>'订金比例',
		);
	}

	/**
	* 生成单品编码
	* @param string $serialNumber 产品编码
	* @param string $relation 规格组合
	*/
	public function singleSerialNumber( $serialNumber,$relation ){
		if( empty ( $serialNumber ) || empty ( $relation ) ){
			return '';
		}
		$attr = $this->relationToArray( $relation );
		if( !is_array( $attr ) ){
			return '';
		}
		sort($attr); //这句必须,保证规格组合顺序不同返回同一值
		$spec = tbSpecvalue::model()->findByPk($attr);
		if( !$spec ){
			return '';
		}
		$arr = array($serialNumber);
		if( is_array($spec) ){
			foreach( $spec as $val ){
				if(!empty( $val->serialNumber )){
					$arr[] = $val->serialNumber;
				}
			}
		}else{
			$arr[] = $spec->serialNumber;
		 }
		return implode('-',$arr); //单品编码分割用中横杠
	}



	/**
	* 把relation转为数组
	*/
	public static function relationToArray( $relation ){
		return json_decode( '{'.preg_replace('/(\w+):/is', '"$1":', $relation).'}', true );
	}

	/**
	* 取得产品规格库存信息列表
	* @param integer $productId
	* @param array $stockIds 指定的规格库存IDS
	*/
	public function specStock ( $productId, array $stockIds = array() ){
		if( empty ( $productId )){
			return array();
		}

		$criteria = new CDbCriteria;
		$criteria->compare('productId', $productId);
		$criteria->compare('state', '0');
		if( !empty( $stockIds ) ){
			$criteria->compare('stockId', $stockIds );
		}

		$models = $this->findAll( $criteria );
		$result = array();
		foreach ( $models as $val ){
			$result[$val->stockId] = $val->attributes;
		}

		return $result;
	}

	/**
	* 按单品编号取得产品规格库存信息列表
	* @param array $singleNumber 指定的单品编号
	*/
	public function specBySingles( array $singleNumber = array() ){
		if( empty ( $singleNumber )){
			return array();
		}

		$criteria = new CDbCriteria;
		$criteria->compare('singleNumber', $singleNumber);
		$models = $this->findAll( $criteria );
		return array_map( function ($i){ return $i->attributes;},$models );
	}

	/**
	* 取得产品单位
	* @param string $singleNumber
	*/
	public function unitName($singleNumber) {
		$model = $this->findByAttributes(array('singleNumber'=>$singleNumber));
		if( !is_null($model) ) {
			$product = $model->product;
			if( $product ) {
				return $unitName = tbUnit::getUnitName( $product->unitId );
			}
		}
	}

	public function getColor() {
		$relation = explode(',', $this->relation);
		list( $spec, $val ) = explode(':', $relation[0]);
		$color              = tbSpecvalue::model()->findByPk( $val );
		if( is_null($color) ) {
			return;
		}
		return $color->title;
	}

	public static function fetchProductId( $singleNumber ) {
		$product = tbProductStock::model()->findByAttributes( array('singleNumber'=>$singleNumber));
		if( is_null($product) ) {
			return $product->productId;
		}
		return;
	}

	/**
	* 根据 singelNumber 取得规格名称显示
	*
	*/
	public static function relationTitle( $singleNumber,&$color = '',&$stockId = '' ){
		$model = tbProductStock::model()->findByAttributes( array('singleNumber'=>$singleNumber));
		if( is_null($model) ) {
			return '';
		}

		$stockId = $model->stockId;
		$spec = self::relationToArray( $model->relation );
		$specs = tbSpecvalue::getSpecs( $spec );

		if( empty($specs) ) {
			return '';
		}

		$relation = '';
		foreach ( $spec as $k=>$reval ){
			if( array_key_exists( $reval, $specs ) ){
				$relation .= $specs[$reval]['specName'].':'.$specs[$reval]['title'].' '.$specs[$reval]['serialNumber'];
				if($k == '1'){
					$color = $specs[$reval]['title'];
				}
			}
		}
		return $relation;
	}

	protected function afterSave(){
		if( $this->isNewRecord ){
			list( $spec, $val ) = explode(':', $this->relation);
			$color              = tbSpecvalue::model()->updateByPk( $val,array('hasProduct'=>'1') );
		}else{
			if( $this->safetyStock >0 ){
				//获取单品可售量，低于低安全库存时添加到低安全库存采购
				ProductModel::total($this->singleNumber);
			}
		}

	}

}