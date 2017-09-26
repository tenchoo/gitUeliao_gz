<?php
/**
 * 订单价格申请信息表
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$orderId			订单ID
 * @property integer	$state				状态：0未审，1审核通过，2审核不通过,3删除
 * @property integer	$applyType			申请来源：0前台业务员申请，1后台申请
 * @property integer	$originatorId		申请人memberId/userId
 * @property integer	$checkUserId		审核人userId
 * @property date		$createTime			申请时间
 * @property date		$checkTime			审核时间
 * @property string		$prices				申请价格详情
 *
 */

 class tbOrderApplyprice extends CActiveRecord {

	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{order_applyprice}}";
	}

	public function relations(){
		return array(
			'order'=>array(self::BELONGS_TO,'tbOrder','orderId'),
		);
	}

	public function rules() {
		return array(
			array('orderId,prices','required'),
			array('applyType','in','range'=>array(0,1)),
			array('orderId', "numerical","integerOnly"=>true),
			array('prices', "safe"),
			array('orderId', "numerical","integerOnly"=>true),
			array('orderId','checkExists','on'=>'insert'),

		);
	}

	/**
	* 检查是否存在，不允许多次价格申请,rules 规格
	*/
	public function checkExists( $attribute,$params ){
		if( empty( $this->$attribute ) ){
			return;
		}

		$criteria=new CDbCriteria;
		$criteria->compare( $attribute,$this->$attribute );
		$criteria->compare( 'state',array(0,1) );
		$model = self::model()->find( $criteria );
		if( $model ){
			$label = $this->getAttributeLabel($attribute);
			$this->addError($attribute,'此订单已提交过价格申请');
		}
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'orderId' => '订单号',
			'remark'=>'备注',
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
	* 取得列表
	* @param array $condition 查询列表条件
	*/
	public  function search( $condition = array() ){
		$criteria = new CDbCriteria;

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
		$criteria->order = ' field (t.state,0) desc ,t.createTime desc ';
		$model = new CActiveDataProvider( $this, array(
			'criteria'=>$criteria,
			'pagination'=>array('pageSize'=>$pageSize,'pageVar'=>'page'),
		));
		$return['list'] = $model->getData();
		$ids = array_map(function ($i){ return $i->orderId;}, $return['list']);
		$productIds = array();
		if( !empty($ids) ){
			$c = new CDbCriteria;
			$c->select = 't.orderType';
			$c->compare('t.orderId',array_unique($ids));
			$orders = tbOrder::model()->with('products')->findAll( $c );
			foreach ( $orders as $key=>$val ){

				$info =  $val->getAttributes( array('orderType') );
				$info['products'] = array();
				foreach ( $val->products as $pval ){
					$productIds[$pval->productId] = $pval->productId;
					$info['products'][] = $pval->getAttributes( array('orderProductId','productId','salesPrice','num','color','singleNumber') );
				}
				$orders[$val->orderId] = $info;
				unset( $orders[$key] );
			}
		}

		$stateTitle = array('0'=>'待审核','1'=>'已审核','2'=>'审核不通过');
		$order = new Order();

		foreach ( $return['list'] as $key=>&$val ){
			$prices = unserialize( $val->prices );
			if(!isset($orders[$val->orderId])){
				unset($return['list'][$key]);
				continue;
			}

			$val = array_merge( $val->getAttributes( array('id','orderId','state')),$orders[$val->orderId] );
			foreach ( $val['products'] as $k=>&$pval ){
				if( isset( $prices[$pval['orderProductId']] ) ){
					$val['products'][$k]['applyprice'] = $prices[$pval['orderProductId']];
				}else{
					unset( $val['products'][$k] );
				}

			}
			$val['stateTitle'] = $stateTitle[$val['state']];
			$val['oType'] = $val['orderType'];
			$val['orderType'] =$order->orderType( $val['orderType'] );
		}

		//取得产品单位
		$return['units'] = tbProduct::model()->getUnitConversion( $productIds );

		$return['pages'] = $model->getPagination();
		return $return;
	}

}