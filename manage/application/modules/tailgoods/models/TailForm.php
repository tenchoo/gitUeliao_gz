<?php
/**
 * 尾货管理
 * @author liang
 * @version 0.1
 * @package CFormModel
 */
class TailForm extends CFormModel {

	public $saleType,$price,$tradePrice;

	public function rules()	{
		return array(
			array('price,tradePrice', 'required'),
			array('price,tradePrice','numerical','min'=>'0.01','max'=>'100000'),
			array('saleType,price,tradePrice','safe'),
			array('saleType','in','range'=> array('retail','whole')),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签
	*/
	public function attributeLabels() {
		return array(
			'price' => '单价',
			'tradePrice'=>'大货价',
			'saleType'=>'促销类型',
		);
	}


	/**
	* 转成尾货
	* @param integer $productId 转成尾货的产品ID
	* @param array $singles 转成尾货的单品singleNumber
	*/
	public function changetail( $productId, $singles ){
		if( $this->saleType == 'whole' ){
			//整批销售两个价格一致
			$this->tradePrice = $this->price;
		}

		if( !$this->validate() ) {
			return false ;
		}

		if( empty( $productId ) || empty($singles) ){
			$this->addError('saleType','not data to save');
			return false;
		}

		$tail = new tbTail();
		$tail->attributes = $this->attributes;
		$tail->source = tbTail::SOURCE_GLASS;
		$tail->productId = $productId;
		if( $tail->saleType == 'whole' ){
			//整批销售两个价格一致
			$tail->tradePrice = $tail->price;
		}

		//使用事务处理，以确保这组数据全部成功插入
		$transaction = Yii::app()->db->beginTransaction();
		try {

			if( !$tail->save() ) {
				$this->addErrors( $tail->getErrors() );
				return false;
			}

			$tbTailSingle = new tbTailSingle();
			$tbTailSingle->tailId = $tail->tailId;

			foreach ( $singles as $val ){
				$_tailsingle = clone $tbTailSingle;
				$_tailsingle->singleNumber = $val;
				if( !$_tailsingle->save() ) {
					$this->addErrors( $_tailsingle->getErrors() );
					return false;
				}
			}

			//把呆滞报表的相关的产品改为已是尾货
			$c = new CDbCriteria;
			$c->compare('state',0);
			$c->compare('singleNumber',$singles);
			$m = tbGlassyList::model()->updateAll( array('state'=>'1'),$c );

			$transaction->commit();
			return true;
		} catch (Exception $e) {
			$transaction->rollback(); //如果操作失败, 数据回滚
			throw new CHttpException(503,$e);
			return false;
		}
	}


	/**
	* 编辑尾货
	* @param tbTail $model 编辑的尾货单品模型
	* @param array $singles 尾货的单品singleNumber
	*/
	public function edittail( $model, $singles ){
		if( empty( $model ) || empty($singles) || !is_array( $singles ) ){
			$this->addError('saleType','not data to save');
			return false;
		}

		$doCart = ( $model->saleType != $this->saleType  ) ? true : false;
		$model->attributes = $this->attributes;
		if( $model->saleType == 'whole' ){
			$model->tradePrice = $model->price;
		}

		//使用事务处理，以确保这组数据全部成功插入
		$transaction = Yii::app()->db->beginTransaction();
		try {
			if( !$model->save() ) {
				$this->addErrors( $model->getErrors() );
				return false;
			}

			foreach ( $model->single  as $_tailsingle ){
				if( !in_array( $_tailsingle->singleNumber, $singles ) ){
					$state = tbTailSingle::STATE_DEL;
					if( !$_tailsingle->save() ) {
						$this->addErrors( $_tailsingle->getErrors() );
						return false;
					}

				}
			}

			if( $doCart ){
				tbCart::model()->updateAll ( array('state'=>'1') ,' tailId = :tailId ',array(':tailId'=>$model->tailId) );
			}
			$transaction->commit();
			return true;
		} catch (Exception $e) {
			$transaction->rollback(); //如果操作失败, 数据回滚
			throw new CHttpException(503,$e);
			return false;
		}
	}


}