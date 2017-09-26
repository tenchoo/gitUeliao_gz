<div class="panel panel-default search-panel">
	<div class="panel-body">
		<form method="get" class="pull-left form-inline">
			<input type="text" placeholder="请输入产品编号" name="s" value="<?php echo Yii::app()->request->getQuery('s');?>" class="form-control input-sm" />
			<input type="submit" value="查找" class="btn btn-sm btn-default" />
		</form>
	</div>
</div>

<div class="clearfix well well-sm list-well">
  <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
</div>

<table class="table table-condensed table-bordered  table-hover">
   <thead>
    <tr class="list-hd">
     <td>产品编号</td>
     <td>颜色</td>
     <td>安全库存</td>
     <td>当前库存</td>
     <td>正在采购数量</td>
     <td>还需预订数量</td>
    </tr>
   </thead>
   <tbody>
   <?php foreach($orderList as $row){?>
   <tr>
     <td><?php echo $row->singleNumber;?></td>
     <td><?php echo $row->color;?></td>
     <td><?php echo $row->safetyStock;?></td>
     <td><?php echo Order::quantityFormat($row->hasTotal);?></td>
     <td><?php echo Order::quantityFormat($row->getBuyTotal());?></td>
     <td><?php echo Order::quantityFormat($row->NeedTotal);?></td>
   </tr>
   <?php }?>
   </tbody>
</table>
<br>
<div class="clearfix well well-sm list-well">
<?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
</div>