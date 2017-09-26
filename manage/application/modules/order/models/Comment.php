<?php
/**
 * 产品反馈
 * @author liang
 * @version 0.1
 * @package CFormModel
 *
 */

class Comment extends CFormModel {

	public $reply;

	/**
	* Declares the validation rules.
	* The rules state that account and password are required,
	* and password needs to be authenticated.
	*/
	public function rules()	{
		return array(
			array('reply','required'),
			//array('')
			array('reply', "safe"),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签
	*/
	public function attributeLabels() {
		return array(
			'reply' => '解释',
		);
	}

	/**
	* 取得客户反馈信息列表
	* @param array $condition 查询列表条件
	*/
	public static function getList( $condition = array() ){
		$criteria = new CDbCriteria;
		$criteria->select = 'orderId';
		$criteria->order = ' t.createTime desc ';
		$criteria->distinct = true; //是否唯一查询
		if(!empty($condition)){
			foreach ($condition as $key=>$val){
				if( $val == '' ){
					continue ;
				}
				if( $key =='createTime1' ){
					$criteria->addCondition("t.createTime>'$val'");
				}else if( $key =='createTime2' ){
					$createTime2 = date("Y-m-d H:i:s",strtotime( $val )+86400 ) ; //包含选择的当天
					$criteria->addCondition("t.createTime<'$createTime2'");
				}else if( $key =='orderId' ){
					$criteria->compare('t.'.$key,$val,true);
				}else{
					$criteria->compare('t.'.$key,$val);
				}
			}
		}
		$pageSize = tbConfig::model()->get( 'page_size' );
		$criteria->compare('t.state','0');
		$tbComment = new tbComment();
		$criteria->order = ' t.createTime desc ';
		$model = new CActiveDataProvider($tbComment, array(
			'criteria'=>$criteria,
			'pagination'=>array('pageSize'=>$pageSize),
		));
		$data = $model->getData();
		$result = array();
		foreach ( $data as $key=>$val ){
			$result[$val->orderId]['orderId'] = $val->orderId;
		}

		if( !empty( $result ) ){
			$criteria = new CDbCriteria;
			$criteria->compare('t.orderId',array_keys($result));
			$criteria->compare('t.state','0');
			$info = $tbComment->with('product','member')->findAll( $criteria );
			foreach ( $info as $val ){
				$arr =  $val->attributes;
				$result[$val->orderId]['createTime'] = $val->createTime;
				if( $val->product ){
					$arr['title'] =  $val->product->title;
					$arr['serialNumber'] =  $val->product->serialNumber;
					$arr['mainPic'] =  $val->product->mainPic;
				}else{
					$arr['title'] = $arr['serialNumber'] = $arr['mainPic'] = '';
				}
				if( $val->member ){
					$result[$val->orderId]['nickName'] =  $val->member->nickName;
				}
				$result[$val->orderId]['products'][] = $arr ;
			}
		}
		$return['list'] = $result;
		$return['pages'] = $model->getPagination();
		return $return;
	}

}