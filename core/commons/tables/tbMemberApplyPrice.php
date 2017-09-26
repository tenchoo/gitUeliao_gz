<?php
/**
 * 批发价格申请记录信息表---由业务员提出申请，针对客户申请对应的产口价格，一次只能申请一个产品的价格。
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer		$applyPriceId
 * @property integer		$productId				产品ID
 * @property integer		$memberId				客户ID
 * @property integer		$salemanId				业务员memberId，即申请人
 * @property integer		$state					状态：0未审，1审核通过，2审核不通过,3失效
 * @property integer		$isDel					是否删除，0正常，1从前台删除，2从后台删除
 * @property munber			$applyPrice				申请的价格
 * @property timestamp		$createTime				申请时间
 *
 */

 class tbMemberApplyPrice extends CActiveRecord {

	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{member_applyprice}}";
	}

	public function relations() {
		return array(
			'product' => array(self::BELONGS_TO,'tbProduct','productId','select'=>'unitId,title,serialNumber,mainPic,price'),
			'saleman'=>array(self::BELONGS_TO,'tbProfile','', 'on' => 't.salemanId=saleman.memberId','select'=>'username'),
			'company'=>array(self::BELONGS_TO,'tbProfileDetail','memberId', 'select'=>'shortname'),//,companyname
		);
	}

	public function rules() {
		return array(
			array('productId,memberId,salemanId,applyPrice','required'),
			array('productId,memberId,salemanId','numerical','integerOnly'=>true,'min'=>0),
			array('applyPrice','numerical','min'=>'0.01','max'=>'100000'),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'productId' => '产品ID',
			'memberId' => '客户ID',
			'salemanId' => '业务员',
			'applyPrice' => '申请的价格',
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
	 * 保存前的操作
	 */
	protected function afterSave(){
		if($this->isNewRecord){
			//增加操作记录
			$op = new tbMemberApplyPriceOp();
			$op->isManage = 0;
			$op->code = 'insert';
			$op->applyPriceId = $this->applyPriceId;
			$op->userId = $this->salemanId;
			$op->remark = '申请批发价格为：'.$this->applyPrice;
			$op->save();
		}
		return true;
	}



	/**
	* 价格申请列表
	*/
	public function search( array $condition = array() ,$pageSize = 1 ){

		$criteria = new CDbCriteria;
		foreach ($condition as $key=>$val){
			if( is_string($val)) $val = trim($val);

			if( is_null($val) ){
				continue ;
			}

			switch( $key ){
				case 'is_string':
					$criteria->addCondition( $val );//直接传搜索条件
					break;
				case 'companyName':
					$criteria->addSearchCondition('company.shortname', $val);//搜索条件，其实代表了。。where name like '%分类
				//	$criteria->join = " left join {{}}"( $val );//直接传搜索条件
					break;
				default:
					$criteria->compare('t.'.$key,$val);
					break;
			}
		}

		$criteria->with = array('saleman','company');
		$criteria->order = "field( t.state,0) DESC , t.createTime DESC";
		$model = new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array('pageSize'=>$pageSize,'pageVar'=>'page'),
		));

		$list = array();
		$data = $model->getData();
		$stateTitle = $this->stateTitle();
		$units =  tbUnit::model()->getUnits();
		foreach ( $data as $key=>$val ){
			$product = $val->product;
			if( is_null( $product ) ) continue;

			$list[$key] = array_merge( $val->attributes, $product->getAttributes( array('unitId','title','serialNumber','mainPic','price') ) );
			$list[$key]['stateTitle'] = array_key_exists( $val->state, $stateTitle )?$stateTitle[$val->state]:'';
			$list[$key]['unitName'] = array_key_exists( $product->unitId, $units )?$units[$product->unitId]:'';
			$list[$key]['saleman'] = ( $val->saleman )?$val->saleman->username:'';
			$list[$key]['companyname'] = ( $val->company )? $val->company->shortname:'';
		}
		$result['list'] = $list;
		$result['pages'] = $model->getPagination();
		$result['stateTitle'] = $stateTitle;
		return $result;

	}

	public function stateTitle(){
		return $arr = array( '0'=>'待审核','1'=>'已审核','2'=>'审核未通过','3'=>'已失效' );
	}


	/**
	* @access 申请失效
	* 只有审核通过的产品才能申请失效
	* @param integer $id 设置失效的申请表ID
	* @param integer $userId 指定业务员,若查找指定业务员的，为前台操作，非指定，为后台操作。
	*/
	public function invalid( $id,$userId = null ){
		if( $id <= 0 ||  !is_numeric($id)  ){
			$this->addError( 'applyPriceId','没找相对应的记录' );
			return false;
		}

		$condition = 'state = :state ';
		$param = array(':state'=>'1');

		$op = new tbMemberApplyPriceOp();
		if( $userId>0 ){
			$op->isManage = 0;
			$op->userId = $userId;

			$condition .= ' and salemanId = :salemanId';
			$param[':salemanId'] = $userId;

		}else{
			$op->isManage = 1;
			$op->userId = Yii::app()->user->id;
		}


		$model = tbMemberApplyPrice::model()->findByPk( $id,$condition,$param );
		if( !$model ){
			$this->addError( 'applyPriceId','没找相对应的记录' );
			return false;
		}

		$op->code = 'invalid';
		$op->applyPriceId = $model->applyPriceId;
		$op->remark = '';

		//增加操作记录
		if( !$op->save() ){
			$this->addErrors( $op->getErrors() );
			return false;
		}

		$model->state = 3;
		return $model->save();
	}

	/**
	* @access 删除申请
	* 只有待审,审核不通过和失效的申请才能删除
	* @param integer $id 要删除的申请表ID
	* @param integer $userId 指定业务员,若查找指定业务员的，为前台操作，非指定，为后台操作。
	*/
	public function del( $id,$userId = null ){
		if( $id <= 0 ||  !is_numeric($id)  ){
			$this->addError( 'applyPriceId','没找相对应的记录' );
			return false;
		}

		$condition = 'state != 1';
		$param = array();

		if( $userId>0 ){
			$condition .= ' and salemanId = :salemanId AND isDel = 0';
			$param[':salemanId'] = $userId;
		}else{
			$condition .= ' AND isDel != 2';
		}

		$model = tbMemberApplyPrice::model()->findByPk( $id,$condition,$param );
		if( !$model ){
			$this->addError( 'applyPriceId','没找相对应的记录' );
			return false;
		}

		$op = new tbMemberApplyPriceOp();
		if( $userId>0 ){
			$op->isManage = 0;
			$op->userId = $userId;
			$model->isDel = 1;
		}else{
			$op->isManage = 1;
			$op->userId = Yii::app()->user->id;

			$model->isDel = 2;
		}

		$op->code = 'del';
		$op->applyPriceId = $model->applyPriceId;
		$op->remark = '';

		//增加操作记录
		if( !$op->save() ){
			$this->addErrors( $op->getErrors() );
			return false;
		}


		return $model->save();
	}

	/**
	* 取得批发价格
	* @param integer $memberId 客户的ID
	* @param array $productIds 产品IDS
	*/
	public function getMemberPrice( $memberId,array $productIds = array() ){
		if( !is_numeric( $memberId ) || $memberId <=0 ) return array();

		$criteria = new CDbCriteria;
		$criteria->compare('t.memberId',$memberId);
		if( !empty ( $productIds ) ){
			$criteria->compare('t.productId',array_unique( $productIds ) );
		}

		$criteria->compare('t.state','1');

		$model = $this->findAll( $criteria  );
		$result = array();
		if( $model ){
			foreach ( $model as $val ){
				$result[$val->productId] = $val->applyPrice;
			}
		}
		return $result;
	}
}