<?php
/**
 * 仓库入库单明细
 *
 * @property integer	$id
 * @property integer	$warrantId		入库单ID
 * @property integer	$orderId		采购单ID
 * @property integer	$num			入库数量
 * @property integer	$postQuantity	发货数量
 * @property timestamp	$storageTime	入库时间
 * @property string		$singleNumber	单品编码
 * @property string		$color			颜色
 * @property string     $batch          批次
 * @property string		$corpProductNumber	革厂产品编码
 * @property integer	$positionId  入库仓位ID
 *
 */
class tbWarehouseWarrantDetail extends CActiveRecord {

	public function init() {
		$this->storageTime = new CDbExpression('NOW()');
		$this->corpProductNumber = '';
		$this->postQuantity = 0; //默认发货数量为0，内部入库单
		$this->attachEventHandler('onAfterSave',array('tbWarehouseProduct','event_push'));
	}

	/**
	 * 返回基于自身的AR实例
	 * @param string $className 类名
	 * @return CActiveRecord 实例
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string 返回表名
	 */
	public function tableName()
	{
		return '{{warehouse_warrant_detail}}';
	}

	/**
	 * @return array 模型验证规则.
	 */
	public function rules()
	{
		return array(
			array('warrantId,orderId,postQuantity,num,positionId,singleNumber,storageTime,batch', 'required'),
			array('warrantId,orderId,orderId,positionId', "numerical","integerOnly"=>true),
			array('num', "numerical","integerOnly"=>false,'min'=>'0.1','max'=>'99999999'),
			array('corpProductNumber,batch', "length","max"=>'15'),
			array('singleNumber,color,total,corpProductNumber,batch','safe'),
		);
	}

	public function findAllByWarrant( $warrantId ) {
		$criteria = new CDbCriteria();
		$criteria->order = "singleNumber ASC";
		$criteria->addColumnCondition(array('warrantId'=>$warrantId));
		return $this->findAll( $criteria );
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'warrantId' => '入库单编号',
			'orderId' => '采购单编号',
			'num' => '入库数量',
			'singleNumber' => '产品编码',
			'color' => '颜色',
			'positionId' => '仓位',
			'corpProductNumber'=>'革厂产品编码',
			'postQuantity'=>'发货数量',
			'batch'=>'产品批次'
		);
	}

	public function getBuyTotal() {
		$orderInfo = tbOrderbuyProduct::model()->findByAttributes( array('orderId'=>$this->orderId,'singleNumber'=>$this->singleNumber) );
		if( $orderInfo ) {
			return $orderInfo->total;
		}
		return 'Not found';
	}

	public function getPostTotal() {
		$postInfo    = tbOrderPost::model()->findByAttributes( array('orderId'=>$this->orderId) );
		if( !$postInfo ) {
			return;
		}
		$postProduct = tbOrderPostProduct::model()->findByAttributes( array('postId'=>$postInfo->postId,'singleNumber'=>$this->singleNumber) );
		if( $postProduct ) {
			return $postProduct->total;
		}
		return 'Not found';
	}

	public function setTotal($info) {
		$this->num = $info;
	}

	public function getPositionName() {
		$position = tbWarehousePosition::model()->findByPk($this->positionId);
		if( $position ) {
			return $position->title;
		}
		return;
	}
}