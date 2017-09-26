<?php
class widgetSubNav extends CWidget {
	public $urlMap=array();
	
	public function run() {
		echo '<ul class="nav nav-tabs">';
		foreach( $this->urlMap as $key => $item ) {
			if( $this->owner->getRoute() == substr($item,1) ) {
				$optional = array('class'=>'active');
			}
			else {
				$optional = array();
			}

			$link = CHtml::link($key, $this->owner->createUrl($item));
			echo CHtml::tag('li',$optional,$link);
		}
		echo "</ul><br>";
	}
}