<?php
/**
 * ajax 地域
 * @author morven
 * @version 0.1
 * @param int $areaid 当前地域ID
 * @package CAction
 */
class ajaxDynamiccitys extends CAction{
	public function run() {
		$childArray  = array();
		$parentid = 0;
		$childData = '';
		$areaId = (int)Yii::app()->request->getParam('areaid',0);
		if($areaId!=0){		
			$area = tbArea::model()->find('areaId=:areaId',array(':areaId'=>$areaId));			
			if(empty($area)){
				$msg= Yii::t ( 'msg', 'No data' );
				$this->error1('areaId',$msg);
			}
			$parentid = $area->parentid;
		}
		$childData=tbArea::model()->findAll('parentid=:parentid',array(':parentid'=>$areaId));
		
		if($childData){		
			$childArray=CHtml::listData($childData,'areaId','title');
		}else{
			$childArray = array();
		}
		$areaArray['parent'] = $parentid?$parentid:'';//返回上级ID
		$areaArray['childs'] = $childArray;//返回子类
		
		$json=new AjaxData(true,null,$areaArray);
		echo $json->toJson();
		Yii::app()->end();
	}
}