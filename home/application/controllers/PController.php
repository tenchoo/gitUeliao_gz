<?php
/**
 * 广告跳转中转页.
 * @author liang
 * @version 0.1
 * @package CController
 * @example
 *
 */
class PController extends CController {

	/**
	 * 广告跳转中转页,同时统计点击数量。
	 */
	public function actionIndex() {
		$id = Yii::app()->request->getQuery('id');
		if( $id>0 && is_numeric($id)){
			$ad = tbAd::model()->getPromoted( $id );
			if($ad){
				//更新点击数量
				$ad->updateCounters(array('clickNum'=>1),'adId='.$id );//自动叠加1

				//跳转到推广页面
				$this->redirect( $ad->link );
			}
		}
		$this->redirect( Yii::app()->request->hostInfo );
	}
}