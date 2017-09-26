<?php
/**
 * ajax 购物车
 * @author liang
 * @version 0.1
 * @package CAction
 */
class ajaxCart extends CAction{

	public function run() {
		if( Yii::app()->user->getIsGuest() ) {
			$message = Yii::t('user','You do not log in or log out');
			$json=new AjaxData(false,$message);
			echo $json->toJson();
			Yii::app()->end();
		}

		$data = array();
		$cart = new tbCart();
		$list = $cart->allCartLists();

		if( empty( $list ) ){
			$totalPrice = 0;
			goto end;
		}

		$priceType = 0;
		$userType = Yii::app()->user->getState('usertype');
		if( $userType == tbMember::UTYPE_MEMBER ){
			$priceType = tbMember::model()->getPriceType( Yii::app()->user->id );
		}

		$total = array();
		foreach ( $list as $val ){
			$price = ($priceType)?$val['tradePrice']:$val['price'];

			$data[] = array(
						'cartId'=>$val['cartId'],
						'productId'=>$val['productId'],
						'title'=>'【'.$val['serialNumber'].'】'.$val['title'],
						'mainPic'=>Yii::app()->params['domain_images'] .$val['mainPic'] . '_100',
						'relation'=>$val['relation'],
						'price'=> Order::priceFormat( $price ),
						'num'=> Order::quantityFormat( $val['num'] ),
					);
			$total[] = $val['num']*$price;
		}

		$totalPrice = array_sum ( $total );

		end:

		$result = array(
					'totalProduct'=>count($data),
					'totalPrice'=>Order::priceFormat( $totalPrice ),
					'detail'=>$data
					);
		$json=new AjaxData(true,null,$result);
		echo $json->toJson();
		Yii::app()->end();
	}

}