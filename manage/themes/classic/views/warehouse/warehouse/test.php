 <?php $this->beginContent('//layouts/_error');$this->endContent();?>
<form class="form-horizontal modifydetail" method="post">
<div class="form-group">
  <label class="control-label col-md-1" for="">仓库名称：</label>
  <div class="col-md-4 "><p class="form-control-static"><?php echo $data->title;?></p></div>
  <input type="hidden" name="data[warehouseId]" value=" <?php echo $data->warehouseId;?>">
</div>
<div class="form-group">
  <label class="control-label col-md-1" for="">分区：</label>
  <div class="col-md-2 "><?php echo CHtml::dropDownList('data[positionId]','',$position,array('class'=>'form-control input-sm','empty'=>'请选择分区'))?></div>

</div>

<br>
  <table class="table table-condensed table-bordered import">
    <thead>
    <tr>
      <td width="35%">分拣员</td>
      <td>是否设置为负责人</td>
       <td>操作</td>
    </tr>
    </thead>

    <tbody>
      <tr>
        <td><?php echo CHtml::dropDownList('warehouse[0][userId]','',$user,array('class'=>'form-control input-sm','empty'=>'请选择分拣员'))?></td>
        <td>
          <label class="radio-inline">
        <input class="radio" name="warehouse[0][isManage]" value="0" type="radio" checked="checked" ><span>否</span>
        </label>
         <label class="radio-inline">
        <input class="radio" name="warehouse[0][isManage]" value="1" type="radio" >是
       </label></td>
       <td></td>
      </tr>
    </tbody>
    <tfoot>
   <tr>
      <td colspan="3" align="center"><a href="javascript:" data-templateid="importlist">添加分拣员</a></td>
    </tr>
    </tfoot>
  </table>


 <br>

<div class="form-group " align='center'>
    <button class="btn btn-success">保存</button>

</div>
</form>

<script type="text/html" id="importlist">
  <tr id="J_{{id}}">
        <td><?php echo CHtml::dropDownList('warehouse[{{id}}][userId]','',$user,array('class'=>'form-control input-sm','empty'=>'请选择分拣员'))?>
        </td>
         <td>
          <label class="radio-inline">
        <input class="radio" name="warehouse[{{id}}][isManage]" value="0" type="radio" checked="checked" ><span>否</span>
        </label>
         <label class="radio-inline">
        <input class="radio" name="warehouse[{{id}}][isManage]" value="1" type="radio" >是
       </label></td>
        <td><a href="javascript:" class="del">删除</a></td>
      </tr>
</script>

 <script>seajs.use('statics/app/warehouse/js/user.js');</script>
<script>
  seajs.use('statics/app/warehouse/js/warehouse2.js');
</script>



<?php

$positionId = Yii::app()->request->getQuery( 'positionId' );
    // $wModel = tbWarehouseInfo::model()->findByPk( $warehouseId,'state = 0' );
    // if( !$wModel ) {
    //  $this->redirect( $this->createUrl( 'index' ) );
    // }
    //取得仓库下面分区
  //  $position = tbWarehousePosition::model()->getAllWarehouse($warehouseId);

    if( Yii::app()->request->isPostRequest ) {
      $warehouse = Yii::app()->request->getPost( 'warehouse' );
      $data = Yii::app()->request->getPost( 'data' );
      $warehouseUser = new tbWarehouseUser();
      $count = 0;
      foreach ($warehouse as $key => $value) {
           //判断分拣员是否已经存在了
            if( $warehouseUser->getuser( $value['userId'] ) ){
                $errors = $warehouseUser->getErrors();
               $this->dealError($errors);
               // throw new CHttpException('404','该分拣员已经存在了' );
            }
            if($value['isManage'] == 1){
            //判断负责人是否已经存在了
             if($warehouseUser->getManage( $data['positionId'] ) ){
                   throw new CHttpException( '404','负责人已经存在了' );
              }
             $count += $count + 1;
          }
      }
     //一个分区负责人只能设置一个
      if(is_numeric( $count ) && $count > 1){
         throw new CHttpException( '404','只能设置一个负责人' );
      }
      foreach ( $warehouse as $val ){
         $_warehouse = clone $warehouseUser;
         $_warehouse->warehouseId = $data['warehouseId'];
         $_warehouse->positionId = $data['positionId'];
         $_warehouse->userId = $val['userId'];
         $_warehouse->isManage = $val['isManage'];
      //     if( !$_warehouse->save()  ) {
            // $this->addErrors( $_warehouse->getErrors() );
          //  }
        }
        $this->dealSuccess( $this->createUrl('index') );
    }

    //查询所有用户
    $user = tbUser::model()->getAll();
    $this->render( 'user', array( 'data'=>$wModel,'user'=>$user,'position'=>$position ) );

?>