<?php
/**
 * 尾货产品列表
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$id
 * @property integer	$tailId			尾货产品编号
 * @property integer	$state			产品状态:0:正常，1:已删除
 * @property integer	$createTime		生成时间
 * @property integer	$updateTime		最后编辑时间
 * @property integer	$lastSaleTime	最后销售时间
 * @property string		$singleNumber	单品编码
 *
 */

 class tbTailSingle extends CActiveRecord {

	const STATE_NORMAL = 0; //产品状态:0:正常
	const STATE_DEL = 1; //产品状态:1:已删除

	public function rules() {
		return array(
			array('tailId,singleNumber','required'),
			array('tailId', "numerical","integerOnly"=>true),
			array('singleNumber','safe'),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'tailId' => '尾货ID',
			'singleNumber' => '产品编码',
		);
	}

	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{tail_single}}";
	}

	/**
	 * 取得单品的库存
	 */
	public function getStock(){
		if( empty( $this->singleNumber ) ) return ;

		$stock = array();
		$model = tbWarehouseCount::model()->findAll('singleNumber = :s',array(':s'=>$this->singleNumber ) );
		foreach ( $model as $_stock ){
			if( $_stock->num >0 ){
				$stock [] = $_stock->attributes;
			}
		}
		return $stock;
	}
	
	


	/**
	 * 保存前的操作
	 */
	protected function beforeSave(){
		if($this->isNewRecord){
			$this->createTime = time();
		}
		$this->updateTime = time();
		return parent::beforeSave();
	}
}