<?php

/**
 * 帮助分类表模型
 *
 * @property integer	$adId			广告id
 * @property integer	$adPositionId	广告位ID
 * @property integer	$pageId			所属页面ID
 * @property integer	$state			状态：0正常，1下架,2已删除
 * @property integer	$listOrder		排序值,从小到大排序
 * @property integer	$price			广告价格
 * @property integer	$views			浏览数
 * @property integer	$clickNum		总点击数量
 * @property integer	$priceCycle		广告价格周期
 * @property timestamp	$createTime
 * @property timestamp	$updateTime
 * @property timestamp	$startTime		开始时间
 * @property timestamp	$endTime		结束时间
 * @property string		$customerTel	客户手机
 * @property string		$customerName	客户姓名
 * @property string		$title			广告标题
 * @property string		$replaceText	图片替换文本
 * @property string		$link			链接地址
 * @property string		$description	描述
 * @property string		$image			图片地址
 * @version 0.1
 * @package CActiveRecord
 */

class tbAd extends CActiveRecord {

	public $priceCycle;

	/**
	 * 构造数据库表模型单例方法
	 * @param system $className
	 * @return static
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}


	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{ad}}';
	}

	/**
	 * @return array 模型验证规则.
	 */
	public function rules()	{
		return array(
			array('adPositionId,title,link,image','required'),
			array('state','in','range'=>array(0,1,2)),
		//	array('link','url'),
			array('adPositionId,pageId,listOrder,views,clickNum', 'numerical','integerOnly'=>true),
			array('price', 'numerical'),
			array('customerTel,customerName,title,mark,replaceText,description,image,startTime,endTime,priceCycle','safe'),
		);
	}

	public function attributeLabels(){
		return array(
			'adPositionId'=>'广告位ID',
			'listOrder'=>'排序值',
			'price'=>'广告价格',
			'startTime'=>'开始时间',
			'endTime'=>'结束时间',
			'customerTel'=>'客户手机',
			'customerName'=>'客户姓名',
			'title'=>'广告标题',
			'replaceText'=>'图片替换文本',
			'link'=>'链接地址',
			'description'=>'描述',
			'image'=>'广告图片',
			'priceCycle'=>'广告价格周期',
		);

	}

	/**
	* 批量删除广告
	* @param array  $ids		 分类表PK
	* @param integer  $message	 提示信息
	*/
	public function del( $ids,&$message ){
		if( empty($ids) || !is_array($ids) ) return false;

		if( $this->updateByPk( $ids,array('state'=>'2'), 'state!=2') ){
			$message = null;
			return true;
		}

		return false;
	}

	/**
	* 批量下架广告
	* @param array  $ids		 分类表PK
	*/
	public function offShelf( $ids ){
		if( empty($ids) || !is_array($ids) ) return false;

		if( $this->updateByPk( $ids,array('state'=>'1'), 'state=0') ){
			return true;
		}
		return false;
	}


	/**
	* 根据广告位取得广告位列表
	* @param integer  $adPositionId
	*/
	public static function getList( $adPositionId,$pageSize = 2 ){
		if(!is_numeric($adPositionId) || $adPositionId<1 ) return ;

		$criteria = new CDbCriteria;
		$criteria->select = 'adId,state,startTime,endTime,title,image';
		$criteria->compare('t.adPositionId',$adPositionId);
		$criteria->compare('t.state',array(0,1));
		$criteria->order = 'listOrder asc,updateTime desc';

		$model = new CActiveDataProvider('tbAd',array(
					'criteria'=>$criteria,
					'pagination'=>array('pageSize'=>$pageSize,'pageVar'=>'page'),
				));
		$data = $model->getData();
		$return['list'] = array();
		$return['pages']= $model->getPagination();

		$now = date( 'Y-m-d H:i:s');
		foreach ( $data as $val ){
			$info = $val->getAttributes(array('adId','title','image'));
			$info['active'] = ( $val->state )?false:true;
			$info['stateTitle'] =  ( $val->state )?'已下架':'进行中';
			if( $val->state == '0'){
				if(  $val->startTime > $now ){
					$info['stateTitle'] = '未开始';
					$info['active'] = false;
				}
				else if( $val->endTime !='0000-00-00 00:00:00' && $val->endTime < $now ) {
					$info['stateTitle'] = '已过期';
					$info['active'] = false;
				}
			}

			if( $val->startTime !='0000-00-00 00:00:00' ){
				if( $val->endTime !='0000-00-00 00:00:00' ){
					$info['activeTime'] =  $val->startTime.' 至'. $val->endTime;
				}else{
					$info['activeTime'] =  $val->startTime.' 至 不限';
				}
			}else{
				if( $val->endTime !='0000-00-00 00:00:00' ){
					$info['activeTime'] = '- 至 '. $val->endTime;
				}else{
					$info['activeTime'] = '不限';
				}
			}
			array_push($return['list'],$info);
		}
		return $return;
	}


	/**
	* 广告价格周期
	*/
	public function cycles(){
		return array('天','星期','月','年','期');
	}






	/**
	 * 保存前的操作
	 */
	protected function beforeSave(){
		if($this->isNewRecord){
			$this->createTime = new CDbExpression('NOW()');
		}
		$this->updateTime = new CDbExpression('NOW()');
		return true;
	}


	/**
	* 要据广告位ID取得当前广告位正在进行中的广告个数。
	* @param integer $adPositionId
	*/
	public function getNum( $adPositionId ){
		$criteria = $this->isPromoted();
		$criteria->compare('t.adPositionId',$adPositionId);
		$count = $this->count( $criteria );
		return $count;
	}

	/**
	* 要据广告位ID取得当前广告位正在进行中的内容。
	* @param integer $adPositionId 广告位ID
	* @param integer $limit		显示的广告个数
	*/
	public function getAds( $adPositionId,$limit ){
		$criteria = $this->isPromoted();
		$criteria->compare('t.adPositionId',$adPositionId);
		$criteria->limit = $limit;
		$ads = $this->findAll( $criteria );
		$ads = array_map(function ($i){
					//查看次数增1
					$i->views = $i->views+1;
					$i->save();
					return $i->getAttributes(array('adId','mark','title','image','link','replaceText','description'));
				},$ads);
		return $ads;
	}

	/**
	* 正在进行中的广告的搜索条件
	* return CDbCriteria
	*/
	private function isPromoted(){
		$criteria = new CDbCriteria;
		$criteria->compare('t.state','0');
		$now = date( 'Y-m-d H:i:s');
		$criteria->addCondition("startTime <'$now'");
		$criteria->addCondition("endTime ='0000-00-00 00:00:00' or endTime >'$now'");
		$criteria->order = 'listOrder asc,updateTime desc';

		return $criteria;
	}

	/**
	* 根据广告ID取得正在进行中的广告详情。
	*/
	public function getPromoted( $id ){
		$criteria = $this->isPromoted();
		$criteria->compare('t.adId',$id);
		$ad = $this->find( $criteria );
		return $ad;
	}


}
?>