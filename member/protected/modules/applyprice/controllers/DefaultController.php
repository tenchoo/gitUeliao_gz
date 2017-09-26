<?php
/**
* 批发价申请
*/

class DefaultController extends Controller {

	public function init(){
		parent::init();
		$this->pageTitle .= ' 批发价格申请';
	}

	public function beforeAction( $action ){
		//只有业务员能访问
		$userType = Yii::app()->user->getState('usertype');
		if( $userType != 'saleman'){
			throw new CHttpException(403,"You do not have permission to view the current page.");
		}
		return  $action ;
	}


	/**
	* 批发价申请列表页
	* @access 批发价申请列表页
	*/
	public function actionIndex() {
		$state = Yii::app()->request->getQuery('state');
		$keyword = trim( Yii::app()->request->getQuery('keyword') );

		$condition = array( 'isDel'=>'0' ,'salemanId'=>Yii::app()->user->id );
		if( is_numeric($state) && $state>=0  ){
			$condition['state'] = $state;
		}

		if( $keyword ){
			if( is_numeric( $keyword ) ){
				$condition['applyPriceId'] = $keyword;
			}else{
				$condition['companyName'] = $keyword;
			}
		}

		$data = tbMemberApplyPrice::model()->search( $condition ,10 );

		$data['state'] = array_key_exists($state,$data['stateTitle'])?$state:null;
		$data['keyword'] = $keyword;

		$this->render( 'index' ,$data);
	}

	/**
	* @access 新增申请
	*/
	public function actionAdd(){
		//取得当前所服务的客户列表
		$memberList = tbMember::model()->searchServes( Yii::app()->user->id );

		if( Yii::app()->request->isPostRequest ){
			if( $this->addApply( $memberList,$model ) ){
				$this->dealSuccess( $this->createUrl('index') );
			}else{
				$this->dealError( $model->errors );
			}
		}

		$memberId = Yii::app()->request->getQuery('memberId','');
		if( !array_key_exists( $memberId, $memberList ) ){
			$memberId = '';
		}

		$productInfo =  null;
		$productId = Yii::app()->request->getQuery('productId','');
		if( $productId > 0 && is_numeric($productId) ){
			$product = tbProduct::model()->findByPk ( $productId,'state = 0 ' );
			$productInfo =  $product->getAttributes( array('productId','unitId','title','serialNumber','mainPic','price','tradePrice') );
			$productInfo['unit'] =  tbUnit::getUnitName( $productInfo['unitId'] );
		}
		$this->render( 'add',array( 'memberId'=>$memberId,'memberList'=>$memberList,'productInfo'=>$productInfo ) );
	}

	private function addApply( $memberList,&$model ){

		$model = new tbMemberApplyPrice();
		$model->salemanId =  Yii::app()->user->id;

		$model->productId =  Yii::app()->request->getPost('productId');
		$model->memberId =  Yii::app()->request->getPost('memberId');
		$model->applyPrice =  Yii::app()->request->getPost('applyPrice');
		$model->applyPrice = str_replace(',','',$model->applyPrice );

		if( !$model->validate() ){
			return false;
		}

		if( !array_key_exists( $model->memberId, $memberList ) ){
			$model->addError( 'memberId','此客户不是您所服务的客户，请选择你所服务的客户' );
			return false;
		}

		//判断此产品是否存在
		$exist = tbProduct::model()->exists ( 'productId =:productId and state = 0 ',array(':productId'=>$model->productId ) );
		if( !$exist ){
			$model->addError( 'productId','此产品不存在已下线' );
			return false;
		}

		//判断是否已经申请，
		$exist = $model->exists ( 'productId =:productId and memberId = :memberId and state in (0,1) and isDel=0 ',array(':productId'=>$model->productId,':memberId'=>$model->memberId ) );
		if( $exist ){
			$model->addError( 'productId','此产品已经申请批发价,不能重复申请' );
			return false;
		}

		return $model->save();
	}

	/**
	* @access 编辑申请
	*/
	public function actionEdit(){
		$id =  Yii::app()->request->getQuery('id');
		$model = tbMemberApplyPrice::model()->findByPk( $id,'state !=1 and salemanId = :salemanId',
														array( ':salemanId'=>Yii::app()->user->id ) );

		if( !$model ){
			$this->redirect( $this->createUrl('index') );exit;
			//throw new CHttpException(404,"the require obj has not exists.");
		}

		if( Yii::app()->request->isPostRequest ){
			if( $this->editApply( $model ) ){
				$this->dealSuccess( $this->createUrl('index') );
			}else{
				$this->dealError( $model->errors );
			}
		}

		$product =  $model->product->getAttributes( array('unitId','title','serialNumber','mainPic','price') );
		$data = array_merge( $model->attributes, $product );

		$stateTitle = $model->stateTitle();
		$data['stateTitle'] = array_key_exists( $model->state, $stateTitle )?$stateTitle[$model->state]:'';

		$data['unitName'] = '';
		$unit =  tbUnit::model()->findByPk ( $product['unitId'] );
		if( $unit ){
			$data['unitName'] = $unit->unitName;
		}

		$data['companyname'] = ( $model->company )? $model->company->shortname:'';
		$this->render( 'edit',array( 'data'=>$data ) );
	}

