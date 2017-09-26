<div class="panel panel-default search-panel">
   <div class="panel-body">
    <form role="search" class="pull-left form-inline" action="<?php echo $this->createUrl(Yii::app()->getController()->getAction()->id);?>">
     <div class="form-group">
      <input type="text" name="orderId" value="<?php echo $condition['orderId'];?>" placeholder="订单编号" class="form-control input-sm" />
	   <input type="text" name="singleNumber" value="<?php echo $condition['singleNumber'];?>" placeholder="产品编号" class="form-control input-sm" />
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
  <table class="table table-condensed table-bordered order">
   <thead>
    <tr>
     <td >产品编号</td>
     <td  width="25%">颜色</td>
     <td  width="25%">数量</td>
     <td width="15%">操作</td>
    </tr>
   </thead>
   <tbody>
    </table>
   <?php foreach(  $list as $val  ){?>
   <br/>
   <table class="table table-condensed table-bordered">
	<tr class="list-hd">
    <td colspan="5">
		<span class="first">订单编号：<?php echo $val['orderId'];?></span>
	  </td>
  </tr>
	<?php $count = count($val['products']);foreach( $val['products'] as $key=>$pval  ){ ?>
	 <tr class="order-list-bd">
	<td><?php echo $pval['singleNumber'];?></td>
     <td width="25%"><?php echo $pval['color'];?></td>
	 <td width="25%"><?php echo Order::quantityFormat( $pval['num'] );?></td>
	  <?php if($key=='0'){?>
     <td rowspan="<?php echo $count;?>" width="15%">
		<a href="<?php echo $this->createUrl('delivery',array('id'=>$val['orderId']));?>">发货</a>
	 </td>
	 <?php }?>
    </tr>
   <?php }?>
   </tbody>
  </table>
   <?php }?>
   <br>
  <div class="clearfix well well-sm list-well">
    <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>