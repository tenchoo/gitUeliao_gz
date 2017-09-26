<?php
/**
 * 地区信息
 * @author liang
 * @version 0.1
 * @package Controller
 */
class AreaController extends Controller {

	/**
	 * 全部地区信息 地域按等级显示
	 */
	public function actionIndex(){
		$this->data = tbArea::model()->getCache();
		$this->state = true;
		$this->showJson();
	}

	/**
	 * ajax 地域
	 * @param int $id 当前地域ID
	 */
	public function actionShow($id){
		$childArray  = array();
		$parentid = 0;
		$childData = '';
		if($id!=0){
			$area = tbArea::model()->find('areaId=:areaId',array(':areaId'=>$id));
			if(empty($area)){
				$this->message = Yii::t ( 'msg', 'No data' );
				$this->showJson();
			}
			$parentid = $area->parentid;
		}
		$childData=tbArea::model()->findAll('parentid=:parentid',array(':parentid'=>$id));

		if($childData){
			$childArray=CHtml::listData($childData,'areaId','title');
		}else{
			$childArray = array();
		}
		$areaArray['parent'] = $parentid?$parentid:'';//返回上级ID
		$areaArray['childs'] = $childArray;//返回子类

		$this->data = $areaArray;
		$this->state = true;
		$this->showJson();
	}

}