<?php
/**
 * 后台管理面包屑物件
 * @author yagas
 * @package CWidget
 * @version 0.1.1
 */
class current extends CWidget {

	/**
	 * 执行物件进行渲染
	 * @see CWidget::run()
	 */
	public function run() {
		ob_start();
		$buff    = array();
		$title = '';
		$route   = Yii::app()->name . ':/' . $this->owner->getRoute();
		if( $route == 'manage:/default/index' ){
			$id = (int)Yii::app()->request->getQuery('id');
			array_unshift( $buff, '<li>default page</li>' );

			$this->find( $id , $buff );
		}else{
			$current = tbMenu::model()->find("route=:route",array(':route'=>$route));
			if( $current ) {
				if( preg_match( "/^custom/", $this->owner->getPageTitle() ) ) {
					$title = substr( $this->owner->getPageTitle(), 8 );
					array_unshift( $buff, '<li>'.$title.'</li>' );
				}
				else {
					$title = $current->title;
					array_unshift( $buff, '<li>'.$current->title.'</li>' );
				}
				$this->find( $current->parentId, $buff );
			}
			else {
				//$title = mb_substr( $this->owner->getPageTitle(), 8 );
				$title = $this->owner->getPageTitle();
			}

		}

		if(isset($this->owner->currentTitle)){
			$title =  $this->owner->currentTitle;
		}

		echo '<ol class="breadcrumb">';
		echo implode('', $buff);
		echo '</ol>';
		echo "<h2 class=\"h3\">{$title}</h2>";
		ob_end_flush();
	}

	/**
	 * 获取菜单数据
	 * @param integer $id 菜单编号
	 * @param array $buff 数据
	 */
	private function find( $id, & $buff ) {
		$result = tbMenu::model()->find( "menuId=:id", array(":id"=>$id) );

		if( !is_null($result) ) {
			array_unshift( $buff, '<li>'.CHtml::link($result->title,$result->url).'</li>' );
			if( $result['parentId'] !== 0 ) {
				$this->find( $result['parentId'], $buff );
			}
		}
	}
}

// <ol class="breadcrumb">
// <li><a href="javascript:">首页</a></li>
// <li><a href="javascript:">路径1</a></li>
// <li><a href="javascript:">路径2</a></li>
// <li>页面标题</li>
// </ol>