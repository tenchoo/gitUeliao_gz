<?php
/**
 * Created by PhpStorm.
 * User: yagas
 * Date: 2015/11/25
 * Time: 15:43
 * @access 客户订货管理
 */
class OrderController extends Controller {

	/**
	 * 客户订货订单列表
	 * @access 订货单列表
	 */
    public function actionIndex() {
        $serial = Yii::app()->request->getQuery( 's' );
        $order  = Yii::app()->request->getQuery( 'o' );

        $criteria = new CDbCriteria();
        $criteria->condition = "source=:source and state=0";
        $criteria->group = "orderId";

        $params = array(':source'=>tbOrderPurchase2::FROM_ORDER);

        if( $serial ) {
            $criteria->addCondition("productCode=:pro");
            $params[':pro'] = $serial;
        }

        if( $order ) {
            $criteria->addCondition("orderId=:orderId");
            $params[':orderId'] = $order;
        }

        $criteria->params = $params;

        $count = tbOrderPurchase2::model()->count( $criteria );
        $pages = new CPagination( $count );
        $pages->setPageSize( (int)tbConfig::model()->get('page_size') );
        $pages->applyLimit( $criteria );
        $orderList = tbOrderPurchase2::model()->findAllByOrder( $criteria );

        $this->render( 'index', array('pages'=>$pages,'total'=>$count,'orderList'=>$orderList) );
    }

    public function actionList() {
        $serial = Yii::app()->request->getQuery( 's' );
        $order  = Yii::app()->request->getQuery( 'o' );

        if( empty($serial) && empty($order) ) {
            $criteria = new CDbCriteria();
            $criteria->condition = "orderType=:type and isDel=:state";
            $criteria->params = array(':type'=>1,'state'=>0);

            $total = tbOrder::model()->count( $criteria );
            $pages = new CPagination( $total );
            $pages->setPageSize( (int) tbConfig::model()->get('page_size') );
            $pages->applyLimit( $criteria );

            $orderList = tbOrder::model()->findAll( $criteria );
        }
        else {
            extract( $this->search( $serial, $order ) );
        }

        $this->render( 'list', array('pages'=>$pages,'total'=>$total,'orderList'=>$orderList) );
    }

    /**
     * @access 待匹配订单
     * @throws CHttpException
     */
    public function actionUnassign() {
		$serial = Yii::app()->request->getQuery( 's' );
        $order  = Yii::app()->request->getQuery( 'o' );

		$model = new tbOrderPurchase2();

		$where = ' P.`isAssign`=0 ';
		if( !empty( $order ) ){
			$where .= " and P.`orderId` = '$order'";
		}

		if( !empty( $serial ) ){
			$where .= " and P.`productCode` = '$serial'";
		}

		$sql = "select count(distinct orderId) from {$model->tableName()} P where $where and exists( select null from {{order}} o where o.orderId = P.orderId and o.state = 1 )" ;
		$cmd = $model->getDbConnection()->createCommand($sql);
		$result = $cmd->queryScalar();
		$total = intval($result);

		$total = $model->unAssignCount();

		$pages  = new CPagination();
		$pageSize = (int)tbConfig::model()->get('page_size');
		$pages->setPageSize( $pageSize );
		$pages->setItemCount( $total );

		$offset = $pages->currentPage * $pageSize;


		$sql = "SELECT DISTINCT P.`orderId`, O.`createTime` FROM {$model->tableName()} P LEFT JOIN {{order}} O USING(`orderId`) WHERE  $where limit {$offset},{$pageSize}";
		$cmd = $model->getDbConnection()->createCommand($sql);
		$orderList = $cmd->queryAll();

		foreach ( $orderList as &$item ){
			$item['products'] = $model->findAllByAttributes( array('orderId'=>$item['orderId']) );
		}

		$this->render( 'unassign', array('pages'=>$pages,'total'=>$total,'orderList'=>$orderList,'serial'=>$serial,'order'=>$order) );
    }

    /**
     * @access 订单审核
     */
  /*   public function actionValidate() {
        $id = Yii::app()->request->getQuery('id');
        $order = tbOrder::model()->findByPk( $id );
        $ajax = new AjaxData(false);
        $hasError = false;
        if( is_null($order) ) {
            $ajax->message = "Not found record";
            echo $ajax->toJson();
            Yii::app()->end( 200 );
        }

        $tr = Yii::app()->getDb()->beginTransaction();
        $order->state = 1;
        if( $order->save() ) {
            $hasError = tbOrderPurchase::pushProducts( new CEvent($order) );
        }

        if( !$hasError ) {
            $tr->rollback();

            $ajax->message = "Failed push to purchase";
            echo $ajax->toJson();
            Yii::app()->end( 200 );
        }

        $tr->commit();
        $ajax->state = true;
        echo $ajax->toJson();
    } */

    /**
     * @access 订单明细查看
     * @throws CHttpException
     */
    public function actionView() {
        $id = Yii::app()->request->getQuery('id');
        if( empty($id) || !is_numeric($id) ) {
            throw new CHttpException( 404, "Invalid id values" );
        }

        $order = tbOrder::model()->findByPk( $id );
        if( is_null($order) ) {
            throw new CHttpException( 404, "Not found record" );
        }

        $this->render('view',array('order'=>$order));
    }


    private function search( $serial, $order ) {
        $criteria = new CDbCriteria();
        $criteria->group = "orderId";

        if( !empty( $serial) ) {
            $criteria->addColumnCondition( array('singleNumber'=>$serial) );
        }

        if( !empty( $order ) ) {
            $criteria->addColumnCondition( array('orderId'=>$order) );
        }

        $orderList = tbOrderProduct::model()->findAll( $criteria );
        $orderIds = array_map( function($row){
            return $row->orderId;
        }, $orderList);

        $criteria = new CDbCriteria();
        $criteria->condition = "state=0";
        $criteria->addInCondition('orderId', $orderIds);

        $total = tbOrder::model()->count( $criteria );
        $pages = new CPagination( $total );
        $pages->setPageSize( (int) tbConfig::model()->get('page_size') );
        $pages->applyLimit( $criteria );

        $orderList = tbOrder::model()->findAll( $criteria );

        return array( 'total'=>$total, 'pages'=>$pages, 'orderList'=>$orderList );
    }
}