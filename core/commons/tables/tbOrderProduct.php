<?php
/**
 * 订单表
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$orderProductId
 * @property integer	$orderId			订单ID
 * @property integer	$productId 			产品ID
 * @property integer	$tailId 			尾货ID
 * @property numerical	$price				商品实际成交单价
 * @property numerical	$salesPrice			商品销售单价
 * @property integer	$num				购买数量
 * @property integer	$isSample			是否赠板
 * @property integer	$stockId			产品规格库存表ID
 * @property integer	$packingNum			已拣货数量
 * @property integer	$deliveryNum		已发货数量
 * @property integer	$receivedNum		已收货数量
 * @property integer	$state				状态:0正常，1已取消购买
 * @property string		$saleType			销售类型enum('normal', 'retail', 'whole')
 * @property string		$title				产品标题
 * @property string		$color				颜色
 * @property string		$serialNumber		产品编号
 * @property string     $singleNumber		单品编码
 * @property string		$mainPic			产品图片
 * @property string		$specifiaction		产品规格
 * @property string		$remark				备注
 *
 */

 class tbOrderProduct extends CActiveRecord {

	/**
	* @var int 是否更改销定的可销售量
	*/
	public $isfree = 0;

	public $orderType;

	public function init() {
		parent::init();

		//触发器绑定[商家日销量统计]
//		$this->attachEventHandler('onAfterSave', array('tbSellerSales', 'salesLog'));
	}

	 public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{order_product}}";
	}

	public function rules() {
		return array(
			array('orderId,stockId,productId,price,num,title,mainPic,specifiaction','required'),
			array('isSample','in','range'=>array(0,1)),
			array('saleType','in','range'=>array('normal','whole','retail')),
			array('price', "numerical",'max'=>'99999'),
			array('num,packingNum,deliveryNum,receivedNum', "numerical","integerOnly"=>false,'max'=>'99999999'),
			array('orderId,stockId,productId,tailId', "numerical","integerOnly"=>true),
			array('title,mainPic,specifiaction,serialNumber,color,remark,singleNumber', "safe"),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'orderId' => '订单ID',
			'productId' => '产品ID',
			'price' => '商品单价',
			'num' => '购买数量',
			'title' => '产品标题',
			'mainPic'=>'产品图片',
			'specifiaction'=>'产品规格',
			'serialNumber'=>'产品编号',
			'isSample'=>'是否赠板',
			'remark'=>'备注',
			'color'=>'颜色'
		);
	}

	/**
	* 计算总销售量
	* @param integer $productId
	* @param integer $tailId 尾货产品ID
	*/
	public function dealCount( $productId ,$tailId = 0){
		$criteria = new CDbCriteria;
		$criteria->select = 'SUM(t.num) as num ';
		$criteria->compare('t.state','0');
		if( $tailId >0 ){
			$criteria->compare('t.tailId',$tailId);
		}else{
			$criteria->compare('t.productId',$productId);
			$criteria->compare('t.tailId',0);
		}

		$count = $this->find( $criteria );
		return ( $count )? Order::quantityFormat( $count->num  ) :0;
	}

	/**
	* 产品成交计录售量--普通产品
	* @param integer $productId
	*/
	public function dealList( $productId ,$page = '1',$perpage = '10' ){
		$offset = ($page)?($page-1)*$perpage:0;
		$sql = "SELECT m.nickName, t.price,t.num,t.specifiaction,o.createTime,mp.icon from  {$this->tableName()} t,{{order}} o ,{{member}} m left join {{profile}} mp on (m.memberId = mp.memberId ) where t.productId =$productId and t.tailId = 0 and t.state = 0 and t.orderId = o.orderId and o.memberId = m.memberId order by  o.createTime desc limit $offset, $perpage";
		$result = $this->getDbConnection()->createCommand($sql)->queryAll();
		foreach ($result as &$val){
			$val['nickName'] = tbMember::half_replace( $val['nickName'] );
			$val['num']    = Order::quantityFormat( $val['num'] ) ;
			$val['price']    = Order::priceFormat( $val['price'] ) ;
		}
		return $result;
	}

	/**
	* 产品成交计录售量--尾货产品
	* @param integer $tailId 尾货产品ID
	*/
	public function taildealList( $tailId ,$page = '1',$perpage = '10' ){
		$offset = ($page)?($page-1)*$perpage:0;
		$sql = "SELECT m.nickName, t.price,t.num,t.specifiaction,o.createTime,mp.icon from  {$this->tableName()} t,{{order}} o ,{{member}} m left join {{profile}} mp on (m.memberId = mp.memberId ) where t.tailId =$tailId and t.orderId = o.orderId and o.memberId = m.memberId order by  o.createTime desc limit $offset, $perpage";
		$result = $this->getDbConnection()->createCommand($sql)->queryAll();
		foreach ($result as &$val){
			$val['nickName'] = tbMember::half_replace( $val['nickName'] );
			$val['num']    = Order::quantityFormat( $val['num'] ) ;
			$val['price']    = Order::priceFormat( $val['price'] ) ;
		}
		return $result;
	}


	/**
	 * 保存前的操作
	 */
	protected function beforeSave(){
		if($this->isNewRecord){
			$this->salesPrice = $this->price;
		}
		return true;
	}

	public function getUnitName() {
		if( $this->getScenario() == 'update' ) {
			$product = tbProduct::model()->findByPk( $this->productId );
			if( $product ) {
				return tbUnit::getUnitName( $product->unitId );
			}
		}
		return '';
	}

	public function getDealTime() {
		$criteria            = new CDbCriteria();
		$criteria->condition = "orderId=:id";
		$criteria->params    = array( ':id' => $this->orderId );
		$criteria->order     = "exprise ASC";
		$result              = tbOrderBatches::model()->find( $criteria );

		if( $result ) {
			return strtotime($result->exprise);
		}
		return '0';
	}

	public function getComment() {
		return $this->remark;
	}

	public function getTotal() {
		return $this->num;
	}

	public function getSerial() {
		return $this->orderId;
	}

	public function getFrom() {
		return tbRequestbuy::FORM_ORDER;
	}

	public function getRequestProductId() {
		return $this->orderId;
	}

	 /**
	  * 获取供应商ID
	  * @return bool|int
	  */
	public function getSupplierId() {
		$procurement = tbProcurement::model()->findByAttributes(['productId'=>$this->productId]);
		if(is_null($procurement)) {
			return false;
		}
		return $procurement->supplierId;
	}


	/**
	 * 保存后的操作
	 */
	protected function afterSave(){
		if( $this->isNewRecord ){
			if( in_array( $this->orderType,array('0','2','3') ) ){
				//现货订单下单的同时锁定可销售量，订货订单在采购回来后锁定
				$this->onAfterSave = array('tbStorageLock','lock');
				$obj = new stdclass;
				$obj->orderId = $this->orderId;
				$obj->total = $this->num;
				$obj->singleNumber = $this->singleNumber;
				$this->onAfterSave( new CEvent( $obj )  );
			}

			//lastSaleTime
			tbProductStock::model()->updateAll ( array('lastSaleTime'=>date('Y-m-d H:i:s') ,'singleNumber =:s',array(':s'=>$this->singleNumber) ));
		}else{
			if( $this->isfree ){
				$this->onAfterSave = array('tbStorageLock','free');
				$obj = new stdclass;
				$obj->orderId = $this->orderId;
				$obj->total = ( $this->isfree === 2 ) ? $this->deliveryNum : $this->num;
				$obj->singleNumber = $this->singleNumber;
				$obj->state = $this->state;
				$this->onAfterSave( new CEvent( $obj )  );
			}
		}


		//尾货状态更改，整批销售的只允许销售一次。
		if( $this->isNewRecord && $this->orderType == tbOrder::TYPE_TAIL  && $this->saleType == 'whole'	){
			tbTail::model()->updateByPk( $this->tailId,array('isSoldOut'=>'1'),'state = :state and isSoldOut = 0 ',array( ':state'=>'selling' ) );
		}

//		if($this->isNewRecord) {
//			$this->onAfterSave( new CEvent( $this )  );
//		}

	}

	/**
	 * find 查找后的统一数据格式
	 */
	protected function afterFind(){			
		$this->num = Order::quantityFormat( $this->num );
		$this->price = Order::priceFormat($this->price,'');
		$this->salesPrice = Order::priceFormat( $this->salesPrice,'' );
	}
}
