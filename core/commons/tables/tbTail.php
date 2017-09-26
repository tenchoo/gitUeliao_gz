<?php
/**
 * 尾货产品
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$tailId			尾货产品编号
 * @property integer	$source			尾货来源：1.由呆滞报表转成，2.由产品直接转成
 * @property integer	$isSoldOut		是否已售完
 * @property integer	$productId		产品ID
 * @property decimal	$price			单价
 * @property decimal	$tradePrice		大货价
 * @property decimal	$salesVolume	销售数量
 * @property integer	$saleType		销售类型，零售和整批销售enum('retail', 'whole')
 * @property integer	$state			产品状态: enum('selling', 'underShelf', 'del','recycledel')
 * @property integer	$createTime		生成时间
 * @property integer	$updateTime		最后编辑时间
 *
 */

 class tbTail extends CActiveRecord {

	const SOURCE_GLASS = 1; //由呆滞报表转成
	const SOURCE_PRODUCT = 2; //由产品直接转成

	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{tail}}";
	}

	public function relations(){
		return array(
			'product'=>array(self::BELONGS_TO,'tbProduct','productId','select'=>'title,unitId,serialNumber,mainPic'),
			'single'=>array(self::HAS_MANY,'tbTailSingle','tailId','condition'=>'single.state = 0'),
		);
	}

	public function rules() {
		return array(
			array('source,productId,price,tradePrice,saleType','required'),
			array('source', 'in', 'range'=>array('1','2')),
			array('saleType', 'in', 'range'=>array('retail', 'whole')),
			array('price,tradePrice', "numerical",'min'=>'0.01','max'=>'10000'),
			array('productId', "numerical","integerOnly"=>true),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'source' => '尾货来源',
			'productId' => '产品ID',
			'price' => '单价/零售价',
			'tradePrice'=>'大货价',
			'saleType' => '销售类型',
		);
	}

	/**
	 * 保存前的操作
	 */
	protected function beforeSave(){
		if($this->isNewRecord){
			//设置tailId
			$this->tailId = tbProduct::model()->getAutoIncrement();
			$this->createTime = time();
		}
		$this->updateTime = time();	

		if( $this->saleType == 'whole' ){
			$this->tradePrice = $this->price;
		}
		
		//'1:retail 2:whole'
		$this->saleType = ( $this->saleType == 'whole' )?2:1;
		
		return parent::beforeSave();
	}
	
	protected function afterFind(){	
		$this->saleType = ( $this->saleType == '1' )?'retail':'whole';		
		return parent::afterFind();
		
	}

	public function saleTypes(){
		return array('retail'=>'降价促销', 'whole'=>'整批销售');
	}

	/**
	* 产品状态
	*/
	public function tailStates(){
		return array('selling', 'underShelf', 'del','recycledel');
	}	

}