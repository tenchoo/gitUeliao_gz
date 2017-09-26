<style>.h3{display:none} .well span.text-danger{margin-left:5px}</style>
<?php
   $this->widget('application.widgets.widgetSubNav',array('urlMap'=>array(
       '待采购订单'=>'/purchase/default/index',
       '低安全库存'=>'/purchase/lower/index',
       '客户订单'=>'/purchase/order/index',
       '内部请购单'=>'/purchase/requestbuy/index'
   )));
?>
<div class="panel panel-default search-panel">
	<div class="panel-body">
		<form method="get" class="pull-left form-inline">
			<input type="text" name="s" placeholder="请输入产品编号" class="form-control input-sm" value="<?php echo $s;?>"/>
			<input type="text" name="o" placeholder="请输入订单编号" class="form-control input-sm" value="<?php echo $o;?>"/>
			<input type="submit" value="查找" class="btn btn-sm btn-default" />
		</form>
	</div>
</div>
<div class="clearfix well well-sm list-well">
   <div class="pull-left form-inline">
    <label class="checkbox-inline"> <input type="checkbox" class="checkedall"/> 全选 </label>
    <button class="btn btn-sm btn-default addall" data-url="<?php echo $this->createUrl('add')?>">批量加入采购</button>
    <a class="btn btn-sm btn-default"  <?php if( $chooseCount <=0 ){ echo 'disabled' ;}else{ ?>href="<?php echo $this->createUrl('default/add')?>" <?php }?> >采购单<span class="text-danger"><?php echo $chooseCount;?></span></a>
   </div>
    <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>
<table class="table table-condensed table-bordered">
  <colgroup><col><col width="10%"><col width="15%"><col width="15%"><col width="15%"><col width="15%"><col width="10%"><col></colgroup>
  <thead>
    <tr>
      <td width="24"></td>
      <td>来源</td>
	  <td>来源单号</td>
	  <td>产品编号</td>
	  <td>颜色</td>
      <td>预定数量</td>
      <td>交货日期</td>
      <td>备注</td>
    </tr>
  </thead>
</table>
<br />
<table class="table table-condensed table-bordered table-hover">
  <colgroup><col><col width="10%"><col width="15%"><col width="15%"><col width="15%"><col width="15%"><col width="10%"><col></colgroup>
  <tbody>
    <?php foreach( $list as $item ):?>
    <tr>
    	<td width="20"><input type="checkbox" class="draft" name="id[]" value="<?php echo $item['purchaseId'];?>" /></td>
		<td><?php echo $this->formDict( $item->source );?></td>
		<td><?php echo $item->orderId;?></td>
		<td><?php echo $item->productCode;?></td>
		<td><?php echo $item->color;?></td>
		<td><?php printf("%.1f", $item->quantity);?><?php echo ZOrderHelper::getUnitName($item->productCode);?></td>
		<td><?php echo $item->deliveryTime;?></td>
		<td><?php echo $item->comment;?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<br>
<div class="clearfix well well-sm list-well">
   <div class="pull-left form-inline">
    <label class="checkbox-inline"> <input type="checkbox" class="checkedall"/> 全选 </label>
    <button class="btn btn-sm btn-default addall" data-url="<?php echo $this->createUrl('add')?>">批量加入采购</button>
    <a class="btn btn-sm btn-default" href="<?php echo $this->createUrl('default/add')?>">采购单<span class="text-danger"><?php echo $chooseCount;?></span></a>
   </div>
    <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
</div>
<script>seajs.use('statics/app/purchase/default/js/index.js');</script>