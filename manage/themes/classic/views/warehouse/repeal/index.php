<!-- 操作提示消息框 -->
<?php $this->beginContent('//layouts/_error');$this->endContent();?>
<!-- 操作提示消息框 -->

<!-- 条件过虑档位开始 -->
<div class="panel panel-default search-panel">
  <div class="panel-body">
    <form role="search" class="pull-left form-inline" action="<?php echo $this->createUrl('index'); ?>">
      <div class="form-group">
        <input type="text" name="repealId" value="<?php echo Yii::app()->request->getQuery('repealId'); ?>" autocomplete="off" class="form-control input-sm" placeholder="撤消申请单号" />
      </div>
      <button class="btn btn-sm btn-default">查找</button>
    </form>
  </div>
</div>
<!-- 条件过虑档位结束 -->

<!-- 数据显示区开始 -->
<table class="table table-condensed table-bordered">
  <colgroup><col><col width="15%"><col width="15%"><col width="15%"><col width="15%"></colgroup>
  <thead>
    <tr>
      <td>产品编号</td>
	  <td>产品颜色</td>
	  <td>发货数量</td>
	  <td>入库数量</td>
	  <td>&nbsp;</td>
    </tr>
  </thead>
</table>
<br />

<?php foreach($datalist as $item){
	$warrant = $item->warrant();
	$details = $warrant->detail();
	?>
<table class="table table-condensed table-bordered">
  <colgroup><col><col width="15%"><col width="15%"><col width="15%"><col width="15%"></colgroup>
	<tbody>
    <tr class="list-hd">
      <td colspan="5">
	    <span class="first">撤消申请单号：<?php echo $item->repealId;?></span>
		<span>入库单号：<?php echo $warrant->warrantId;?></span>
		<span>入库时间：<?php echo $warrant->realTime;?></span>
	  </td>
    </tr>
	<?php foreach( $details as $index=>$detail ):
		   $unitName = tbProductStock::model()->unitName($detail->singleNumber);
		   ?>
	<tr>
      <td><?php echo $detail['singleNumber']?></td>
	  <td width="15%"><?php echo $detail['color']?></td>
	  <td width="15%"><?php printf("%s %s", Order::quantityFormat($detail->postQuantity), $unitName);?></td>
	  <td width="15%"><?php printf("%s %s", Order::quantityFormat($detail->num), $unitName);?></td>
		<?php if(!$index){?>
	  <td width="15%" rowspan="<?php echo count($details);?>">
	  <?php if( $item->state == '0' ) { ?>
	  <a href="<?php echo $this->createUrl('check',array('id'=>$item->repealId))?>">审核</a>
	  <?php }else{ ?>
	   <a href="<?php echo $this->createUrl('view',array('id'=>$item->repealId))?>">查看</a>
	  <?php } ?>
	  </td>
		<?php } ?>
    </tr>
	<?php endforeach; ?>
	</tbody>
</table>
<br>
<?php }?>
<!-- 数据显示区结束 -->