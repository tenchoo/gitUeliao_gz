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
   <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>
  <table class="table table-condensed table-bordered ">
  <colgroup><col width="20%"><col width="20%"><col><col><col></colgroup>
   <thead>
    <tr>
	 <td>订单编号</td>
	 <td>客户</td>
     <td>下单时间</td>
	 <td>发货仓库</td>
	 <td>配送方式</td>
     <td>操作</td>
    </tr>
   </thead>
    <tbody>
	 <?php foreach(  $list as $val  ){ ?>
	 <tr>
	 <td><?php echo $val['orderId'];?></td>
     <td><?php echo $val['companyname'];?></td>
     <td><?php echo $val['orderTime'];?></td>
	 <td><?php echo $val['Dwarehouse'];?></td>
	 <td><?php echo $val['deliveryMethod'];?></td>
	 <td>
		<a href="<?php echo $this->createUrl( 'view',array('id'=>$val['id']) );?>">查看</a>&nbsp;&nbsp;
		<a href="<?php echo $this->createUrl( 'delivery',array('id'=>$val['id']) );?>">发货</a>
		<?php if( $val['state'] == tbOrderMerge::STATE_DONE && $val['warehouseId'] != $val['sortHouseId'] ){ ?>
		&nbsp;&nbsp;
		<a href="<?php echo $this->createUrl( 'allocation',array('id'=>$val['id']) );?>">调拨</a>
		<?php }?>
	</td>
    </tr>
	 <?php }?>
	</tbody>
   </table>
   <br>
	<div class="clearfix well well-sm list-well">
   <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>