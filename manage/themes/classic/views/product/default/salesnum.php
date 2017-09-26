<link rel="stylesheet" href="/themes/classic/statics/app/order/css/style.css" />
<link rel="stylesheet" href="/themes/classic/statics/app/warehouse/css/style.css" />
 <div class="panel panel-default search-panel">
   <div class="panel-body">
    <form class="pull-left form-inline" action="<?php echo $this->createUrl('salesnum');?>">
	  <input type="text" class="form-control input-sm" name="s"  placeholder="请输入单品编号" value="<?php echo $s;?>" data-suggestion="s" data-search="serial=%s" data-api="/api/search_product_serial" autocomplete="off"/>
     <button class="btn btn-sm btn-default" id="btn-add" >查询</button>
    </form>
   </div>
  </div>

 <?php if( !empty( $s ) ){ ?>
 <?php if( !isset( $safety )){ ?>
	 <div class="panel panel-default">
	<div class="panel-heading clearfix">
	  <span class="col-md-4">编号：<?php echo $s;?>的产品不存在</span>
	</div>
	</div>
  <?php }else {  ?>
  <div class="panel panel-default">
	<div class="panel-heading clearfix">
	  <span class="col-md-4">单品编号：<?php echo $s;?></span>
	  <span class="col-md-4">是否尾货销售：<?php echo $isTail?'<span class="text-danger">是</span>':'否';?></span>
	  <span class="col-md-4">可销售量：<?php echo $canSell;?></span>
	</div>
	<ul class="list-group">
		<li class="list-group-item clearfix">
			<span class="col-md-4">当前库存：<?php echo $total;?></span>
			<span class="col-md-4">锁定数量：<?php echo $lockTotal;?>&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo $this->createUrl('salesnum',array('s'=>$s,'type'=>'lockdetail'));?>">锁定明细</a></span>
			<span class="col-md-4">安全库存：<?php echo $safety;?></span>
		</li>
	<li class="list-group-item clearfix">
			<span class="col-md-12">可销售量：
			<?php if( $isTail ){ ?> 0 
			<?php }else{ ?>
			<?php echo $total;?> - <?php echo $lockTotal;?> = <?php echo $canSell;?>
			<?php }?>
			</span>
		</li>
	</ul>
</div>
 <?php }}?>
 <p class="text-muted">说明： 可销售量 = 当前库存 - 锁定数量。为尾货销售时，普通产品的可销售量显示为0。可销售量不计算样品仓和损耗仓的库存</p>

<?php  if( $showDetail ){ ?>
 <h2 class="h3">锁定明细</h2>
<table class="table table-condensed table-bordered table-hover">
  <thead>
    <tr>
	<td>订单编号</td>
    <td>锁定数量</td>
	<td>锁定时间</td>
    </tr>
  </thead>
  <tbody>
    <?php foreach( $list as $item ): ?>
    <tr>
	  <td><?php echo $item['orderId'];?></td>
	  <td><?php echo $item['total'];?></td>
	  <td><?php echo $item['createTime'];?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<br>
<div class="clearfix well well-sm list-well">
<?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
</div>
 <?php }?>
  <script>
seajs.use('statics/app/product/default/js/salesnum.js');
</script>