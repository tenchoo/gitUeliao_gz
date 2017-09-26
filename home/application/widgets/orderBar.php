<?php
/**
 * 平台产品列表页面商品排序仪表盘物件
 * @author yagas
 * @package CWidget
 * @version 0.1
 */
class orderBar extends CWidget {
	
	/**
	 * 地址栏参数列表
	 * @var array
	 */
	private $_params;
	
	public function run() {
		$this->params();
		$order = Yii::app()->request->getQuery('order');
		
		if( is_null($order) ) {
			$order = 'rankingdo';
		}
		
		$field = substr( $order, 0, -2 );
		$do    = substr( $order, -2 );
		
		//综合排序
		if( $field == 'ranking' ) {
			$options = array('class'=>'default active');
			$order   = strtolower($do)=='do'? 'rankingup':'rankingdo';
		}
		else {
			$options = array('class'=>'default');
			$order = 'rankingdo';
		}
		echo $this->createLink( '综合排序', $order, $options, 'ico-empty' );
		
		//价格排序
		$options = array('class'=>'price');
		$order   = 'pricedo';
		$ico     = 'ico-up';
		if( $field == 'price' ) {
			$options['class'] .= ' active';
			if( $do=='do' ) {
				$order = 'priceup';
				$ico   = 'ico-down';
			}
		}
		echo $this->createLink( '价格', $order, $options, $ico );
		
		//销量排序
		$options = array('class'=>'sales');
		$order   = 'salesdo';
		$ico     = 'ico-up';
		if( $field == 'sales' ) {
			$options['class'] .= ' active';
			if( $do=='do' ) {
				$order = 'salesup';
				$ico   = 'ico-down';
			}
		}
		echo $this->createLink( '销量', $order, $options, $ico );
		
		//上架时间排序
		$options = array('class'=>'time');
		$order   = 'publishdo';
		$ico     = 'ico-up';
		if( $field == 'publish' ) {
			$options['class'] .= ' active';
			if( $do=='do' ) {
				$order = 'publishup';
				$ico   = 'ico-down';
			}
		}
		echo $this->createLink( '上架时间', $order, $options, $ico );
	}
	
	/**
	 * 提取URL链接参数列表
	 */
	private function params() {
		$url        = Yii::app()->request->url;
		$parse      = parse_url( $url );
		
		if( !isset($parse['query']) ) {
			$this->_params = array();
		}
		else {
			parse_str( $parse['query'], $this->_params );
		}
	}
	
	/**
	 * 创建链接
	 * @param string $title    链接标题
	 * @param string $order    排序关键字
	 * @param array  $options  链接样式
	 * @param string $ico      箭头图标
	 */
	private function createLink( $title, $order, $options=array(),$ico='' ) {
		$title .= '<span class="ico '.$ico.'"></span>';
		$params = $this->_params;
		$params['order'] = $order;
		
		if( $order == 'rankingdo' ) {
			unset( $params['order'] );
		}
		
		$url = $this->owner->createUrl( $this->owner->getRoute(), $params );
		$link = CHtml::link( $title, $url, $options );
		return $link;
	}
}

/**
<a href="<?php $this->orderHelper(null);?>" class="default active">综合排序<span class="ico ico-empty"></span></a>
<a href="<?php $this->orderHelper('pricedo');?>" class="price">价格<span class="ico ico-up"></span></a>
<a href="<?php $this->orderHelper('salesVolumedo');?>" class="sales">销量<span class="ico ico-down"></span></a>
<a href="<?php $this->orderHelper('publishTimedo');?>" class="time">上架时间<span class="ico ico-down"></span></a>
*/