<?php
/**
 * Created by PhpStorm.
 * User: yagas
 * Date: 2015/11/27
 * Time: 14:08
 */

class ProductModel {

	 /**
	 * 商品可售数量--可销售量不计算样品仓和损耗仓的库存
	 * 商品可售数量 = 库存-锁定销售产品数量-安全库存
     * @param string $singleNumber 产品单品编码
	 * @param boolean $isTail 是否尾货商品,尾货商品的可售数量，跟正常产品对比，不减去安全库存
     * @return integer
     */
    public static function total( $singleNumber, $isTail = false, & $isLower=false ) {
		if( !$isTail && $f = self::isTail( $singleNumber ) ){
			return 0;
		}

        //库存
        $canSell = tbWarehouseProduct::model()->singleSaleCount( $singleNumber );
		if( $canSell > 0 ){
			//锁定产品数量
			$lockTotal = tbStorageLock::model()->singleCount( $singleNumber );

			/** 商品可售量=商品总库存 - 商品锁定量 */
			$canSell = bcsub( $canSell, $lockTotal, 1);
		}

		if( !$isTail ){
			//安全库存,先不减安全库存
			$safetyTotal = tbProductStock::model()->findByAttributes(array('singleNumber'=>$singleNumber));
			if( $safetyTotal ){
				if( $canSell < $safetyTotal->safetyStock ){
					$_buy  = bcsub( $safetyTotal->safetyStock,$canSell,1);
					self::addPurchaseQueue($singleNumber, $_buy );
				}
			}

			/* $safetyTotal = ( $safetyTotal )?$safetyTotal->safetyStock:0;

			//是否进行低安装库存采购
			$isLower = $canSell < $safetyTotal;
			if( $isLower ) {
				self::addPurchaseQueue($singleNumber, $safetyTotal-$canSell);
				$canSell = 0;
			}else{
				// 再减去安全库存
				$canSell = bcsub($canSell, $safetyTotal, 2);
			} */
		}

		if( $canSell < 0 ){
			$canSell = 0;
		}
        return $canSell;
    }



	/**
	* 是否尾货销售
	* @param string $singleNumber 产品单品编码
    * @return boolean
	*/
	public static function isTail( $singleNumber ) {
		if( empty( $singleNumber ) ) return false;

		$criteria = new CDbCriteria;
		$criteria->select = 't.singleNumber';
		$criteria->compare( 't.singleNumber',$singleNumber );
		$criteria->addCondition(" exists (select null from {{tail}} tail where tail.state = 'selling' and tail.tailId = t.tailId )");
		return tbTailSingle::model()->exists( $criteria );
	}

    /**
     * 添加低安全库存采购
     * @param $productCode 产品单品编码
     * @return bool
     */
    private static function addPurchaseQueue( $productCode, $quantity ) {
		$color = ZOrderHelper::getColor ( $productCode );
		$trans = Yii::app ()->getDb ()->beginTransaction ();
		$result = tbRequestlower::appendRequest ( $productCode, $color, $quantity );
		if ($result) {
			$trans->commit ();
		} else {
			$trans->rollback ();
		}
		return $result;
    }
}