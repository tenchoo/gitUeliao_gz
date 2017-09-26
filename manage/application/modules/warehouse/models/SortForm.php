<?php
/**
 * 订单分拣确定
 * @author xiaomo
 * @version 0.2
 * @package CFormModel
 *
 */

class SortForm extends CFormModel {

  public $positionId = 0;

  public $positionTitle;

  public $productBatch;

  public $packingNum;

  public $unitRate;
 //辅助数量
  public $unit;
  // 整料数量
  public $int;
  //零码数量
  public $remainder;
  //单品编码
  public $singleNumber;

  /**
  * Declares the validation rules.
  * The rules state that account and password are required,
  * and password needs to be authenticated.
  */
  public function rules() {
    return array(
      array('positionId,packingNum,productBatch','required'),
      array('positionId', "numerical","integerOnly"=>true,'min'=>'1'),
      array('packingNum,unitRate', "numerical"),
      array('productBatch,positionTitle','safe'),
    );
  }

  public function attributeLabels() {
    return array(
      'packingNum' => '分拣数量',
      'positionId' => '仓位',
      'positionTitle'=>'仓位',
      'productBatch'=>'产品批次',
      'distributionNum'=>'分配数量',
      'unitRate'=>'单位换算值',
    );
  }

  /**
   * 分拣单列表 -- 后台
   * @param  array $condition 查找条件
   * @param  string $order  排序
   */
  public function search( $condition = array() ){
    $pageSize = tbConfig::model()->get( 'page_size' );
    $criteria = new CDbCriteria;
    if(!empty($condition)){
      foreach ($condition as $key=>$val){
        if($key =='state'){
            $criteria->compare('t.state',$val);
        }
        if( $val == '' ){
          continue ;
        }

        if( $key =='singleNumber' ){
          $criteria->compare('t.'.$key,$val,true);
        }else if( $key =='string' ){
          $criteria->addCondition($val);
        }else{
          $criteria->compare('t.'.$key,$val);
        }
      }
    }
    $criteria->order = $order;
    $model = new CActiveDataProvider('tbPack', array(
      'criteria'=>$criteria,
      'pagination'=>array('pageSize'=>$pageSize,'pageVar'=>'page'),
    ));
    $orders = new Order();
    $data = $model->getData();
    $return['list'] = array();
    if( $data ){
      foreach ( $data as $key=>$val) {
        $return['list'][$key] = $val->attributes;
        $return['list'][$key]['time'] = $this->getOrderData( $val->orderId )['createTime'];//订单时间
        $return['list'][$key]['method'] = $orders->deliveryMethod( $this->getOrderData($val->orderId)['deliveryMethod'] );//提货方式
        $return['list'][$key]['unit'] = $this->getOrderUnit($val->productId)['unitConversion'];//辅助单位
        $return['list'][$key]['unitname'] =$this->getOrderUnit($val->productId)['unitname'];
        $volume =$this->getVolume($val->num,$val->productId);
        $return['list'][$key]['int'] = $volume['int'];//整卷
        $return['list'][$key]['remainder'] = $volume['remainder'];//零码
        $return['list'][$key]['name'] = $this->getOrderData( $val->orderId )['name'];//客户姓名
        $return['list'][$key]['packUser'] = tbUser::model()->getUsername($val->packUserId);//分拣姓名
        $return['list'][$key]['memo'] = $this->getOrderData( $val->orderId )['memo'];//分拣姓名
         $WarehouseId = $this->getOrderData( $val->orderId )['warehouseId'];//发货仓库ID
         $return['list'][$key]['warehouse'] = tbWarehouseInfo::model()->getWarehouseInfoTitle($WarehouseId);//发货仓库
      }
    }
    $return['pages'] = $model->getPagination();
    return $return;
  }

