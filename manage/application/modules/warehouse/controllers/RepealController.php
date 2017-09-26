<?php

class RepealController extends Controller
{

    /**
     * 撤消入库申请列表--未审
     *  @access 未审撤消入库申请列表
     */
    public function actionIndex()
    {
        $repealId   = Yii::app()->request->getQuery('repealId');
        $repealForm = new RepealForm;
        $params     = $repealForm->findAll( $repealId,0 );
        if (!$params) {
            $this->setError($repealForm->getErrors());
            $params = ['datalist' => [], 'pagination' => new CPagination];
        }

        return $this->render('index', $params);
    }

	 /**
     * 撤消入库申请列表--已审
     *  @access 已审撤消入库申请列表
     */
	public function actionList() {
        $repealId   = Yii::app()->request->getQuery('repealId');
        $repealForm = new RepealForm;
        $params     = $repealForm->findAll( $repealId ,array(1,2) );
        if (!$params) {
            $this->setError($repealForm->getErrors());
            $params = ['datalist' => [], 'pagination' => new CPagination];
        }

        return $this->render('index', $params);
    }


	public function actionCheck(){
		$repealId   = Yii::app()->request->getQuery('id');

		$detail = null;
		if( is_numeric( $repealId ) && $repealId > 0 ){
			$detail = tbWarrantRepeal::model()->findByPk( $repealId,'state = 0' );
		}

		if( !$detail ){
			$this->redirect( $this->createUrl( 'index' ) );
		}

		if (Yii::app()->request->getIsPostRequest()) {
            $reasons = Yii::app()->request->getPost('reasons');
            $action  = Yii::app()->request->getPost('action');

            $repealForm = new RepealForm;
            $result     = $repealForm->verify($repealId, $action, $reasons);
            if ( $result ) {
                $this->dealSuccess($this->createUrl('index'));
            } else {
                $errors = $repealForm->getErrors();
				$this->dealError($errors);
            }
        }

		$oplog = tbWarrantRepealLog::model()->findAll(	array(
										'condition'=>'repealId=:repealId',
										'params'=>array( ':repealId'=> $detail->repealId ),
										'order'=>'datetime DESC',));

        return $this->render('view', [
            'detail' =>$detail,
			'oplog' =>$oplog,
        ]);
	}

    /**
     * 审核撤消入库申请
     * @access 审核撤消申请
     */
    public function actionVerify()
    {
        $repealId = Yii::app()->request->getQuery('repealId');

        $this->setError([Yii::t('base', 'Invalid request url')]);
        $this->redirect($this->createUrl('index'));
    }

    /**
     * 撤消入库申请单详情
     * @access 撤消申请详情
     */
    public function actionView()
    {
        $repealId   = Yii::app()->request->getQuery('id');
        $repealForm = new RepealForm;
		$detail =  $repealForm->detail($repealId);
		if( empty($detail)){
			$this->redirect( $this->createUrl( 'list' ) );
		}
		$oplog = tbWarrantRepealLog::model()->findAll(	array(
										'condition'=>'repealId=:repealId',
										'params'=>array( ':repealId'=> $detail->repealId ),
										'order'=>'datetime DESC',));

        return $this->render('view', [
            'detail' =>$detail,
			'oplog' =>$oplog,
        ]);
    }

    /**
     * 撤消入库申请操作日志
     */
    public function actionLog()
    {
        $dataProvider = new CActiveDataProvider('tbWarrantRepealLog', array(
            'pagination' => array(
                'pageSize' => 20,
            ),

             'criteria'=>array(
                'order'=>'datetime DESC',
            ),
        ));

        $this->render('log', [
            'datalist' => $dataProvider->getData(),
            'pages' => $dataProvider->getPagination()
        ]);
    }

    public function labels($action) {
        switch($action) {
            case tbWarrantRepealLog::ACTION_NEW:
                return '添加申请';

            case tbWarrantRepealLog::ACTION_DONE:
                return '通过申请';

            case tbWarrantRepealLog::ACTION_REFUSE:
                return '拒绝申请';
        }
    }
}

/**
 * 撤消申请单代理模型 */
class RepealForm extends CFormModel
{
    public $repealId;

    public function attributeLabels()
    {
        return [
            'repealId' => '撤消入库申请单号',
        ];
    }

    public function rules()
    {
        return [
            ['repealId', 'numerical', 'integerOnly' => true],
            ['repealId', 'repeal_exists', 'message' => 'not found repeal'],
        ];
    }

    public function repeal_exists($attribute, $options)
    {
        if (empty($this->$attribute)) {
            return true;
        }

        $repeal = tbWarrantRepeal::model()->findByPk($this->$attribute);
        if (is_null($repeal)) {
            $this->addError($attribute, Yii::t('base', $options['message']));
            return false;
        }
        return true;
    }

