<?php
/**
 * 低安全库存据库表
 * @author yagas
 * @package CActiveRecord
 */
class tbRequestlower extends CActiveRecord {
	
	public $lowerId;
	public $singleNumber;
	public $buyTotal;
	public $createTime;
	public $state;
	public $color='';
	
	private $_unitName;
	
	const STATE_NORMAL      = 0;	
	const STATE_CHECKED     = 1;
	const STATE_WAITING     = 2;
	const STATE_PROCCESSING = 3;
	const STATE_FINISHED    = 4;
	const STATE_CLOSE       = 5;
	const STATE_DELETE      = 6;
	
	public function init() {
		parent::init();
		$this->state  = self::STATE_NORMAL;
		$this->createTime = time();
	}
	
	public static function model( $className=__CLASS__ ) {
		return parent::model( $className );
	}
	
	public function tableName() {
		return '{{request_lower}}';
	}
	
	public function primaryKey() {
		return 'lowerId';
	}
	
	public function getSafetyStock() {
		$safety = tbProductStock::model()->findByAttributes( array('singleNumber'=>$this->singleNumber) );
		if( $safety instanceof tbProductStock ) {
			return $safety->safetyStock . $this->unitName;
		}
		
		$message = Yii::t('order','not found safety stock by {serial}',array('{serial}'=>$this->singleNumber));
		Yii::log($message,CLogger::LEVEL_WARNING,__CLASS__.'::getSafetyStock');
		return $message;
	}
	
	/**
	 * 当前库存
	 * @return number
	 */
	public function getHasTotal() {
		return ProductModel::total( $this->singleNumber ).$this->unitName;
	}
	
	/**
	 * 计价单位
	 * @return string
	 */
	public function getUnitName() {
		if( is_null($this->_unitName) ) {
			$this->_unitName = ZOrderHelper::getUnitName( $this->singleNumber );
		}
		return $this->_unitName;
	}
	
	/**
	 * 当前采购数量
	 */
	public function getBuyTotal() {
		return $this->buyTotal.$this->unitName;
	}
	
	/**
	 * 满足最低安全库存还需要采购数量
	 * @return string
	 */
	public function getNeedTotal() {
		$need = $this->safetyStock - $this->buyTotal;
		if( $need<0 ) {
			$need = 0;
		}
		return $need . $this->unitName;
	}

	/**
	 * 指定产品是否已经添加采购
	 * @param $productCode
	 * @return bool
	 */
	public static function isRequest( $productCode ) {
		$criteria = new CDbCriteria();
		$criteria->condition = "singleNumber=:serial and state not in(4,5,6)";
		$criteria->params = array(':serial'=>$productCode);
		$result = tbRequestlower::model()->count( $criteria );
		return $result>0;
	}

	/**
	 * 添加低安全库存请购
	 * @param $productCode
	 * @param $color
	 * @param $total
	 * @return bool
	 */
	public static function appendRequest( $productCode, $color, $total ) {
		
		if(tbRequestlower::model()->exists("singleNumber=:serial and state<4", [':serial'=>$productCode])) {
			return true;
		}		
		
		$serial               = new tbRequestlower();
		$serial->singleNumber = $productCode;
		$serial->color        = $color;
		$serial->buyTotal     = $total;

		if( $serial->save() ) {
			return true;
		}

		$errors = $serial->getErrors();
		$error  = array_shift($error);
		Yii::log($error[0], CLogger::LEVEL_ERROR, __CLASS__.'::'.__FUNCTION__);
		return false;
	}

	protected function afterSave() {
		if( $this->isNewRecord ) {
			//自动添加到待采购队列
			tbOrderPurchase2::importOrder( $this );
		}

		parent::afterSave();
	}
}