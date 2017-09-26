<?php
/**
 * 订单结算单
 * @author liang
 * @version 0.1
 * @package CFormModel
 *
 */

class OrderSettlement extends CFormModel {

	public $deliveryMethod;

	public $payModel;

	public $address;

	public $memo;

	public $freight = 0;

	public $settlementNum;

	public $remark;

	public $isSample;

	public $products;

	/**
	* Declares the validation rules.
	* The rules state that account and password are required,
	* and password needs to be authenticated.
	*/
	public function rules()	{
		return array(
			array('deliveryMethod,payModel,address,settlementNum,freight','required'),
			array('deliveryMethod,payModel,isSample','numerical','min'=>'1',"integerOnly"=>true),
			array('settlementNum', "numerical",'min'=>'0.1',"integerOnly"=>false),
			array('freight', "numerical",'min'=>'0',"integerOnly"=>false),
			array('address,memo,remark,products', 'safe'),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签
	*/
	public function attributeLabels() {
		return array(
			'deliveryMethod' => '提贷方式',
			'payModel' => '支付方式',
			'address' => '收货地址',
			'memo' => '订单备注',
			'freight' => '物流费',
			'settlementNum' => '结算数量',
			'isSample'=> '是否赠板',
			'remark'=> '备注',
		);
	}

	/**
	* 生成结算单
	* @param array $dataArr 生成结算单提交的数据
	* @param obj $model
	*/
	public function save( $dataArr,$model ){
		$printpush = Yii::app()->request->getPost('printpush');
		if( $printpush == '1' ){
			$user = tbMemberSaleman::model()->findByPk( Yii::app()->user->id );
			if( !$user ){
				$this->addError('num','请先配置打印机');
				return false;
			}

			//取得得打印机
			$printer = tbPrinter::model()->findByPk( $user->printerId );
			if( !$printer ){
				$this->addError('num','请先配置打印机');
				return false;
			}
		}


		$this->attributes = $dataArr;

		if( !array_key_exists('products',$dataArr) || empty( $dataArr['products'] ) ){
			if(!$this->validate()){
				return false;
			}
		}

		foreach( $dataArr['products'] as $val ){
			$this->attributes = $val;
			if(!$this->validate()){
				return false;
			}
		}

		if( array_key_exists( 'freight', $dataArr ) ){
			$model->freight = $this->freight;
		}

		//使用事务处理
		$transaction = Yii::app()->db->beginTransaction();
		try {
			//1.生成结算单，记录明细
			$Settlement = new tbOrderSettlement();
			$Settlement->orderId = $model->orderId;
			$Settlement->warehouseId = $model->warehouseId;

			//单据来源：０前台业务员生成，１后台生成
			$userType =  Yii::app()->user->getState('usertype');
			$Settlement->type = ($userType == 'saleman' )?'0':'1';

			$Settlement->freight = $model->freight;

			if( !$Settlement->save() ){
				$this->addErrors( $Settlement->getErrors() );
				return false;
			}

			$detail = new tbOrderSettlementDetail ();
			$detail->settlementId = $Settlement->settlementId;

			$productPayments = array();
			foreach( $model->products as $val ){
				$val->isfree = 2;
				if( !isset($dataArr['products'][$val->orderProductId]) ){
					$val->deliveryNum = 0;
					$val->state = 1;
				}else{
					$products = $dataArr['products'][$val->orderProductId];
					
					if( $products['settlementNum']>$val->num ){
						$this->addError('num',Yii::t('base','Settlement quantity can not be greater than the number of buy'));
						return false;
					}

					/* if( $model->state == '2' ){
						if( $products['settlementNum']>$val->packingNum ){
							$this->addError('num',Yii::t('base','Settlement quantity can not be greater than the number of pick'));
							return false;
						}
					}else{
						
					} */
					$val->num = $val->deliveryNum = $products['settlementNum'];
					$val->remark = $products['remark'];
					$_detail = clone $detail ;
					$_detail->orderProductId = $val->orderProductId;
					$_detail->num = $products['settlementNum'];
					$_detail->remark = $products['remark'];

					if( isset($products['isSample']) && $products['isSample'] == '1' && $products['settlementNum'] < 5 ){
						$_detail->isSample = 1;
						$val->isSample = 1;
					}else{
						$_detail->isSample = 0;
						$val->isSample = 0;
					}

					//赠板不算钱
					if( $_detail->isSample != '1' ){
						$productPayments[] = bcmul( $val->num,$val->price ,2 );
					}

					if( !$_detail->save() ){
						$this->addErrors( $_detail->getErrors() );
						return false;
					}
				}
				if( !$val->save() ){
					$this->addErrors( $val->getErrors() );
					return false;
				}
			}

			$Settlement->productPayments = array_sum($productPayments);
			if( !$Settlement->save() ){
				$this->addErrors( $Settlement->getErrors() );
				return false;
			}

			//2.更新order表的结算方式，提货信息，备注和状态
			$model->realPayment = $Settlement->productPayments + $model->freight;
			if( $model->state == '2' ){
				$model->state = 3; //备货完成生成结算单后，进入待发货状态
			}
			$model->deliveryMethod = $this->deliveryMethod;
			$model->payModel = $this->payModel;
			$model->address = $this->address;
			$model->memo = $this->memo;
			$model->isSettled = 1;
				if( !$model->save() ){
				$this->addErrors( $model->getErrors() );
				return false;
			}


			//月结
			if( $model->payModel =='1' ){
				$creditDetail = new tbMemberCreditDetail();
				if( !$creditDetail->changeCredit( $model ) ){
					$this->addErrors( $creditDetail->getErrors() );
					return false;
				}
			}

			$transaction->commit();
		} catch (Exception $e) {
 			$transaction->rollback(); //如果操作失败, 数据回滚
			throw new CHttpException(503,$e);
			return false;
		}

		if( $printpush == '1' ){
			//推送打印
			PrintPush::printSettlement( $Settlement->settlementId,$msg );
		}
		return true;
	}

	/**
	* 取得列表
	* @param array $condition 查询列表条件
	*/
	public function search( $condition = array(),$pageSize =10 ){
		$criteria = new CDbCriteria;
		if(!empty($condition)){
			foreach ($condition as $key=>$val){

				if( is_null( $val ) || $val === '' ) continue ;

				switch( $key ){
					case 'createTime1':
						$criteria->addCondition("t.createTime>='$val'");
						break;
					case 'createTime2':
						$createTime2 = date("Y-m-d H:i:s",strtotime( $val )+86400 ) ; //包含选择的当天
						$criteria->addCondition("t.createTime<'$createTime2'");
						break;
					case 'is_string':
						$criteria->addCondition( $val );//直接传搜索条件
						break;
					default:
						$criteria->compare('t.'.$key,$val);
						break;
				}
			}
		}

		$userType = Yii::app()->user->getState('usertype');
		if(  $userType == tbMember::UTYPE_SALEMAN ){
			$userId[] =  Yii::app()->user->id;
			if( tbConfig::model()->get( 'default_saleman_id' ) == Yii::app()->user->id ){
				$userId[] = 0;
			}
			$userId = implode(',',$userId);
			$criteria->join .= 'inner join {{order}} o on ( o.orderId = t.orderId ) left join {{member}}  m on (m.memberId = o.memberId and m.userId in ('.$userId.') )';
			$criteria->addCondition("m.memberId is not null ");
		}

		$criteria->order = ' t.createTime desc ';
		$criteria->with = array('order');
		$model = new CActiveDataProvider( 'tbOrderSettlement', array(
			'criteria'=>$criteria,
			'pagination'=>array('pageSize'=>$pageSize,'pageVar'=>'page'),
		));


		$return['list'] = array();
		$return['pages'] = $model->getPagination();
		$data = $model->getData();
		if( empty( $data ) ){
			return $return;
		}

		$order = new Order();
		$userIds = $productids = array();
		foreach ( $data as $val ){
			$list = array();
			$userIds[] = $val->order->userId;
			$list = array_merge($val->attributes,array(
													'orderCreateTime'=>$val->order->createTime,
													'userId'=>$val->order->userId,
													'memberId'=>$val->order->memberId,
													'payState'=>$val->order->payState,
													'realPayment'=>$val->order->realPayment,
								));
			$list['products'] = array();
			foreach ($val->order->products as $pval){
				if( $pval->state == '0' ){
					$list['products'][] = $pval->attributes;
					$productids[] = $pval->productId;
				}
			}

			$list['member'] = $order->getMemberDetial( $val->order->memberId );
			$return['list'][] = $list;
		}

		$userIds = implode(',',array_unique( $userIds ));
		$memberPro = tbProfile::model()->findAll(
					array('select'=>'memberId,username',
						  'condition'=>'memberId in ('.$userIds.')',

					));
		foreach ( $memberPro as $val ){
			$salesmans[$val->memberId] = $val->username;
		}

		$units = tbProduct::model()->getUnitConversion( $productids );

		foreach ( $return['list'] as &$val){
			$val['salesman'] = (isset($salesmans[$val['userId']]))?$salesmans[$val['userId']]:'';

			foreach ( $val['products'] as &$pval ){
				$pval['unitName'] = (isset($units[$pval['productId']]['unit']))?$units[$pval['productId']]['unit']:'';
			}
		}
		return $return;
	}
}