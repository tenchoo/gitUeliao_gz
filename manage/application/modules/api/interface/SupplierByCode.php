<?php
/**
 * 获取供应商信息
 * Created by PhpStorm.
 * User: yagas-office
 * Date: 2015/12/17
 * Time: 12:46
 */
class SupplierByCode extends CAction {

    public function run() {
        $code = Yii::app()->request->getQuery('code');
        if( empty($code) ) {
            return new AjaxData(false,"nothing");
        }

        $result = tbSupplier::model()->findByPk( $code );
        if( is_null($result) ) {
            return new AjaxData(false,"nothing");
        }

        return new AjaxData(true,null,$result->getAttributes());
    }
}