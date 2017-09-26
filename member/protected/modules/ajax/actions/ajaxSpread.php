<?php
/**
 * 取得广告信息，传广告标识 mark 进行读取。是否生成JS?
 * @author liang
 * @version 0.1
 * @package CAction
 */
class ajaxSpread extends CAction {
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

	public $adtype = 1 ; //输出格式

	public function run() {
		$this->id = Yii::app()->request->getQuery('id'); //广告位ID
		$this->mark = Yii::app()->request->getQuery('mark');//广告位标识
		$this->adtype = Yii::app()->request->getQuery('adtype');//广告位标识

		if( !empty($this->id) ){
			$cacheName = 'ads_'.$this->id;
		}else{
			$cacheName = 'ads_'.$this->mark;
		}

		$data = Yii::app()->cache->get($cacheName);//获取缓存
		$f = Yii::app()->request->getQuery('f');//强刷新广告内容
		
		//如果缓存不存在，则直接读取数据库
		 if( $f === 'adflush' || empty($data) ){
			$data = $this->getAds();
			Yii::app()->cache->set($cacheName,$data,3600);
		}

		if( $this->adtype == 2 ){
			$data = str_replace('"','\"',$data);
			echo 'document.write("'.$data.'");';exit;
		}
		if( $data ){
			$json = new AjaxData(  true ,null, $data );
		}else{
			$json = new AjaxData( false, 'Not found record' );
		}

		echo $json->toJson();
		Yii::app()->end();
	}


	private function output( $data ){
		if( empty($data) || empty($data['ads'])) return ;
		if( count($data['ads'])>1 ){
			$tem = '<li><a href="%s" target="_blank" rel="nofollow"><img src="%s" width="%d" height="%d"  alt="%s" title="%s"/></a></li>';
		}else{
			$tem = '<a href="%s" target="_blank" rel="nofollow"><img src="%s" width="%d" height="%d"  alt="%s" title="%s"/></a>';
		}

		$api =  new ApiClient('www','service',false);
		$str = '';
		foreach ( $data['ads'] as $val ){
			//广告跳转中转页面，用于统计点击数量。
			$url = $api->createUrl('/p/index',array('id'=>$val['adId']));
			$val['image'] = Yii::app()->params['domain_images'].$val['image'];
			$str .= sprintf($tem,$url,$val['image'],$data['width'],$data['height'],$val['replaceText'],$val['description']);
		}
		return $str;
	}

	/**
	* 取得广告信息
	*/
	private function getAds(){
		$model = null;
		if( !empty($this->id) && is_numeric($this->id) ){
			$model = tbAdPosition::model()->findByPk( $this->id ,'t.state=0 and t.parentId > 0');
		}else if( !empty($this->mark) && preg_match('/^[a-zA-Z_0-9]+$/',$this->mark)) {
			$model = tbAdPosition::model()->findByAttributes( array('mark'=> $this->mark ) ,'t.state=0 and t.parentId > 0');
		}
		if( $model ){
			$data = $model->getAttributes( array('adPositionId','maxNum','height','width','title','mark'));
			$data['ads'] =  tbAd::model()->getAds( $model->adPositionId,$model->maxNum );
			$data = $this->output( $data );
			return $data;
		}
	}
}