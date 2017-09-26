<?php

class AjaxController extends CController
{
	public function init() {
		parent::init();

		header('Content-Type:application/json;charset=utf-8');
	}

    public function actionProduct()
    {
        $serial = Yii::app()->request->getQuery('q');
        if (!empty($serial)) {
            $condition = new CDbCriteria;
            $condition->addSearchCondition('serialNumber', $serial);
            $condition->limit = 10;
            $products = tbProduct::model()->findAll($condition);
            if ($products) {
            	$products = array_map(function($row){
            		return ['id'=>$row->productId, 'title'=>$row->serialNumber];
            	}, $products);
            }
            $msg = new AjaxData(true, '', $products);
        }
        else {
        	$msg = new AjaxData(false, 'nothing', []);
        }

        echo $msg->toJson();
    }

    public function actionSingle() {
        $serial = Yii::app()->request->getQuery('q');
        if (!empty($serial)) {
            $condition = new CDbCriteria;
            $condition->addSearchCondition('singleNumber', $serial);
            $condition->limit = 10;
            $products = tbProductStock::model()->findAll($condition);
            if ($products) {
                $products = array_map(function($row){
                    return ['id'=>$row->productId, 'title'=>$row->singleNumber];
                }, $products);
            }
            $msg = new AjaxData(true, '', $products);
        }
        else {
            $msg = new AjaxData(false, 'nothing', []);
        }

        echo $msg->toJson();
    }

    public function actionMember()
    {
        $serial    = Yii::app()->request->getQuery('q');
        $user_type = Yii::app()->request->getQuery('t',1);
        if (!empty($serial)) {
            $condition = new CDbCriteria;
            $condition->join = "left join {{member}} m using(memberId)";            
            $condition->compare('m.groupId', $user_type);

            if($user_type == 1) {
                $condition->addSearchCondition('username', $serial);
                $products = tbProfile::model()->findAll($condition);
                if ($products) {
                    $products = array_map(function($row){
                        return ['id'=>$row->memberId, 'title'=>$row->username];
                    }, $products);
                }
                $msg = new AjaxData(true, '', $products);
            }
            else {
                $condition->addSearchCondition('companyname', $serial);
                $products = tbProfileDetail::model()->findAll($condition);
            if ($products) {
                $products = array_map(function($row){
                    return ['id'=>$row->memberId, 'title'=>$row->companyname];
                }, $products);
            }
            $msg = new AjaxData(true, '', $products);
            }
                        
        }
        else {
        	$msg = new AjaxData(false, 'nothing', []);
        }

        echo $msg->toJson();
    }
}
