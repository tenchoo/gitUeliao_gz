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
  <colgroup><col width="15%"><col width="15%"><col><col><col><col></colgroup>
   <thead>
    <tr>
	 <td>订单编号</td>
     <td>产品编号</td>
	 <td>购买数量</td>	 
     <td>分拣数量</td>
	 <td>分拣说明</td>
	 <td>分区</td>
	 <td>分拣人</td>
	 <td>分拣时间</td>
	 <td>状态</td>
	 <td>
    </tr>
   </thead>
    <tbody>
	 <?php foreach(  $list as $val  ){ ?>
	 <tr>
	 <td><?php echo $val['orderId'];?></td>
     <td><?php echo $val['singleNumber'];?>&nbsp;<?php echo $val['color'];?></td>
	  <td><?php echo $val['num'];?></td>
	  <td><?php echo $val['packNum'];?></td>
     <td><?php echo $val['remark'];?></td>
	  <td><?php echo (isset($areas[$val['positionId']]))?$areas[$val['positionId']]:'';?></td>
	 <td><?php echo tbUser::model()->getUsername( $val['packUserId'] );?></td>
	 <td><?php echo $val['packTime'];?></td>
	  <td><?php if( $val['state'] == '2' ){ ?>
		<span class="alert-success">归单完成</span>
	  <?php }else{ ?>
	    <span class="alert-info">分拣完成</span>
	  <?php }?></td>
	  <td>
	  <a href="<?php echo $this->createUrl('printtag',array('id'=>$val['orderProductId']));?>" class="print">打印条码</a></td>
    </tr>
	 <?php }?>
	</tbody>
   </table>
   <br>
	<div class="clearfix well well-sm list-well">
   <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>
 <script>seajs.use('statics/app/order/js/applypricelist.js');</script>