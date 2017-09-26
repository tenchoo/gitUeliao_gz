<?php
class tbOrderPostAssign extends CActiveRecord {
	
	public $assignId;
	public $postProductId;
	public $orderbuyProductId;
	public $total;
	public $singleNumber;
	public $isAssign;
	
	public static function model( $className=__CLASS__) {
		return parent::model( $className );
	}
	
	public function tableName() {
		return '{{order_post_assign}}';
	}
	
	public function primaryKey() {
		return 'assignId';
	}
	
	public function rules() {
		return array(
			array('postProductId,orderbuyProductId,singleNumber,total,isAssign','required'),
			array('total,isAssign','numerical','min'=>1)
		);
	}
	
	public function init() {
		parent::init();
		$this->isAssign = 1;
		
		$this->attachEventHandler('onAfterSave', array('tbOrderPostAssign','updateAssignState'));
	}
	
	public static function updateAssignState(CEvent $event) {
		$sender = $event->sender;
		
		if( !($sender instanceof tbOrderPostAssign) ) {
			return false;
		}
		
		if( !$sender->isNewRecord ) {
			return false;
		}		
		
		
		$product = tbOrderPostProduct::model()->findByPk($sender->postProductId);
		if( is_null($product) ) {
			return false;
		}
		
		$product->isAssign = 1;
		$product->save();
		return true;
	}
}