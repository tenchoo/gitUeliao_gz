<?php
/**
 * 工厂发货单
 * @author yagas
 * @package CModel
 * @version 0.1
 */
class PostOrder extends CModel {
	
	public $orderId;
	public $logisticId;
	public $logisticNumber;
	public $logisticName;
	public $postTime;
	public $childrens = array();
	
	public function attributeNames() {
		return array('orderId','logisticId','logisticName','logisticNumber','postTime','childrens');
	}
	
	/**
	 * 数据校验
	 * @see CModel::rules()
	 */
	public function rules() {
		return array(
			array('orderId,logisticId,logisticName,logisticNumber,postTime','required'),
			array('orderId,logisticId','numerical'),
			array('childrens','valifyProduct'),
		);
	}
	
	/**
	 * 添加产品信息
	 @param tbOrderbuyRelate $relate 单品编码
	 */
	public function postProduct( tbOrderbuyRelate $relate, $total ) {
		$product = new tbOrderPostProduct();
		$product->attributes = $relate->getAttributes( array('singleNumber','color','unitName','comment') );
		$product->total = $total;
		$product->orderbuyId = $relate->orderId;
		array_push( $this->childrens, $product );
	}
	
	/**
	 * 批量添加产品信息
	 @param string $data
	 */
	public function products( $idsArray=null ) {
		if( !is_null($idsArray) && is_array($idsArray) ) {
			foreach($idsArray as $singleNumber=>$post) {
				$relate = tbOrderbuyRelate::model()->findByAttributes( array('orderProductId'=>$post['orderProId']) );
				if( !is_null($relate) ) {
					$this->postProduct( $relate, $post['total'] );
				}
			}
			return true;
		}
		else {
			return $this->childrens;
		}
	}
	
	/**
	 * 产品信息进行验证
	 * @param string $fiedName
	 * @return boolean
	 */
	public function valifyProduct( $fiedName ) {
		$data = $this->$fiedName;
		if( count($data) == 0 ) {
			return false;
		}
		
		foreach( $data as $item ) {
			if( !$item->validate(array('singleNumber','total')) ) {
				if( !$this->hasErrors($fiedName) ) {
					$this->addError($fiedName, 'Invalid singleNumber or post count.');
					return false;
				}
			}
		}
		
		return true;
	}
	
	/**
	 * 进行数据校验之前对时期进行转换
	 * @see CModel::beforeValidate()
	 */
	public function beforeValidate() {
		if( !is_null($this->postTime) ) {
			$this->postTime = strtotime( $this->postTime );
			return parent::beforeValidate();
		}
	}
	
	/**
	 * 保存发货单信息入库
	 * @return boolean
	 */
	public function save() {
		if( $this->validate() ) {
			$postOrder = new tbOrderPost();
			$postOrder->attributes = $this->getAttributes(array('orderId','logisticId','logisticName','logisticNumber','postTime'));
			$postOrder->orderType = tbOrderPost::TYPE_USER;

			//开户事务处理
			$tr = Yii::app()->getDb()->beginTransaction();
			
			if( !$postOrder->save() ) {
				$this->addErrors( $postOrder->getErrors() );
			}
			
			if( !$this->hasErrors() ) {
				foreach( $this->childrens as $item ) {
					$item->postId = $postOrder->postId;
					if( !$item->save() ) {
						$this->addError('childrens', 'Faild in save record id:'.$item->singleNumber);
					}
				}
			}
			
			//事务回滚或提交
			if( !$this->hasErrors() ) {
				$tr->commit();
				$buy = tbOrderbuy::model()->findByPk( $this->orderId );
				if( $buy ) {
					$buy->state = tbOrderbuy::STATE_FINISHED;
					$buy->save();
				}
				return true;
			}
			else {
				$tr->rollback();
			}
		}
		return false;
	}
}