<?php
/**
 * 产品详情页--相似产品推荐
 * 读取同一子分类下的最新产品
 * @author liang
 * @package CWidget
 * @version 0.1.1
 */
class SameProductList extends CWidget {
	public $productId;
	
	public function run(){
		ob_start();
		$data = Product::getSamelist( $this->productId );
		foreach ( $data as $val ){
			$image = $this->owner->imageUrl($val->mainPic,200,false);
			echo '<li>';
			echo '<a href="/product/detail/id/'.$val->productId.'"><img src="'.$image.'" width="200" height="200" alt="'.$val->title.'"></a>';
			echo '<a href="/product/detail/id/'.$val->productId.'" class="t">'.$val->title.'</a>';
			echo '<span class="price">&yen;'.$val->price.'</span>';
			echo '</li>';
		}
		ob_end_flush();
		
	}
	
}