    /**
     * 撤消入库申请单列表 */
    public function findAll( $repealId = null ,$state )
    {
        if (!is_null($repealId)) {
            $this->repealId = $repealId;
        }

        if (!$this->validate()) {
            return false;
        }

        $criteria = new CDbCriteria;
        if (!empty($repealId)) {
            $criteria->compare('repealId', $repealId);
        }

        $criteria->compare( 'state', $state );
        $dataProvider = new CActiveDataProvider('tbWarrantRepeal', array(
            'criteria'   => $criteria,
            'pagination' => array(
                'pageSize' => 20,
            ),
        ));

        return ['datalist' => $dataProvider->getData(), 'pagination' => $dataProvider->getPagination()];
    }

    /**
     * 撤消入库申请单详情 */
    public function detail( $repealId )
    {
		if( is_numeric( $repealId )  && $repealId>0 )
			return tbWarrantRepeal::model()->findByPk($repealId);
    }

    /**
     * 撤消单审核 */
    public function verify($repealId, $action, $reasons)
    {
        $loger             = new tbWarrantRepealLog;
        $loger->attributes = ['repealId' => $repealId, 'action' => $action, 'reasons' => $reasons];

        $transaction = Yii::app()->db->beginTransaction();
        if ($loger->save()) {

            $result = ($action == 1) ? $this->warehouseOut($repealId) : $this->cancelApply($repealId);
            if ($result) {
                $transaction->commit();
                return true;
            }
        } else {
            $this->addErrors($loger->getErrors());
        }

        $transaction->rollback();
        return false;
    }

    /**
     * 通过撤消申请单号生成出库单
     * 过程：
     * 变更入库单状态
     * 插入一条新的出库记录
     * 变更撤消申请单状态
     */
    private function warehouseOut($repealId)
    {

        $repeal = tbWarrantRepeal::model()->findByPk($repealId);
        if (!$repealId) {
            return false;
        }

        // 变更入库单状态
        $warrant        = $repeal->warrant();
        $warrant->state = tbWarehouseWarrant::STATE_REPEAL;
        if (!$warrant->save()) {
            $this->addErrors($warrant->getErrors());
        } else {

            // 插入一条新的出库记录
            $order             = new tbWarehouseOutbound;
            $order->attributes = [
                'source'      => tbWarehouseOutbound::TO_REPEAL,
                'sourceId'    => $warrant->warrantId,
                'warehouseId' => $warrant->warehouseId,
                'remark'      => '',
            ];

            $repeal->state = tbWarrantRepeal::STATE_DONE;
            if (!$repeal->save()) {
                $this->addErrors($repeal->getErrors());
            }

            $products = tbWarehouseWarrantDetail::model()->findAllByAttributes(['warrantId'=>$warrant->warrantId]);
            foreach($products as $item) {
                $condition = [
                    'positionId'=>$item->positionId,
                    'singleNumber'=>$item->singleNumber,
                    'productBatch'=>$item->batch
                ];

                $product = tbWarehouseProduct::model()->findByAttributes($condition);
   				if(!$product || $product->num < $item->num ) {
                    $this->addError('repealId', Yii::t('warehouse','{product} Inventory is not enough, can not be undone',array('{product}'=>$item->singleNumber )));
                    break;
                }

                $lock = tbWarehouseLock::model()->findByAttributes($condition);
                if(!$lock) {
                    $lock = 0;
                }
                else {
                    $lock = $lock->num;
                }

                $total = bcsub($product->num, $lock,2);
                if($total < $item->num) {
                    $this->addError('repealId', Yii::t('base','product:{pro} lower than the quantity of stock', $item->singleNumber));
                    break;
                }

                $product->num = bcsub($product->num, $item->num,2);
                if($product->num < 0) {
                    $product->num = 0;
                }

                if(!$product->save()) {
                    $this->addErrors($product->getErrors());
                    break;
                }
            }
        }
        return !$this->hasErrors();
    }

    /**
     * 取消撤消申请
     * 变更撤消申请单状态
     * 变更入库单状态
     */
    private function cancelApply($repealId)
    {
        $repeal = tbWarrantRepeal::model()->findByPk($repealId);
        if (!$repealId) {
            return false;
        }

        $repeal->state = tbWarrantRepeal::STATE_REFUSE;
        if (!$repeal->save()) {
            $this->addErrors($repeal->getErrors());
            return false;
        }

        $warrant        = $repeal->warrant();
        $warrant->state = tbWarehouseWarrant::STATE_NORMAL;
        if (!$warrant->save()) {
            $this->addErrors($warrant->getErrors());
            return false;
        }

        return true;
    }
}