  /**
  * @access 分拣调度
  */
  public function scheduling( $areas ){
    $positionId = Yii::app()->request->getPost('positionId');
    if( !array_key_exists( $positionId, $areas ) ){
      $this->addError( 'positionId',Yii::t('warning','Abnormal parameter') );
      return false;
    }

    $ids = explode(',',Yii::app()->request->getPost('ids') );
    if(  empty( $ids ) ){
      $this->addError( 'positionId',Yii::t('warehouse','No data') );
      return false;
    }
    foreach( $ids as $val ){
      if( (int)$val != $val && $val<1 ){
        $this->addError( 'positionId',Yii::t('warning','Abnormal parameter') );
        return false;
      }
    }

    tbPack::model()->updateByPk( $ids,array('positionId'=>$positionId),'state = 0' );
    return true;

  }

  /**
   * 确定分拣 -- 后台
   * @param  int $positionId
   * @param  int $orderProductId
   * @param  int $unit
   * @param  int $int
   * @param  string $remainder  排序
   */
  public function confirm($positionId,$orderProductId){

          $intPackingNum = bcmul($this->unit,$this->int);
          $remainderTotal= array_sum($this->remainder);
        //整料和零码的仓库相同时
        if($positionId[0] == $positionId[1]){
              $total = $this->getTotalNum($positionId[0]);//库存总量

              $lockTotal=$this->getLockTotalNum($positionId[0]);  //锁定库存总量
              $sortTotal = bcadd($intPackingNum,$remainderTotal);//分拣的总量
              $newTotal = bcadd($sortTotal,  $lockTotal);
              if($newTotal > $total){
                  echo '库存是不足，请添加产品后再分拣';die;
                  $errors = array('0'=>'库存是不足，请添加产品后再分拣');
                  return false;
                }
        }elseif($positionId[0] != $positionId[1]){
          //整料仓库
          $total1 = $this->getTotalNum($positionId[0]);//库存总量
          $lockTotal1=$this->getLockTotalNum($positionId[0]);  //锁定库存总量
          $newTotal1 = bcadd($intPackingNum,  $lockTotal1);
          if($newTotal1 > $total1){

                  $errors = array('0'=>'库存是不足，请添加产品后再分拣');
                  return false;
                   echo '库存是不足，请添加产品后再分拣';die;
                }
          //零码仓库
          $total2 = $this->getTotalNum($positionId[1]);//库存总量
          $lockTotal2=$this->getLockTotalNum($positionId[1]);  //锁定库存总量
          $newTotal2 = bcadd($remainderTotal,  $lockTotal2);
              if($newTotal2 > $total2){
                echo '库存是不足，请添加产品后再分拣';die;
                  $errors = array('0'=>'库存是不足，请添加产品后再分拣');
                  return false;
                }

        }

        //计算整数分拣量
        $packDetail = new tbPackDetail();
        $packDetail->positionId = $positionId[0];
        $packDetail->orderProductId = $orderProductId;
        $packDetail->positionTitle =tbWarehousePosition::model()->getWarehouseTitle($positionId[0]);
        $packDetail->packingNum = $intPackingNum;
        $packDetail->wholes = $this->unit;
        if( !$packDetail->validate() ){
            $errors = $packDetail->getErrors();
            return false;
          // throw new CHttpException( '404',$errors['positionId'][0] );
         }

        $transaction = Yii::app()->db->beginTransaction();
         //添加整料
        if( !$packDetail->save() ){
            $transaction->rollback();
            $this->addErrors( $packDetail->getErrors() );
            return false;

        }
        $packDetail = new tbPackDetail();
       //添加零码
         foreach ( $this->remainder as $key => $value ) {
             $_packDetail = clone $packDetail;
             $_packDetail->positionId = $positionId[1];
             $_packDetail->orderProductId = $orderProductId;
             $_packDetail->positionTitle =tbWarehousePosition::model()->getWarehouseTitle($positionId[1]);
             $_packDetail->wholes = 0;
             $_packDetail->packingNum = $value;
            if(!$_packDetail->save()){
                 $transaction->rollback();
                 $this->addErrors( $_packDetail->getErrors() );
                 return false;
              }
          }

         //保存分拣员、改变状态
        if( !$this->packUser( $orderProductId )  ) {
            $transaction->rollback();
            return false;
        }
         //锁定仓库产品
        if($positionId[0] != $positionId[1]){
            if( !$this->warehouseLock( $orderProductId,$positionId[0],$intPackingNum ) ){
                 $transaction->rollback();
                 return false;
              }
            if(!$this->warehouseLock( $orderProductId,$positionId[1],$remainderTotal ) ){
                 $transaction->rollback();
                 return false;
              }

        }else{
            $num = bcadd( $intPackingNum,$remainderTotal );//分拣的总量
            if(!$this->warehouseLock( $orderProductId,$positionId[0],$num )){
                 $transaction->rollback();
                 return false;
             }
        }

        //写入产品的分拣数量
        if( !$this->product( $orderProductId ) ){
              $transaction->rollback();
              return false;
        }

         $transaction->commit();

         //打印订单
         $pack = tbPack::model()->findByPk($orderProductId);
       //  PrintPush::printOrderTag( $pack->orderId,$msg );
         //保存到归单订单
         if( !$this->orderMerge( $orderProductId,$positionId ) ){
             return false;
         }

         return true;
  }

