<?php
/**
 * 行业分类
 * @author liang
 * @version 0.1
 * @package Controller
 */
class CategoryController extends Controller {

	/**
	 * ajax 行业分类,一次性返回所有分类
	 */
	public function actionIndex(){
		$this->data = tbCategory::model()->getChildrens( 0, true );
		$this->state = true;
		$this->showJson();
	}


	/**
	* 类目信息
	* @param integer $id 类目ID
	* @param string $event
	*/
	public function actionShow($id,$event=''){

		$func  = "category_".$event;
		if( method_exists($this, $func) ) {
			$this->$func( $id );
		}

		$this->notFound();
	}


	/**
	 * 获取类目属性信息
	 * @param integer $categoryId 类目编号
	 */
	private function category_propertys( $categoryId ) {

		$criteria        = new CDbCriteria();
		$criteria->order = "listOrder ASC";
		$propertys = tbAttribute::model()->fetchAttributes($categoryId, null);

		if( !$propertys ) {
			$this->message = Yii::t('filter','Not found propertys');
			$this->showJson();
		}

		$resultData = array();
		foreach( $propertys as $item ) {
			$row   = array('id'=>$item['attributeId'],'title'=>$item['title'],'value'=>$this->formatValue($item['attrValue']));
			array_push($resultData,$row);
		}

		$this->state = true;
		$this->data = $resultData;
		$this->showJson();
	}

	/**
	 * 格式化属性值
	 */
	private function formatValue($values) {
		$lists=[];
		foreach($values as $key=>$val) {
			$row = ['key'=>$key, 'value'=>$val];
			array_push($lists, $row);
		}
		return $lists;
	}
}