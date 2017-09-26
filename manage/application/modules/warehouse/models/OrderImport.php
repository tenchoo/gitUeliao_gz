<?php
/**
 * Created by PhpStorm.
 * User: yagas
 * Date: 2015/11/23
 * Time: 10:00
 */


class OrderImport extends CModel {

    private $warrant;
    private $detailWarrant = array();
    private $_isNewRecord = true;

    public function attributeNames() {
        return array();
    }

    public function __construct( $warrantId=null ) {
        if( is_null($warrantId) ) {
            $this->warrant = new tbWarehouseWarrant();
        }
        else {
            $warrant = tbWarehouseWarrant::model()->findByPk( $warrantId );
            if( is_null($warrant) ) {
                throw new CHttpException( 404, 'Not found record' );
            }
            $this->_isNewRecord = false;

            $this->detailWarrant = tbWarehouseWarrantDetail::model()->findAllByAttributes( array('warrantId'=>$warrantId) );
        }
    }

    /**
     * 设置入库单属性
     * @param array $propertyArr 入库单属性
     */
    public function setWarrant( $propertyArr ) {
        if( is_array($propertyArr) ) {
            return $this->warrant->setAttributes( $propertyArr );
        }
        elseif( $propertyArr instanceof tbOrderbuy ){
            return $this->warrant->setAttributes( $propertyArr->getAttributes(array('factoryNumber','factoryName','contacts','phone','address','comment')) );
        }

        return false;
    }

    /**
     * 获取入库单明细
     * @return array
     */
    public function getDetails() {
        return $this->detailWarrant;
    }

    /**
     * 设置入库单明细信息
     @param array $dataArr tbWarehouseWarrantDetail数组
     * @return bool
     */
    public function setDetails( $dataArr ) {
        $hasError = true;
        if( !is_array($dataArr) ) {
            return false;
        }

        foreach( $dataArr as $item ) {
            if( $item instanceof tbWarehouseWarrantDetail ) {
                array_push( $this->detailWarrant, $item );
            }
            else {
                $hasError = false;
            }
        }

        return $hasError;
    }

    /**
     * 入库单信息保存
     * @return bool
     * @throws CDbException
     */
    public function save() {
        if( !$this->warrant->save() ) {
            $this->addErrors( $this->warrant->getErrors() );
            return false;
        }

        $this->delOrgDetail();

        foreach( $this->detailWarrant as $dWarrant ) {
            $dWarrant->warrantId = $this->warrant->warrantId;
            if( !$dWarrant->save() ) {
                $this->addErrors( $dWarrant->getErrors() );
                return false;
            }
        }

        $this->_isNewRecord = false;
        return true;
    }
	
	/**
     * 取得入库单号
     */
    public function getWarrantId() {
		return $this->warrant->warrantId;
    }
	
    
    public function noticeToDistribution( $postId ) {
    	$orderIds = tbOrderPostProduct::model()->findAllOrderIds( $postId );
    	if( $orderIds ) {
    		foreach ( $orderIds as $id ) {
    			$order = tbOrder::model()->findByPk( $id );
    			if( !$order ) {
    				continue;
    			}
    			tbOrderDistribution::addOne( $id );
    		}
    	}
    }

    /**
     * 更新入库单前清理原有明细信息
     * @return bool
     */
    private function delOrgDetail() {
        if( $this->_isNewRecord ) {
            return false;
        }

        $warrantId = $this->warrant->warrantId;
        tbWarehouseWarrantDetail::model()->deleteAllByAttributes( array('warrantId'=>$warrantId) );
    }
}