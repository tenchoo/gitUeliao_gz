<?php
class BuyOrderDraft extends CModel {
	
	private $_order=array();
	private $_factoryNumber;
	private $_factoryName;
	private $_linkman;
	private $_phone;
	private $_address;
	private $_comment;
	
	public function attributeNames() {
		return array();
	}
}

class bod_row {
	private $_propertys=array(
		'singleNumber',
		'color',
		'factoryNumber',
		'dealTime',
		'comment',
		'form' => array()
	);
	
	public function __set($name,$value) {
		if( array_key_exists($name, $this->_propertys) ) {
			$this->_propertys[$name] = $value;
		}
	}
	
	public function __get($name) {
	if( array_key_exists($name, $this->_propertys) ) {
			return $this->_propertys[$name];
		}
	}
	
	public function pushRequest($serial,$total) {
		array_push( $this->_propertys['form'], array('serial'=>$serial, 'total'=>$total) );
	}
	
	public function serialize() {
		return serialize( $this->_propertys );
	}
}