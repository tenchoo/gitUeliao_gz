<div class="panel panel-default search-panel">
   <div class="panel-body">
    <form role="search" class="pull-left form-inline" action="<?php echo $this->createUrl(Yii::app()->getController()->getAction()->id);?>">
     <div class="form-group">
      <input type="text" name="orderId" value="<?php echo $orderId;?>" placeholder="订单编号" class="form-control input-sm" />
	    <input type="text" name="singleNumber" value="<?php echo $singleNumber;?>" placeholder="产品编号" class="form-control input-sm" />
     </div>
     <button class="btn btn-sm btn-default">查找</button>
    </form>
   </div>
  </div>
  <div class="clearfix well well-sm list-well">
   <div class="pull-left form-inline">
    <!-- <label class="checkbox-inline"> <input type="checkbox" class="checkedall"/> 全选 </label>
    <button class="btn btn-sm btn-default">批量导出</button> -->
   </div>
  <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>
  <table class="table table-condensed table-bordered">
   <colgroup><col width="35%"/><col width="20%"/><col width="25%"/><col width="20%"/></colgroup>
   <thead>
    <tr>
     <td>产品编号</td>
     <td>颜色</td>
     <td>购买数量</td>
     <td>操作</td>
    </tr>
   </thead>
   </table>
   <br>
   <?php foreach(  $list as $val  ){ ?>
   <table class="table table-condensed table-bordered">
   <colgroup><col width="35%"/><col width="20%"/><col width="25%"/><col width="20%"/></colgroup>
   <tbody>

	<tr class="list-hd">
    <td colspan="5">
		订单编号：<?php echo $val['orderId'];?>
	  </td>
  </tr>
	<?php $count = count($val['products']);foreach( $val['products'] as $key=>$pval  ){ ?>
	 <tr class="list-bd">
	<td><?php echo $pval['singleNumber'];?></td>
     <td><?php echo $pval['color'];?></td>
     <td><?php echo Order::quantityFormat( $pval['num'] );?> <?php echo ZOrderHelper::getUnitName($pval['singleNumber'])?></td>
	  <?php if($key=='0'){?>
     <td rowspan="<?php echo $count;?>">
		<?php if($val['state'] == '0') { ?>
		<a href="<?php echo $this->createUrl('distribution',array('id'=>$val['orderId']));?>">分配</a>
		<?php }else{ ?>
		<a href="<?php echo $this->createUrl('view',array('id'=>$val['orderId']));?>">查看</a>
		<?php }?>
	 </td>
	 <?php }?>
    </tr>
   <?php }?>
   </tbody>
  </table>
  <br>
  <?php }?>
  <div class="clearfix well well-sm list-well">
    <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>