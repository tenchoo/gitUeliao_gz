<?php
/**
 * 工厂订单管理
 * @access 工厂订单管理
 * @package Controller
 * @since 0.1
 * @version 0.1
 * User: yagas
 * Date: 2016/2/29
 * Time: 15:59
 */

class DefaultController extends Controller {

//    public $layout = "//layouts/factory";  //暂时屏蔽工厂视图

    /**
     * @access 工厂订单列表
     */
    public function actionIndex() {
        $criteria = new CDbCriteria();

//        $criteria->condition = "state=:state and supplierId=:id";
//        $criteria->params = array(':state'=>tbOrderPurchasing::STATE_NORMAL, ':id'=>Yii::app()->user->id);
        /**########### 管理后台测试代码 #############*/
        $criteria->condition = "state=:state";
        $criteria->params = array(':state'=>tbOrderPurchasing::STATE_NORMAL);
        /**########### 管理后台测试代码 #############*/

        $criteria->order = "createTime ASC";

        $total = tbOrderPurchasing::model()->count($criteria);
        $pages = new CPagination($total);
        $pages->setPageSize((int)tbConfig::model()->get('page_size'));
        $pages->applyLimit($criteria);
        $orders = tbOrderPurchasing::model()->findAll($criteria);

        $this->render('index', array('pages'=>$pages, 'orders'=>$orders));
    }

    public function actionPosted() {
        $criteria = new CDbCriteria();

//        $criteria->condition = "orderType=:type and userId=:id";
//        $criteria->params = array(':type'=>tbOrderPost2::TYPE_FACTORY, ':id'=>Yii::app()->user->id);
        /**########### 管理后台测试代码 #############*/
        $criteria->condition = "orderType=:type";
        $criteria->params = array(':type'=>tbOrderPost2::TYPE_USER);
        /**########### 管理后台测试代码 #############*/

        $criteria->order = "createTime DESC";

        $total = tbOrderPost2::model()->count($criteria);
        $pages = new CPagination($total);
        $pages->setPageSize((int)tbConfig::model()->get('page_size'));
        $pages->applyLimit($criteria);
        $orders = tbOrderPost2::model()->with('user')->findAll($criteria);

        $this->render('posted', array('pages'=>$pages, 'orders'=>$orders));
    }

    /**
     * @access 创建发货单
     */
    public function actionCreate() {
		$id = Yii::app()->request->getQuery('id');
        $purchasing = tbOrderPurchasing::model()->findByPk($id,'state =:s',array(':s'=>tbOrderPurchasing::STATE_NORMAL));
        if( is_null( $purchasing ) ) {
            $this->redirect( $this->createUrl('index') );
        }

        if(Yii::app()->request->isPostRequest) {
            //创建发货单记录
			$post = new tbOrderPost2();
			if( $post->createPost( $purchasing, 0 ) ){
				$this->dealSuccess ( $this->createUrl('index') );
			}else{
				$this->dealError ( $post->getErrors() );
			}
        }

        $products = tbOrderPurchasingProduct::model()->findAllByAttributes(['purchaseId'=>$id]);
        $this->render('create',['order'=>$purchasing, 'products'=>$products]);
    }


    /**
     * @access 确定发货完成
     */
    public function actionFinish() {
		$id = Yii::app()->request->getQuery('id');
        $purchase = tbOrderPurchasing::model()->findByPk($id,'state =:s',array(':s'=>tbOrderPurchasing::STATE_NORMAL));
        if( is_null( $purchase ) ) {
            $this->redirect( $this->createUrl('index') );
        }

		//判断是否有发过货，有发货不允许取消
		$falg = tbOrderPost2::model()->exists('purchaseId=:purchaseId',array(':purchaseId'=>$purchase->purchaseId));
		if( !$falg ){
			$this->dealError ( array('还未发过货,不能提交发货完成') );
		}else{
			$purchase->stateToDone();
			$this->dealSuccess( Yii::app()->request->urlReferrer );
		}
    }



    /**
     * 工厂已发货订单列表
     * @access 已发货订单列表
     */
    public function actionView() {
        $id = Yii::app()->request->getQuery("id");
        $order = tbOrderPost2::model()->with('purchase')->findByPk($id);
        if(is_null($order)) {
            $this->setError(['message'=>Yii::t('order','Not found record')]);
            $this->redirect($this->createUrl('index'));
        }
        $products = tbOrderPost2Product::model()->with('details')->findAllByAttributes(['postId'=>$id]);

        //获取物流跟踪信息
        $m = Yii::app()->params['SLD_member'];
        $memberApi = new ApiClient($m,'service');
        $url = $memberApi->createUrl('/ajax/default/index',array('action'=>'express','com'=>'shunfeng','nu'=>$order->logisticsCode));
        $content = $memberApi->fetchUrl($url,null,false);
        $content = json_decode( $content,true );

        $this->render("view", array('order'=>$order, 'products'=>$products));
    }

    /**
     * 创建发货单细节
     * 启动事物，创建发货单
     * 创建发货单明细订单记录
     * 发货单及发货单明细写入成功，提事物
     * 如果发货单或发货单明细写入失败，事物回滚
     * @throws CDbException
     */
    private function createPostOrder( $purchase ) {
        $product = Yii::app()->request->getPost('product');
		if( !is_array(  $product )  || empty (  $product ) ){
			$this->dealError ( array( array('发货产品不能为空') ) );
			return;
		}

        $form    = Yii::app()->request->getPost('form');

        unset($form['logisticId']);

        //启动事物处理
        $tr = Yii::app()->db->beginTransaction();

        //创建发货单记录
        $post = new tbOrderPost2();
		$post->setAttributes($form);

		$post->purchaseId = $purchase->purchaseId;
		$post->orderType =  0;

        //保存发货单记录
        if( $post->save() ) {

            //循环创建发货单明细记录
            foreach($product as $pid=>$item) {
                $purchasePro = tbOrderPurchasingProduct::model()->findByPk($pid);
                if(is_null($purchasePro)) {
                    //无法找到采购的产品记录明细，回滚事物并提示错误信息
                    $tr->rollback();
                    $this->setError(['message'=>Yii::t('order','Not found record')]);
                    $this->redirect($this->createUrl('index'));
                }

                $postPro = new tbOrderPost2Product();
                $postPro->setAttributes(['postId'=>$post->postId, 'purchaseId'=>$purchasePro->purchaseId, 'purchaseProId'=>$purchasePro->purchaseProId,'postTotal'=>$item]);
                $postPro->comment = '';

                if(!$postPro->save()) {
                    //写入发货单明细记录失败，回滚事物并提示错误信息
                    $tr->rollback();
					$this->dealError ( $postPro->getErrors() );
                }
            }

            $purchase->stateToDone();
            //无写入错误则提交事物，并跳转页面到列表页面
            $tr->commit();
			$this->dealSuccess ( $this->createUrl('index') );
        }else{
			$this->dealError ( $post->getErrors() );
		}

    }
}