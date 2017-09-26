<?php
/**
 * 结算单
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$settlementId
 * @property integer	$orderId			订单ID
 * @property integer	$type				单据来源：０前台业务员生成，１后台生成
 * @property integer	$originatorId		申请人memberId/userId
 * @property integer	$state				状态：0未发货，1已发货
 * @property integer	$warehouseId		发货仓库
 * @property integer	$freight			物流费
 * @property integer	$productPayments	结算单商品总金额，不包含物流费
 * @property date		$createTime			申请时间
 *
 */

 class tbOrderSettlement extends CActiveRecord {

	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{order_settlement}}";
	}

	public function relations(){
		return array(
			'order'=>array(self::BELONGS_TO,'tbOrder','orderId'),
			'detail'=>array(self::HAS_MANY,'tbOrderSettlementDetail','settlementId'),
		);
	}

	public function rules() {
		return array(
			array('orderId,warehouseId','required'),
			array('type,state','in','range'=>array(0,1)),
			array('orderId,warehouseId', "numerical","integerOnly"=>true),
			array('freight,productPayments', "numerical"),
			array('orderId', "unique"),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'orderId' => '订单ID',
			'warehouseId' => '仓库ID',
			'freight'=>'物流费',
			'productPayments'=>'商品总金额',
		);
	}

	/**
	 * 保存前的操作
	 */
	protected function beforeSave(){
		if($this->isNewRecord){
			$this->originatorId = Yii::app()->user->id;
			$this->createTime = new CDbExpression('NOW()');
		}
		return true;
	}



	/**
	 * 保存后的操作
	 */
	protected function afterSave(){
		if($this->isNewRecord){
			if( $this->type == '1' ){
				$message = '操作人：'. Yii::app()->user->getState('username').'(后台 userId:'.$this->originatorId.')';
			}else{
				$message = '操作人：'. tbProfile::model()->getMemberUserName( $this->originatorId ).'(业务员 memberId:'.$this->originatorId.')';
			}

			tbOrderMessage::addMessage2( $this->orderId,'出具结算单',$message );
		}
		return parent::afterSave();
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
		$ids = array_map(function ($i){ return $i->orderId;}, $return['list']);
		if( !empty($ids) ){
			$c = new CDbCriteria;
			$c->select = 't.orderType';
			$c->compare('t.orderId',array_unique($ids));
			$orders = tbOrder::model()->findAll( $c );
			foreach ( $orders as $key=>$val ){
				$info =  $val->getAttributes( array('orderType') );
				$info['products'] = array();
				foreach ( $val->products as $pval ){
					$info['products'][] = $pval->getAttributes( array('orderProductId','salesPrice','num','color','singleNumber') );
				}
				$orders[$val->orderId] = $info;
				unset( $orders[$key] );
			}
		}


		foreach ( $return['list'] as &$val ){
			$prices = unserialize( $val->prices );
			$val = array_merge( $val->getAttributes( array('settlementId','orderId','state')),$orders[$val->orderId] );
		}

		$return['pages'] = $model->getPagination();
		return $return;
	}

}