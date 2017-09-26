<link rel="stylesheet" href="/themes/classic/statics/app/warehouse/css/style.css" />
  <div class="panel panel-default search-panel">
   <div class="panel-body">
    <form role="search" class="pull-left form-inline" action="<?php echo $this->createUrl(Yii::app()->getController()->getAction()->id);?>">
     <div class="form-group">
      <input type="text" name="orderId" value="<?php echo $orderId;?>" placeholder="订单编号" class="form-control input-sm" />
	    <input type="text" name="singleNumber" value="<?php echo $singleNumber;?>" placeholder="产品编号" class="form-control input-sm">
     </div>
     <button class="btn btn-sm btn-default">查找</button>
    </form>
   </div>
  </div>
   <div class="clearfix well well-sm list-well">
   <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>
  <table class="table table-condensed table-bordered ">
  <colgroup><col width="20%"><col width="20%"><col><col><col></colgroup>
   <thead>
    <tr>
	 <td>订单编号</td>
     <td>产品编号</td>
	 <td>分拣数量</td>
     <td>数量明细</td>
	 <td>归单人</td>
	 <td>归单时间</td>
    </tr>
   </thead>
    <tbody>
	 <?php foreach(  $list as $val  ){ ?>
	 <tr>
	 <td><?php echo $val['orderId'];?></td>
     <td><?php echo $val['singleNumber'];?></td>
	  <td><?php echo $val['packNum'];?></td>
     <td><?php echo $val['remark'];?></td>
	 <td><?php echo tbUser::model()->getUsername( $val['mergeUserId'] );?></td>
	 <td><?php echo $val['mergeTime'];?></td>
    </tr>
	 <?php }?>
	</tbody>
   </table>
   <br>
	<div class="clearfix well well-sm list-well">
   <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>