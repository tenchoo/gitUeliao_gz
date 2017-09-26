<?php
/**
 * 取得广告信息，传广告标识 mark 进行读取。批量读取
 * @author liang
 * @version 0.1
 * @package model
 */
class Spread {

	/**
	* 取得广告信息
	*/
	public static function getAds( $arr,$type = 'id' ){
		if( empty($arr) ) return;
		$cacheName = 'ads_'.strtr($arr,',','_');

		$f = Yii::app()->request->getQuery('f');//强刷新广告内容
		if( $f === 'adflush' ){
			Yii::app()->cache->set($cacheName,null);
		}

		$data = Yii::app()->cache->get($cacheName);//获取缓存
		//如果缓存不存在，则直接读取数据库
		if( !empty( $data ) ){
			return $data;
		}

		$c = new CDbCriteria;
		$c->compare('t.state','0');
		$c->addCondition('t.parentId > 0');

		$arr = explode(',' ,$arr );
		if( $type == 'id' ){
			$c->compare('t.adPositionId',$arr);
		}else if( $type == 'mark' ) {
			$c->compare('t.mark',$arr);
		}else{
			return null;
		}

		$model = tbAdPosition::model()->findAll( $c );
		if(!$model ) return null;

		$data = array();
		$adModel = new tbAd();
		foreach( $model as $val ){
			$data[$val->adPositionId] = array(
											'id'=>$val->adPositionId,
											'mark'=>$val->mark,
											'title'=>$val->title,
											'maxNum'=>$val->maxNum,
										);
			
			$ads =  $adModel->getAds( $val->adPositionId,$val->maxNum );
			foreach ( $ads as &$ad ){				
				$arr = explode(':',$ad['link']);
				if( count($arr) != 2 ) continue;
				
				$ad['type'] = $arr['0'];
				$ad['value'] = $arr['1'];
				
				unset($ad['adId'],$ad['mark'],$ad['link']);
				$ad['image'] = Yii::app()->params['domain_images'].$ad['image'];
			}
			$data[$val->adPositionId]['ads'] = $ads;
		}

		$data = array_values($data);

		Yii::app()->cache->set($cacheName,$data,3600);
		return $data;
	}


}