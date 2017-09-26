<link rel="stylesheet" href="/themes/classic/statics/app/warehouse/css/style.css" />
  <div class="panel panel-default search-panel">
   <div class="panel-body">
    <form role="search" class="pull-left form-inline" action="<?php echo $this->createUrl(Yii::app()->getController()->getAction()->id);?>">
     <div class="form-group">
      <input type="text" name="orderId" value="<?php echo $orderId;?>" placeholder="订单编号" class="form-control input-sm" />
     </div>
     <button class="btn btn-sm btn-default">查找</button>
    </form>
   </div>
  </div>
   <div class="clearfix well well-sm list-well">
   <div class="pull-left form-inline">
    <label class="checkbox-inline"> <input type="checkbox" class="checkedall"/> 全选 </label>
    <button class="btn btn-sm btn-default"  data-toggle="modal" data-target=".setarea-confirm" title="批量分配归单员" >批量分配归单员</button>
   </div>
   <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>
  <table class="table table-condensed table-bordered ">
  <colgroup><col width="50px"><col width="20%"><col><col></colgroup>
   <thead>
    <tr>
	<td></td>
	 <td>订单编号</td>
     <td>下单时间</td>
     <td>分配归单员</td>	 
	 <td></td>
    </tr>
   </thead>
    <tbody>
	 <?php foreach(  $list as $val  ){ ?>
	 <tr>
	 <td><input type="checkbox" value="<?php echo $val['id'];?>" name="id[]"/></td>
	 <td><?php echo $val['orderId'];?></td>
     <td><?php echo $val['orderTime'];?></td>
     <td><?php echo isset($users[$val['mergeUserId']])?$users[$val['mergeUserId']]:'';?></td>
	  <td>
	  <a href="<?php echo $this->createUrl('print',array('id'=>$val['orderId']));?>" class="print">打印备货单</a></td>
    </tr>
	 <?php }?>
	</tbody>
   </table>
   <br>
	<div class="clearfix well well-sm list-well">
   <div class="pull-left form-inline">
    <label class="checkbox-inline"> <input type="checkbox" class="checkedall"/> 全选 </label>
    <button class="btn btn-sm btn-default"  data-toggle="modal" data-target=".setarea-confirm" title="批量分配归单员" >批量分配归单员</button>
   </div>
   <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>
  <script>seajs.use('statics/app/warehouse/js/scheduling.js');</script>
   <script>seajs.use('statics/app/order/js/applypricelist.js');</script>

 <div class="modal fade setarea-confirm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="exampleModalLabel">批量分配归单员</h4>
      </div>
      <div class="modal-body">
        <form action="" method="post">
			<input type="hidden" value="" name="ids"/>
			<input type="hidden" value="scheduling" name="action"/>
			<?php echo CHtml::dropDownList('userId','',$users,array('class'=>'form-control input-sm'))?>
		</form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
        <button type="button" class="btn btn-success">确定</button>
      </div>
    </div>
  </div>
</div>