<?php
/**
 * 顶部导航栏购物车物件
 * @author yagasx
 *
 */
class CartTopbar extends CWidget {
	
	public $cartDetail = "Cart/detail";
	public $cartDelete = "Cart/remove";
	
	public function run() {
		if( Yii::app()->user->getIsGuest() )
			return "";
		
		$data       = $this->FetchData();
		$totalPrice = $this->statistic( $data );
		
		if( !is_null( $data ) ) {
			$code = "<ul class=\"top-cart-list list-unstyled\">";
			reset($data);
			$api = new ApiClient('member');
			do {
				$cur = current( $data );
				extract( $cur );
				$title = htmlentities( $title );
				$linkDetail = $api->createUrl('product/detail',array('productId'=>$goodId));
				$linkDelete = $api->createUrl('cart/delete',array('productId'=>$goodId));
				
				$code .= "<li class=\"clearfix\">";
				$code .= "<a href=\"{$linkDetail}\" class=\"pull-left\"><img src=\"{$thumb}\" alt=\"{$title}\" width=\"50\" height=\"50\"/></a>";
				$code .= "<div class=\"pull-right\">";
				$code .= "<span class=\"price\">￥{$price}×{$count}</span>";
				$code .= "<a href=\"{$linkDelete}\" class=\"del\">删除</a>";
				$code .= "</div><a href=\"{$linkDetail}\" class=\"t\">{$title}</a></li>";
			}	
			while( next($data) );
			
			$code .= "</ul>";
			$code .= "<div class=\"clearfix top-cart-count\">";
			$code .= "<button class=\"top-cart-btn pull-right\">去结算</button>共计：<span class=\"price\">￥{$totalPrice}</span>";
			$code .= "</div>";
			echo $code;
		}
	}
	
	/**
	 * 获取购物车数据
	 * @return multitype:multitype:string number
	 */
	protected function FetchData() {
		return array(
			array("goodId"=>"1", "title"=>"诺基亚G手机（灰色）TD-SCDMA/GSM", "price"=>1230.00, "count"=>1, "thumb"=>""),
			array("goodId"=>"2", "title"=>"华为H手机（白色）TD-SCDMA/GSM", "price"=>999.00, "count"=>3, "thumb"=>"")
		);
	}
	
	/**
	 * 购物车价格统计
	 * @param array $data
	 * @return string
	 */
	protected function statistic( $data ) {
		$price = 0;
		if( $data ) {
			foreach ( $data as $item ) {
				$price += $item['price'] * $item['count'];
			}
		}
		return sprintf( "%0.2f", $price );
	}
}