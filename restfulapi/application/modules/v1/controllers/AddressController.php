<?php
/**
 * 订单确认地址
 * @author liang
 * @version 0.1
 * @package Controller
 */
class AddressController extends Controller {

	public function init() {
		parent::init();

		if( empty( $this->memberId ) ) {
			$this->message = Yii::t('user','You do not log in or log out');
			$this->showJson();
		}

		if( $this->userType == tbMember::UTYPE_SALEMAN ){
			$this->memberId = Yii::app()->request->getParam('memberId');
			if( empty($this->memberId ) ){
				$params = Yii::app()->request->getRestParams();
				if( isset($params['memberId']) ){
					$this->memberId = $params['memberId'];
				}
			}

			if( !is_numeric($this->memberId) || $this->memberId < 1 ){
				$this->message = Yii::t('user','memberId must');
				$this->showJson();
			}
		}
	}

	/**
	 * 订单确认地址列表
	 */
	public function actionIndex(){
		$address = tbMemberAddress::model()->getAll( $this->memberId );
		$result = array();
		foreach( $address as $key=>$val ){
			$result[$key] = $val->attributes;
			$result[$key]['cityinfo'] = tbArea::getAreaStrByFloorId( $val->areaId );
		}
		$this->data = $result;
		$this->state = true;
		$this->showJson();
	}

	/**
	* 取得单个地址信息
	* @param integer $id 地址ID
	*/
	public function actionShow( $id ){
		$address = tbMemberAddress::model()->findOne($id ,$this->memberId );
		if( $address ){
			$info = $address->attributes;
			$info['areas'] = tbArea::getAreaArrByFloorId( $address->areaId );
			$this->data = $info;
			$this->state = true;
		}else{
			$this->message = 'the address is no exists';
		}
		$this->showJson();
	}

	/**
	* 新增地址
	*/
	public function actionCreate(){
		$model = new tbMemberAddress();
		$model->memberId = $this->memberId;

		//最多只能有10个地址
		$count = $model-> getCount( $this->memberId );
		if( $count >= 10 ){
			$this->message = Yii::t('msg','Receive the goods address 10');
			$this->showJson();
		}

		$this->saveAddress( $model );
	}

	/**
	* 编辑地址
	* @param integer $id 地址ID
	*/
	public function actionUpdate( $id ){
		$model = tbMemberAddress::model()->findOne($id ,$this->memberId );
		if( !$model ){
			$this->message = 'the address is no exists';
			$this->showJson();
		}
		$this->saveAddress( $model );
	}

	/**
	* 保存地址信息
	*/
	private function saveAddress( $model ){
		$model->attributes = Yii::app()->request->getRestParams();
		if( $model->save() ) {
			$this->state = true;
			$this->message = 'success';
			$info = $model->attributes;
			$info['areas'] = tbArea::getAreaArrByFloorId( $model->areaId );
			$this->data = $info;
		}else{
			$this->message = current( current( $model->getErrors() ) );
		}
		$this->showJson();
	}


	/**
	* 删除会员地址
	* @param integer $id 地址ID
	*/
	public function actionDelete( $id ){
		$model = tbMemberAddress::model()->findOne($id ,$this->memberId );
		if($model){
			if($model->delete()){
				$this->state = true;
			}
		}else{
			$this->message = 'the address is no exists';
		}
		$this->showJson();
	}

	/**
	* 取得一地址为默认地址
	*/
	public function actionDefault(){
		$address = tbMemberAddress::model()->getDefault( $this->memberId );
		if( $address ){
			$this->data = $address->attributes;
			$this->data['cityinfo'] = tbArea::getAreaStrByFloorId( $address->areaId );
			$this->state = true;
		}else{
			$this->message = 'the default address is no exists';
		}
		$this->showJson();
	}
}