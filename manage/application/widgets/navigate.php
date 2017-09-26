<?php
class navigate extends CWidget {

	public function run() {
		$groups = $this->fetchMenusRoot();
// 		ob_start();
		foreach ( $groups as $item ) {
			$url = Yii::app()->createUrl('/default/index',array('id'=>$item['menuId']));
			$url = empty($item['url'])? $url : $item['url'];
			echo '<li>'.CHtml::link( $item['title'], $url ).'</li>';
		}
// 		ob_end_flush();
	}

	public function fetchMenusRoot() {
		$result = tbMenu::model()->findAll("parentId=0 and hidden=0");
		foreach ( $result as & $item ) {
			$item = $item->getAttributes();
		}
		return $result;
	}
}

// <ul class="clearfix head-nav list-inline list-unstyled">
// <li><a href="javascript:">首页</a></li>
// ....
// <li><a href="javascript:">网站配置</a></li>
// </ul>