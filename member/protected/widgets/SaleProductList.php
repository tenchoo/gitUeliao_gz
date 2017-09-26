<?php
/**
 * 购物车页面---购买过此商品的还购了
 * 读取最近卖出的10款产品
 * @author liang
 * @package CWidget
 * @version 0.1.1
 */
class SaleProductList extends CWidget {

	public function run(){
		ob_start();

		$sql = "SELECT distinct productId FROM {{order_product}} where 1 order by orderProductId desc limit 10";
		$result = Yii::app()->db->createCommand($sql)->queryAll();
		foreach ( $result as $val ){
			$ids[] = $val['productId'];
		}

		if( !empty( $ids ) ){
			$data = tbProduct::model()->findAllByPk( $ids,'state = 0' );
			$home = 'http://www'.DOMAIN;
			foreach ( $data as $val ){
				$url = $home.'/product/detail-'.$val->productId.'.html';
				echo '<li> ';
				echo '<div class="c-img"><a href="'.$url.'" target="_blank"><img src="'.Yii::app()->params['domain_images'],$val->mainPic.'_200" width="200" height="200" alt="'.$val->title.'"></a></div>';
				echo '<p class="price"><strong class="text-warning">&yen;'.$val->price.'</strong></p>';
				echo '<p class="t"><a href="'.$url.'">【'.$val->serialNumber.'】'.$val->title.'</a></p>';
				echo '</li> ';

			}
		}
		ob_end_flush();
	}

}