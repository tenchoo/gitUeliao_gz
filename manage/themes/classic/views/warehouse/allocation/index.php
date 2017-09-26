<div class="panel panel-default search-panel">
  <div class="panel-body">
  <form action="<?php echo $this->createUrl(Yii::app()->getController()->getAction()->id);?>" class="pull-left form-inline">
	<input type="text" value="<?php echo $orderId;?>" name="orderId" class="form-control input-sm" placeholder="订单编号"/>
	<input type="text" value="<?php echo $allocationId;?>" name="allocationId" class="form-control input-sm" placeholder="调拨单号"/>
<?php if( $state != '0' ){ ?>
	<input type="text" value="<?php echo $userName;?>" name="userName" class="form-control input-sm" placeholder="调拨人"/>
	<input type="text" value="<?php echo $createTime;?>" name="createTime" class="form-control input-sm" placeholder="调拨时间"/>
<?php }?>
	<button class="btn btn-sm btn-default">查找</button>
	</form>
  <?php if($state != '1') { ?>
   <div class="pull-right">
    <a class="btn btn-sm btn-default" href="<?php echo $this->createUrl('add');?>">新建调拨单</a>
	</div>
  <?php }?>
  </div>
</div>
<div class="clearfix well well-sm list-well">
<?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
</div>
 <table class="table table-condensed table-bordered order">
  <colgroup><col><col width="12%"><col width="12%"><col width="12%"><col width="12%"><col width="10%"></colgroup>
  <thead>
    <tr>
    <td>产品编号</td>
	  <td>颜色</td>
	  <td>数量</td>
	  <td>产品批次</td>
	  <td>调拨数量</td>
    <td>操作</td>
    </tr>
  </thead>
  </table>
 <?php foreach( $list as $item ): $k=0;?>
  <br/>
  <table class="table table-condensed table-bordered">
  <colgroup><col><col width="12%"><col width="12%"><col width="12%"><col width="12%"><col width="10%"></colgroup>
  <tbody>
	<tr class="list-hd">
	  <td colspan="8">
		<span class="first">订单编号：<?php echo ($item['orderId'])?$item['orderId']:'0000';?></span>
		<span>调拨单号：<?php echo $item['allocationId'];?></span>
		<span>原仓库：<?php echo $item['warehouse'];?></span>
		<span>目标仓库：<?php echo $item['targetWarehouse'];?></span>
	  </td>
	  </tr>
	<?php foreach( $item['detail'] as $detail ): $i = 0; $c = count($detail);?>
	<?php foreach( $detail as $val ): $k++;$i++;?>
    <tr>
	<?php if($i == '1') { ?>
	 <td rowspan="<?php echo $c;?>"><?php echo $val['singleNumber'];?></td>
	 <td rowspan="<?php echo $c;?>"><?php echo $val['color'];?></td>
	 <td rowspan="<?php echo $c;?>"><?php echo Order::quantityFormat( $val['total'] ),$val['unit'];?></td>
	<?php } ?>
	  <td><?php echo $val['productBatch'];?></td>
	  <td><?php echo Order::quantityFormat( $val['num'] );?><?php echo $val['unit'];?></td>
	  <?php if( $k == '1' ) { ?>
      <td rowspan="<?php echo $item['rowspan'];?>">
		 <?php if($state == '0') { ?>
		 <a href="<?php echo $this->createUrl('confirmation',array('id'=>$item['allocationId']) );?>">确定调拨</a>
		 <?php }else if($state == '1') { ?>
		 <a href="<?php echo $this->createUrl('receipt',array('id'=>$item['allocationId']) );?>">确定收货</a>
		 <?php }else{ ?>
		 <a href="<?php echo $this->createUrl('view',array('id'=>$item['allocationId']) );?>">查看</a><br>
		 <?php if( !empty( $item['orderId'] ) && $item['isCallback'] == '0' && in_array( $item['orderId'],$closed ) ){ ?>
		  <a href="<?php echo $this->createUrl('callback',array('id'=>$item['allocationId']) );?>">回调</a><br>		 
		 <?php }} ?>
      </td>
	  <?php } ?>
    </tr>
	 <?php endforeach; ?>
	 <?php endforeach; ?>
  </tbody>
</table>
<?php endforeach; ?>
<br>
<div class="clearfix well well-sm list-well">
<?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
</div>
<?php $this->beginContent('//layouts/_del');$this->endContent();?>