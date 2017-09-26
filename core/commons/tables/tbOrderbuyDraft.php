<?php
/**
 * 采购单草稿箱
 * @author yagas
 * @package CActiveRecord
 */
class tbOrderbuyDraft extends CActiveRecord {
	
	public $userId;
	public $requestId;
	
	public function tableName() {
		return '{{order_buy_draft}}';
	}
	
	public static function model( $className=__CLASS__ ) {
		return parent::model( $className );
	}
	
	public function rules() {
		return array(
			array('userId,requestId','required')
		);
	}
	
	public function findAllByDraft( $userId ) {
		$result = $this->with('requestProduct')->findAllByAttributes(array("userId"=>$userId));
		if( $result ) {
			$data = array();
			foreach ( $result as $item ) {		
				$request = $item->requestProduct;
				$serial  = $request->singleNumber;
				if( !array_key_exists($serial,$data) ) {
					$row = new tbOrderbuyProduct();
					$row->singleNumber = $serial;
					$row->color = $request->color;
					$data[$serial] = $row;
					$request->orderId = tbRequestbuy::model()->orderSerial($request->orderId);
				}
				$data[$serial]->total += intval($request->total);
				$data[$serial]->pushRequest( $request );
			}
			return $data;
		}
		return array();
	}
	
	public function relations() {
		return array(
			"requestProduct" => array(self::HAS_ONE, 'tbRequestbuyProduct',array("id"=>"requestId")),
		);
	}
}