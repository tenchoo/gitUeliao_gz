<?php
/**
 * 结算单月对账单
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$id
 * @property integer	$month				结算月份
 * @property integer	$memberId			客户ID
 * @property integer	$userId				业务员memberId
 * @property integer	$isDone				是否财务结算完成
 * @property number		$receipt			实际收款金额
 * @property number		$payments			总结算金额
 * @property date		$createTime			生成时间
 *
 */

 class tbOrderSettlementMonth extends CActiveRecord {

	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{order_settlement_month}}";
	}
	
	public function relations(){
		return array(
			'member'=>array(self::BELONGS_TO,'tbProfileDetail','memberId','select'=>'companyname'),
		);
	}


	public function rules() {
		return array(
			array('month,memberId,userId,receipt,payments','required'),
			array('isDone','in','range'=>array(0,1)),
			array('memberId', "numerical","integerOnly"=>true),
			array('payments,receipt', "numerical"),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'month' => '结算月份',
			'memberId' => '客户ID',
			'userId'=>'业务员memberId',
			'payments'=>'总结算金额',
		);
	}

	/**
	 * 保存前的操作
	 */
	protected function beforeSave(){
		if($this->isNewRecord){
			$this->createTime = new CDbExpression('NOW()');
		}
		return true;
	}
	
	/**
	* 取得列表
	* @param array $condition 查询列表条件
	*/
	public  function search( $condition = array() ){
		$criteria = new CDbCriteria;
		$criteria->order = ' t.createTime desc ';
		if(!empty($condition)){
			foreach ($condition as $key=>$val){
				if( $val === '' ){
					continue ;
				}
				if( $key =='createTime1' ){
					$criteria->addCondition("t.createTime>'$val'");
				}else if( $key =='createTime2' ){
					$createTime2 = date("Y-m-d H:i:s",strtotime( $val )+86400 ) ; //包含选择的当天
					$criteria->addCondition("t.createTime<'$createTime2'");
				}else{
					$criteria->compare('t.'.$key,$val);
				}
			}
		}

		$pageSize = tbConfig::model()->get( 'page_size' );
		$criteria->order = ' t.createTime desc ';
		$model = new CActiveDataProvider( $this, array(
			'criteria'=>$criteria,
			'pagination'=>array('pageSize'=>$pageSize,'pageVar'=>'page'),
		));

		$return['list'] = $model->getData();
		$return['pages'] = $model->getPagination();
		return $return;
	}

}