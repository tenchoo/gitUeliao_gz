<?php
/**
 * 浏览过的商品
 * 读取最近浏览过8款产品
 * @author liang
 * @package CWidget
 * @version 0.1.1
 */
class ViewProductList extends CWidget {

	public function run(){
		ob_start();
		$this->showList();
		ob_end_flush();
	}

	private function showList(){
		$memberId = Yii::app()->user->id;
		if( !$memberId ) return ;

		$model = tbProductView::model()->findAll(	array(
			'select'=>'distinct productId ',
			'condition'=>'memberId = :memberId',
			'params'=>array(':memberId'=>$memberId),
			'limit'=>8,
			'order' =>'createTime desc',
			));
		if( !$model ) return ;
		foreach ( $model as $val ){
			$ids[] = $val->productId;
		}

		$data = tbProduct::model()->findAllByPk( $ids );

		$home = 'http://www'.DOMAIN;
		foreach ( $data as $val ){
			$url = $home.'/product/detail-'.$val->productId.'.html';
			echo '<li> ';
			echo '<div class="commodity-pic"><a href="'.$url.'" target="_blank"><img src="'.Yii::app()->params['domain_images'],$val->mainPic.'_200" width="160" height="160" alt="'.$val->title.'"></a></div>';
			echo '<span class="text-primary"><a href="'.$url.'">【'.$val->serialNumber.'】'.$val->title.'</span>';
			echo '<strong class="text-warning">&yen;'.$val->price.'</strong>';
			echo '</li> ';
		}
	}

}