	private function editApply( &$model ){
		$oldPrice = $model->applyPrice;
		$oldstate = $model->state;
		$model->applyPrice =  Yii::app()->request->getPost('applyPrice');
		$model->applyPrice = str_replace(',','',$model->applyPrice );
		$model->state = 0; //编辑后状态设为待审核

		//判断是否已经申请，如果已经有申请，不能重复。
		$exist = $model->exists ( 'productId =:productId and memberId = :memberId and state in (0,1) and isDel=0 and applyPriceId!= :id',array(':productId'=>$model->productId,':memberId'=>$model->memberId,':id'=>$model->applyPriceId ) );
		if( $exist ){
			$model->addError( 'productId','此产品已经申请批发价,不能重复申请' );
			return false;
		}

		//增加操作记录
		$op = new tbMemberApplyPriceOp();
		$op->isManage = 0;
		$op->code = 'modify';
		$op->applyPriceId = $model->applyPriceId;
		$op->userId = Yii::app()->user->id;
		$op->remark = '';
		if( $oldstate != '0' ){
			$op->remark .= '再次提交申请;';
		}

		if( $oldPrice != $model->applyPrice ){
			$op->remark .= ' 批发价格由'.$oldPrice.'修改为'.$model->applyPrice;
		}

		if(  $model->save() ){
			$op->save();
			return true;
		}

		return false;
	}

	/**
	* @access 申请失效
	* 只有审核通过的产品才能申请失效
	*/
	public function actionInvalid(){
		$id =  Yii::app()->request->getQuery('id');
		$model = new tbMemberApplyPrice();
		if( $model->invalid( $id,Yii::app()->user->id ) ){
			$url = Yii::app()->request->urlReferrer;
			$this->dealSuccess( $url );
		}else{
			$this->dealError( $model->errors );
		}
	}

	/**
	* @access 删除申请
	*/
	public function actionDel(){
		$id =  Yii::app()->request->getQuery('id');
		$model = new tbMemberApplyPrice();
		if( $model->del( $id,Yii::app()->user->id ) ){
			$url = Yii::app()->request->urlReferrer;
			$this->dealSuccess( $url );
		}else{
			$this->dealError( $model->errors );
		}
	}

	/**
	* @access 搜索查找产品
	*/
	public function actionSearchproduct(){
		$this->attachBehavior('searchHelper', 'libs.commons.models.searchHelper');

		$keyword = trim( Yii::app()->request->getQuery('keyword','') );
		$memberId = Yii::app()->request->getQuery('memberId');

		$isserve = tbMember::checkServe( $memberId,Yii::app()->user->id );
		if( !$isserve ){
			$this->redirect( $this->createUrl('add') );exit;
		}

		$result  = 	$this->fetchProducts( null , $keyword, array() );
		$result  = $result->matches;
		$params['total'] = $this->getSearchTotal();

		$model = new tbMemberApplyPrice();
		
		$list = array();
		
		foreach ( $result  as $val ){
			$product = tbProduct::model()->findByPk( $val->productid );
			if( !$product ) continue;

			$unit = tbUnit::model()->findByPk ( $product->unitId );
			$val->state = $product->state;
			$val->unit      = ($unit)?$unit->unitName:'';
			$val->tradePrice      = $product->tradePrice;
			
			//判断是否已经申请			
			$val->hasApply = $model->exists ( 'productId =:productId and memberId = :memberId and state in (0,1) and isDel=0 ',array(':productId'=>$val->productid,':memberId'=>$memberId ) );
			
			$list[] = (array)$val;			
		}

		$mc = tbProfileDetail::model()->findByPk( $memberId );
		if ( $mc ){
			$companyname = empty($mc->shortname)?$mc->companyname:$mc->shortname;
		}else{
			$companyname = '';
		}

		$this->render( 'serachproduct',array( 'memberId'=>$memberId, 'companyname'=>$companyname,'keyword'=>$keyword,'pages'=>$this->getSearchPager(), 'list'=> $list) );
	}
}