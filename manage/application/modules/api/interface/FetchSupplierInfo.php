<?php
/**
 * 通过供应商编码或名称获取供应商列表
 * Created by PhpStorm.
 * User: yagas-office
 * Date: 2015/12/17
 * Time: 11:30
 */

class FetchSupplierInfo extends CAction {

    public function run() {
        $key = "factoryNumber";
        $search = $code = Yii::app()->request->getQuery('code');
        $name = Yii::app()->request->getQuery('name');

        if( empty($code) ) {
            $key = "shortname";
            $search = $name;
        }

        if( empty($search) ) {
            return new AjaxData(false,"nothing");
        }

        $tableName = tbSupplier::model()->tableName();
        $sql = "select supplierId,factoryNumber,shortname,contact,phone,adddress from {$tableName} where {$key} like :search";
        $cmd = Yii::app()->getDb()->createCommand($sql);
        $cmd->bindValue(':search', "%{$search}%", PDO::PARAM_STR);
        $result = $cmd->queryAll();

        $rows = array();
        foreach( $result as $item ) {
            $row = array('id'=>$item['supplierId'],'code'=>$item['factoryNumber'],'title'=>$item['shortname'],'contact'=>$item['contact'],'phone'=>$item['phone']);
            array_push( $rows, $row );
        }
        return new AjaxData(true,null,$rows);
    }
}