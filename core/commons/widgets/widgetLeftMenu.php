<?php
/**
 * 左侧菜单物件
 * @author yagas
 * @version 0.1
 * @package CWidget
 */
class widgetLeftMenu extends CWidget {
	
	public $navdata = 'memberNav.data';
	/**
	 * 加载左侧导航菜单数据文件
	 * @param string $controllerName 控制器名称
	 * @throws CException
	 * @return multitype:NULL multitype: string
	 */
	protected function _loadData() {
		$filePath = Yii::getPathOfAlias('application.data').DS.'memberNav.data';
		if( !file_exists($filePath) ) {
			throw new CException("widget controller not found menu package: ${filePath}");
		}
		$data = require $filePath;
		return $data;
	}
	
	/**
	 * 提交当前路由
	 * @return string
	 */
	private function getRoute() {
		$route = $this->owner->id;
		$route .= '/' . $this->owner->action->id;
		return strtolower($route);
	}
	
	/**
	 * 渲染物件
	 * @see CWidget::run()
	 */
	public function run() {
		$controllerName = strtolower( $this->owner->id );
		$data           = $this->_loadData();
		$code           = '<div class="pull-left list-unstyled frame-menu">';
		foreach ( $data as $key => $val ) {
			$code .= '<h3 class="active"><span><i></i></span>'.$key.'</h3>';
			$code .= '<ul class="list-unstyled">';
			foreach ( $val as $item ) {
				$options = array();
				list($subtitle,$subroute) = explode(':',$item);
				
				if($this->getRoute() === $subroute) {
					$options['class'] = 'active';
				}
				
				if(empty($subroute)) {
					$subroute = "javascript:;";
				}
				$link = CHtml::link( $subtitle, Yii::app()->urlManager->createUrl($subroute) );
				$code .= CHtml::tag( 'li', $options, $link );
			}
			$code .= '</ul>';
		}
		$code .= "</div><script>seajs.use('app/member/frame/js/menu.js');</script>";
		echo $code;
	}
}