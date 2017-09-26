<link rel="stylesheet" href="/themes/classic/statics/app/order/css/style.css" />
  <div class="panel panel-default search-panel">
   <div class="panel-body">
    <form role="search" class="pull-left form-inline" action="<?php echo $this->createUrl('index');?>">
	申请时间:  <input type="text" name="createTime1" value="<?php echo $createTime1; ?>" class="form-control input-sm input-date" id="starttime" readonly/>
		到 <input type="text" name="createTime2" value="<?php echo $createTime1; ?>" class="form-control input-sm input-date" id="endtime" readonly/>
     <div class="form-group">
      <input type="text" name="orderId" value="<?php echo $orderId;?>" placeholder="请输入订单编号" class="form-control input-sm" />
     </div>
     <button class="btn btn-sm btn-default">查找</button>
    </form>
   </div>
  </div>
  <div class="clearfix well well-sm list-well">
  <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>
  <table class="table table-condensed table-bordered">
   <colgroup><col width="15%"/><col width="15%"/><col width="10%"/><col width="15%"/><col width="15%"/><col width="15%"/><col width="15%"/></colgroup>
   <thead>
    <tr>
     <td>产品编号</td>
	 <td>颜色</td>
     <td>数量(码)</td>
     <td>单价(元)</td>
	 <td>申请价格(元)</td>
	 <td>状态</td>
     <td>操作</td>
    </tr>
   </thead>
   </table>
   <br>
   <?php foreach(  $list as $val  ){?>
   <table class="table table-condensed table-bordered">
   <colgroup><col width="15%"/><col width="15%"/><col width="10%"/><col width="15%"/><col width="15%"/><col width="15%"/><col width="15%"/></colgroup>
   <tbody>
	<tr class="list-hd <?php if($val['oType'] == '1'){ ?>book<?php }else if($val['oType'] == '2'){ ?>obligate<?php }else if($val['oType'] == '3'){ ?>tailproduct<?php } ?>">
    <td colspan="7">
		<span class="first"><?php echo $val['orderType']?></span>
		<span>订单编号：<?php echo $val['orderId'];?></span>
	  </td>
  </tr>
	<?php $count = count($val['products']);foreach( $val['products'] as $key=>$pval  ){ ?>
	 <tr class="list-bd">
   <td><?php echo $pval['singleNumber'];?></td>
    <td><?php echo $pval['color'];?></td>

   <td> <?php echo Order::quantityFormat($pval['num']);?>  <?php echo isset($units[$pval['productId']])?$units[$pval['productId']]['unit']:'';?></td>
   <td><?php echo Order::priceFormat($pval['salesPrice']);?></td>
   <td><?php echo Order::priceFormat($pval['applyprice']);?></td>
	 <?php if($key=='0'){?>
	<td rowspan="<?php echo $count;?>" >
		<?php echo $val['stateTitle'];?>
	 </td>
     <td rowspan="<?php echo $count;?>">
		<?php if($val['state'] == '0'){?>
		<a href="<?php echo $this->createUrl('check',array('id'=>$val['id']));?>">价格审核</a>
		<?php }else{?>
		<a href="<?php echo $this->createUrl('view',array('id'=>$val['id']));?>">查看</a>
		<?php }?>
	 </td>
	 <?php } ?>
    </tr>
	  <?php } ?>
	  </tbody>
  </table>
  <br>
   <?php }?>

  <div class="clearfix well well-sm list-well">
    <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>
<script>seajs.use('statics/app/order/js/applypricelist.js');</script>