  /**
   * 保存分拣员和改变状态、时间
   * @param  int $orderProductId 订单明细ID
   */
  public function packUser( $orderProductId ){
      $pack = tbPack::model()->findByPk( $orderProductId );
      $pack->packUserId = Yii::app()->user->id;
      $pack->state = 1;
      $pack->packTime = date("Y-m-d H:i:s");
      if( !$pack->save()  ) {
          $this->addErrors( $pack->getErrors() );
          return false;
        }
      return true;
  }

  /**
   * 保存归单订单
   * @param  int $orderProductId 订单明细ID
   * @param  int $positionId 分拣仓库ID
   */
  public function orderMerge( $orderProductId,$positionId ){
      $merge = tbPack::model()->findAll(array(
                'condition' => 'state =:state AND orderId=:orderId',
                'params' => array(':state'=>0,':orderId' => $orderId),
              ));
      $pack = tbPack::model()->findByPk($orderProductId);
      $order = new tbOrderMerge();
      $warehouse1 = tbWarehousePosition::model()->getWarehouseID($positionId[0]);
      $warehouse2 = tbWarehousePosition::model()->getWarehouseID($positionId[1]);
      if(!$merge){
        if( $warehouse1 == $warehouse2 ){
            $order->orderId = $pack->orderId;
            $order->warehouseId = $warehouse1;
            if( !$order->save()  ) {
              $this->addErrors( $order->getErrors() );
              return false;
            }
        }else{
                $order->orderId = $pack->orderId;
                $order->warehouseId = $warehouse1;
                if( !$order->save()  ) {
                  $this->addErrors( $order->getErrors() );
                  return false;
                }
                $order->orderId = $pack->orderId;
                $order->warehouseId = $warehouse2;
                if( !$order->save()  ) {
                  $this->addErrors( $order->getErrors() );
                  return false;
                }
             }
        }
          return true;

      }
      $pack->packUserId = Yii::app()->user->id;
      $pack->state = 1;
      if( !$pack->save()  ) {
          $this->addErrors( $pack->getErrors() );
          return false;
        }
      return true;
  }

  /**
   * 保存到仓库产品锁定信息
   * @param  int $orderProductId 订单明细ID
   * @param  int $positionId 分区ID
   * @param  int $num 分拣数量
   */
  public function warehouseLock( $orderProductId,$positionId,$num ){
      $lock = new tbWarehouseLock();
      $pack = tbPack::model()->findByPk($orderProductId);
      $warehouseId = tbWarehousePosition::model()->getWarehouseID($positionId);
      $lock->sourceId = $orderProductId;
      $lock->orderId = $pack->orderId;
      $lock->warehouseId = $warehouseId;
      $lock->positionId = $positionId;
      $lock->productId = $pack->productId;
      $lock->num = $num;
      $lock->type = tbWarehouseLock::TYPE_PACKING;
      $lock->singleNumber = $pack->singleNumber;
      $lock->productBatch = tbWarehouseProduct::DEATULE_BATCH;
      if( !$lock->save()  ) {
          $this->addErrors( $lock->getErrors() );
          return false;
        }
      return true;
  }

  /**
   * 写入产品的分拣数量
   * @param  int $orderProductId 订单明细ID
   */
  public function product( $orderProductId ){
        $product = tbOrderProduct::model()->findByPk($orderProductId);
        $intPackingNum = bcmul($this->unit,$this->int);
        $remainderTotal= array_sum($this->remainder);
        $num = bcadd($intPackingNum, $remainderTotal);
        $product->num = $num;
        $product->remark = $this->int.'*'.$this->unit.','.$remainderTotal;
        if( !$product->save()  ) {
          $this->addErrors( $product->getErrors() );
          return false;
        }

      return true;
  }

  /**
  * 取得仓库管理员所管理的区域IDs
  */
  public function ManageWarehouse(){
    $userId = Yii::app()->user->id;
    if( empty( $userId ) ) return;

    $model = tbWarehouseUser::model()->find('userId=:userId',array(':userId'=>$userId ) );
    if( $model ){
      return $model->warehouseId;
    }
  }

  /**
  * 取得订单时间、订单提货方式、客户姓名
  * @param  int $orderId  订单id
  */
  public function getOrderData($orderId){
    $model = tbOrder::model()->findByPk($orderId);
    if( $model ){
      $data['createTime'] = $model->createTime;
      $data['deliveryMethod'] =$model->deliveryMethod;
      $data['name'] =$model->name;
      $data['memo'] =$model->memo;
      $data['warehouseId'] = $model->warehouseId;
      return $data;
    }
    return '';
  }
  /**
  * 取得产品辅助单位(单位换算量和单位)
  * @param  int $productId  产品id
  */
  public function getOrderUnit($productId){
    $model = tbProduct::model()->findByPk($productId);

    if( $model ){
      $data['unitConversion'] = $model->unitConversion;//单位换算量
      $auxiliaryUnit = $model->auxiliaryUnit;
      $data['unitname'] = tbUnit::model()->getUnitName($auxiliaryUnit);
      return  $data;
    }
    return '';
  }

  /**
  * 计算整卷和零码
  * @param  int $productId  产品id
  */
  public function getVolume($num,$productId){
    $unit = $this->getOrderUnit($productId)['unitConversion'];
    $volume['int'] = bcdiv($num,$unit,0);//整卷
    $volume['remainder'] = bcmod($num,$unit);//零码
    return $volume;
  }

   /**
   * 查询产品的仓库量
   * @param int $positionId 分区id
   * @param string $singleNumber 单品编码
   * @param return boolean
   */
  public function getTotalNum($positionId){
    $model =tbWarehouseProduct::model()->findByAttributes(array('positionId'=>$positionId,'singleNumber' =>$this->singleNumber));
    if( $model ) {
      return $model->num;
    }
    return '';
  }
  /**
   * 查询产品的仓库量
   * @param int $positionId 分区id
   * @param string $singleNumber 单品编码
   * @param return boolean
   */
  public function getLockTotalNum($positionId){

      $model = tbWarehouseLock::model()->findAll(array(
                'select' =>array('num'),
                'condition'=>'positionId=:positionId AND singleNumber=:singleNumber',
                'params' => array(':positionId'=>$positionId,':singleNumber'=> $this->singleNumber),
              ));
    if( $model ) {
       foreach ($model as $key => $value) {
           $count[$key]= $value->num;
       }
       $total = array_sum($count);
      return  $total;

    }
    return 0;
  }



}