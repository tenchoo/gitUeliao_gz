<div class="panel panel-default search-panel">
  <div class="panel-body">
  <form action="<?php echo $this->createUrl('temps');?>" class="pull-left form-inline">
	<?php echo CHtml::dropDownList('warehouseId',$warehouseId,$warehouse,array('class'=>'form-control input-sm'))?>
	<input type="text" value="<?php echo $serialNumber;?>" name="serialNumber" class="form-control input-sm" placeholder="产品编码"/>
	<button class="btn btn-sm btn-default">查找</button>
	<a href="<?php echo $this->createUrl('temps',array('type'=>'defaultTemp'));?>">模板样例</a>
	</form>
	
  </div>
</div>
<br>
<?php if( !empty( $list ) ){ ?>
<form class="form-horizontal" method="post">
<div class="row">    
	<?php foreach( $list as $item ): ?>
	<div class="col-md-2">
	 <label class="checkbox-inline">
		<input type="checkbox" name="productId[]" value="<?php echo $item['productId'];?>" />
		<?php echo $item['serialNumber'];?>
	</label>
	</div>
	<?php endforeach; ?>
</div>	
<br/>	<br/>
 <?php $this->beginContent('//layouts/_error');$this->endContent();?>
<div align="center">
	<button class="btn btn-success">下载选中产品模板</button>
</div>
</form>
<?php } ?>