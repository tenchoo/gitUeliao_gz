 <?php $this->beginContent('//layouts/_error2');$this->endContent();?>
<div class="panel panel-default search-panel">
  <div class="panel-body">
  <form action="<?php echo $this->createUrl('list');?>" class="pull-left form-inline">
	<input type="text" autocomplete="off" data-api="/statistic/ajax/member/" data-search="q=%s&amp;t=2" data-suggestion="searchmember" class="form-control input-sm" placeholder="客户名称" value="<?php echo $memberName;?>" name="memberName">
	&nbsp;&nbsp;收款时间: <input id="t1" name="t1"  type="text" readonly="readonly" maxlength="20"  class="form-control input-sm input-date" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd',maxDate:'#F{$dp.$D(\'t2\')}'})" value="<?php echo $t1;?>"/>
        —
        <input id="t2" name="t2" type="text" readonly="readonly" maxlength="20"  class="form-control input-sm input-date" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd',minDate:'#F{$dp.$D(\'t1\')}',maxDate:'%y-%M-%d'})" value="<?php echo $t2;?>" />
	<input type="hidden" value="<?php echo $memberId;?>" name="memberId">
	<button class="btn btn-sm btn-default" >查找</button>
	</form>
  </div>
</div>
<?php if( isset( $total )  ) { ?>
<div class="clearfix well well-sm list-well">

<span>客户：<?php echo $memberName;?>
	&nbsp;&nbsp;收款时间段：<?php echo $t1.'-'.$t2;?>
	&nbsp;&nbsp;总收款金额：<?php echo Order::priceFormat( $total );?> 元
	&nbsp;&nbsp;
	 <a class="btn btn-sm btn-warning" href="<?php echo $excelUrl;?>">导出EXCEL</a>

</span>


  <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
</div>
<table class="table table-condensed table-bordered">
  <thead>
    <tr>
	  <td>客户</td>
	  <td>结算类型</td>
      <td>结算单号/月份</td>
	  <td>收款金额(元)</td>
	  <td>收款时间</td>
	  <td>操作人</td>
	  <td>状态</td>
	  <td>操作</td>
    </tr>
  </thead>
  <tbody>
   <?php foreach( $list as $item ):?>
   <tr>
	<td><?php echo $item['member'];?></td>
	<td><?php echo ($item['type'] == '1')?'月结':'结算单';?></td>
    <td><?php echo $item['settlementId'];?></td>
	<td><?php echo Order::priceFormat( $item['amount'] );?></td>
	<td><?php echo $item['createTime'];?></td>
	<td><?php echo $item['username'];?></td>
	<td><?php echo ($item['state'] == '1')?'已撤销':'正常';?></td>
	<td>
	   <?php if( $item['canUndo'] ){?>
		<a href="<?php echo $this->createUrl('undo/add',array('id'=>$item['recordsId']));?>">申请撤消</a><br>
		<?php }?>
	</td>
   </tr>
   <?php endforeach; ?>
  </tbody>
</table>
<br/>
<div class="clearfix well well-sm list-well">
  <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
</div>

<?php }?>

<script>seajs.use('statics/app/finance/js/index.js');</script>