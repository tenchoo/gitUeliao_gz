<?php
/**
 * 广告模板物件--取得广告信息，并输出广告内容。id和mark选一传值，默认先选id.
 * @author liang
 * @version 0.1
 * @package CBasePager
 * @example
 *
 */
class AdWidget extends CWidget {

	/**
	 * 广告位Id
	 * @var int
	 */
	public $id;

	/**
	 * 广告位标识
	 * @var string
	 */
	public $mark;

	public function run() {
		$data = $this->getAds();
		$this->output( $data );
	}

	private function output( $data ){
		if( empty($data) || empty($data['ads'])) return ;
		if( count($data['ads'])>1 ){
			$tem = '<li><a href="%s" target="_blank"><img src="%s" width="%d" height="%d"  alt="%s"/></a></li>';
		}else{
			$tem = '<a href="%s" target="_blank"><img src="%s" width="%d" height="%d"  alt="%s"/></a>';
		}

		$url = $this->owner->homeUrl.'/p/index?id=';
		foreach ( $data['ads'] as $val ){
			//广告跳转中转页面，用于统计点击数量。
			$url = $url.$val['adId'];
			$val['image'] = $this->owner->imageUrl($val['image'],null,false);
			printf($tem,$url,$val['image'],$data['width'],$data['height'],$val['replaceText']);
		}
	}

	/**
	* 取得广告信息
	*/
	private function getAds(){
		$model = null;
		if( !empty($this->id) && is_numeric($this->id) ){
			$model = tbAdPosition::model()->findByPk( $this->id ,'t.state=0 and t.parentId > 0');
		}else if( !empty($this->mark) ) {
			$model = tbAdPosition::model()->findByAttributes( array('mark'=> $this->mark ) ,'t.state=0 and t.parentId > 0');
		}
		if( $model ){
			$data = $model->getAttributes( array('adPositionId','maxNum','height','width','title','mark'));
			$data['ads'] =  tbAd::model()->getAds( $model->adPositionId,$model->maxNum );
			return $data;
		}
	}
}