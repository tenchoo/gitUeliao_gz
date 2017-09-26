<?php
/**
 * 订单表
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$commentId
 * @property integer	$orderId			订单ID
 * @property integer	$productId 			产品ID
 * @property integer	$tailId 			尾货产品ID
 * @property integer	$orderProductId		订单产品表ID
 * @property integer	$memberId			客户ID
 * @property integer	$userId				回复者userId
 * @property timestamp	$createTime			评论时间
 * @property timestamp	$updateTime
 * @property timestamp	$replyTime			回复时间
 * @property string		$specifiaction		产品规格
 * @property string		$content			评论内容
 * @property string		$reply				评论回复
 *
 */

 class tbComment extends CActiveRecord {

	public $title;

	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{order_comment}}";
	}

	public function relations(){
		return array(
			'product'=>array(self::BELONGS_TO,'tbOrderProduct','orderProductId','select'=>'title,serialNumber,mainPic'),
			'usericon'=>array(self::BELONGS_TO,'tbProfile','memberId','select'=>'icon'),
			'member'=>array(self::BELONGS_TO,'tbMember','memberId','select'=>'nickName'),
		);
	}

	public function rules() {
		return array(
			array('orderId,productId,memberId,specifiaction,content','required'),
			array('content','length','min'=>'1'),
			array('orderId,productId,memberId,userId', "numerical","integerOnly"=>true),
			array('specifiaction,content,reply', "safe"),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'orderId' => '订单ID',
			'productId' => '产品ID',
			'memberId' => '客户ID',
			'userId' => '回复者userId',
			'specifiaction' => '产品规格',
			'content' => '反馈内容',
			'reply' => '解释',
		);
	}

	/**
	 * 保存前的操作
	 */
	protected function beforeSave(){
		if($this->isNewRecord){
			$this->createTime = new CDbExpression('NOW()');
			$this->userId = 0;
			$this->reply = '';
		}else{
			$this->updateTime = new CDbExpression('NOW()');
		}
		return true;
	}

	/**
	 * 客户评论列表---管理后台
	 * @param  array $condition 查找条件
	 * @param  integer $pageSize 每页显示条数
	 */
	public function getList( $condition = array() ,$pageSize = 10 ){
		$criteria = new CDbCriteria;

		if(!empty($condition)){
			foreach ($condition as $key=>$val){
				if( $val == '' ){
					continue ;
				}
				$criteria->compare($key,$val);
			}
		}
		$criteria->with = 'product';
		$criteria->order = ' t.createTime desc ';
		$criteria->compare('t.state','0');
	//	$criteria->join = 'left join {{order}} t2 on ( t.orderId=t2.orderId )'; //连接表
		$criteria->join = 'left join {{order}} t2 on ( t.orderId=t2.orderId )'; //连接表
		$model = new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array('pageSize'=>$pageSize),
		));

		$result = array();
		$data = $model->getData();
		foreach ( $data as $key=>$val ){
			$result[$key] = $val->attributes;
			if( $val->product ){
				$result[$key]['title'] =  $val->product->title;
				$result[$key]['serialNumber'] =  $val->product->serialNumber;
				$result[$key]['mainPic'] =  $val->product->mainPic;
			}
		}
		$return['list'] = $result;
		$return['pages'] = $model->getPagination();
		return $return;

	}

	/**
	* 产品评论列表--普通产品
	*
	*/
	public function productComment( $productId,$page,$pageSize = 1 ){
		$condition = array( 'productId'=>$productId,'tailId'=>'0' );
		return $this->commentList( $condition,$page,$pageSize );
	}


	/**
	* 产品评论列表
	* @param  array $condition 搜索条件
	*/
	public function commentList( $condition,$page,$pageSize = 1 ){
		$criteria = new CDbCriteria;
		if( is_array($condition)){
			foreach ( $condition as $key=>$val){
				$criteria->compare( $key,$val );
			}
		}

		$criteria->compare('t.state','0');
		$criteria->order = 'createTime desc ';
		$criteria->limit =  $pageSize;
		$criteria->offset =  ( $page-1 )*$pageSize;
		$model = $this->with('usericon','member')->findAll($criteria);
		$result = array();
		foreach  ($model as $key =>$val){
			$result[$key] = $val->getAttributes( array('createTime','content','replyTime','reply','specifiaction') );
			if($val->member){
				$result[$key]['nickName'] = tbMember::half_replace( $val->member->nickName );
			}
			if($val->usericon){
				$result[$key]['icon'] = $val->usericon->icon;
			}
		}
		return $result;
	}


	/**
	* 计算评论总数
	* @param integer $productId 产品ID
	* @param integer $tailId 	尾货产品ID
	*/
	public function commentCount( $productId,$tailId = 0 ){
		$criteria = new CDbCriteria;
		$criteria->compare('t.state','0');
		if( $tailId >0 ){
			$criteria->compare('t.tailId',$tailId);
		}else{
			$criteria->compare('t.productId',$productId);
			$criteria->compare('t.tailId',0);
		}

		$count = $this->count( $criteria );
		return $count;
	}
}