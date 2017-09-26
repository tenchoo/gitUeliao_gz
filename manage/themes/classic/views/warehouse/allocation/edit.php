<link rel="stylesheet" href="/themes/classic/statics/app/warehouse/css/style.css" />
<link rel="stylesheet" href="/themes/classic/statics/app/order/css/style.css" />
<?php if(empty($data['warehouseId'])) { ?>
<form action="">
<div class="panel panel-default">
	<ul class="list-group">
		<li class="list-group-item clearfix">
			<span class="col-md-4">请选择调拨仓库：
			<?php echo CHtml::dropDownList('warehouseId','',$warehouse,array('class'=>'form-control input-sm'))?>
			&nbsp;&nbsp;&nbsp;
			<button class="btn btn-success">确定</button>
			</span>
		</li>
	</ul>
</div>
 </form>
<?php }else{  ?>

<div class="clearfix well well-sm">
	<div class="pull-left">调拨仓库：<?php echo $warehouse[$data['warehouseId']];?></div>
	<div class="pull-right">
		<a href="/warehouse/allocation/add.html">重新选择</a>
	</div>
</div>

<form class="form-horizontal alloction" method="post" action="">


	<table class="table table-condensed table-bordered">
   <thead>
    <tr>
	 <td>产品编号</td>
	 <td>颜色</td>
     <td width="20%">仓位号</td>
	 <td>产品批次</td>
     <td>数量</td>
	 <td>操作</td>
	 </tr>
	 </thead>
	<tbody>
	 <?php foreach( $data['products'] as $key=>$pval) :?>
     <tr>
	 <td><?php echo $pval['singleNumber'];?>
		<input type="hidden" name="data[products][<?php echo $key;?>][singleNumber]" value="<?php echo $pval['singleNumber'];?>"/>
		<input type="hidden" name="data[products][<?php echo $key;?>][productId]" value="<?php echo $pval['productId'];?>"/>
	 </td>
	 <td><?php echo $pval['color'];?>
		<input type="hidden" name="data[products][<?php echo $key;?>][color]" value="<?php echo $pval['color'];?>"/>
	 </td>
	 <td class="col-md-2">
	 <input type="text" name="data[products][<?php echo $key;?>][positionTitle]" value="<?php echo $pval['positionTitle'];?>" class="form-control input-sm"/>
	 </td>
	  <td class="col-md-2">
	 <input type="text" name="data[products][<?php echo $key;?>][productBatch]" value="<?php echo $pval['productBatch'];?>" class="form-control input-sm"/>
	 </td>
     <td  class="col-md-2">
	 <div class="input-group title-group">
		<input name="data[products][<?php echo $key;?>][num]" class="form-control input-sm num-float-only" value="<?php echo $pval['num'];?>"/>
		<div class="input-group-addon"><?php echo $pval['unitName'];?>
		<input type="hidden" name="data[products][<?php echo $key;?>][unitName]" value="<?php echo $pval['unitName'];?>" /></div></div>
	</td>
     <td><a href="/javascript:" class="del">删除</a></td>
	  </tr>
	<?php endforeach;?>
	</tbody>
	<tfoot>
		<tr>
		<td colspan="6">
		<br>
		<div class="form-group center-block">
			<span class="control-label col-md-5">添加产品：</span>
			<div class="col-md-7 form-inline">
			<input type="text" class="form-control input-sm" name="singleNumber"  data-suggestion="singleNumber" data-search="warehouseId=<?php echo $data['warehouseId'];?>&serial=%s" data-house="<?php echo $data['warehouseId'];?>" data-api="/api/search_product_serial" autocomplete="off" placeholder="输入产品编号如：K365-401"/>
			<input type="button" disabled class="btn btn-sm btn-default" value="添加" data-templateid="requestbuylist" id="btn-add"/>
			</div>
		</div>
		</td>
		</tr>
	</tfoot>
</table>
<br/>
	 <div class="panel panel-default">
	 	<br>
	   <div class="form-group">
	     <span class="control-label col-md-5">目标仓库 : </span>
	     <div class="col-md-7 form-inline">
	       <?php echo CHtml::dropDownList('data[targetWarehouseId]',$data['targetWarehouseId'],$warehouse,array('empty'=>'请选择目标仓库','class'=>'form-control input-sm'))?>
	     </div>
	   </div>
	   <div class="form-group">
	     <span class="control-label col-md-5">驾驶员：</span>
	     <div class="col-md-7 form-inline">
	       <?php echo CHtml::dropDownList('data[driverId]',$data['driverId'],$drivers,array('empty'=>'请选择驾驶员','class'=>'form-control input-sm'))?>
	     </div>
	   </div>
	   <div class="form-group">
	     <span class="control-label col-md-5">车辆编号：</span>
	     <div class="col-md-7 form-inline">
	       <?php echo CHtml::dropDownList('data[vehicleId]',$data['vehicleId'],$vehicle,array('empty'=>'请选择车辆','class'=>'form-control input-sm'))?>
	     </div>
	   </div>
	</div>
	 <br/>
	 <?php $this->beginContent('//layouts/_error');$this->endContent();?><br/>
	<div align="center">
		<button class="btn btn-success callbacksub">立即调拨</button>
	</div>
 </form>
 <script type="text/html" id="alloctionlist">
     <tr>
		<td>
			{{title}}
      <input type="hidden" name="data[products][{{t}}][productId]" value="{{productid}}" />
      <input type="hidden" name="data[products][{{t}}][singleNumber]" value="{{title}}" />
    </td>
		<td>
			{{color}}
			<input type="hidden" name="data[products][{{t}}][color]" value="{{color}}" />
		</td>
    <td class="col-md-2">
    	<div class="form-inline">
    	<select name="data[products][{{t}}][areaTitle]" class="form-control input-sm parea" style="width:45%" data-serial="{{title}}">
    		<option value="">请选择分区</option>
    		{{each area}}
	    		<option value="{{$value.areaId}}">{{$value.areaTitle}}</option>}
	    	{{/each}}
    	</select>
    	<select name="data[products][{{t}}][positionId]" class="form-control input-sm ppo" style="width:45%" data-serial="{{title}}">
    		<option value="">请选择仓位</option>
    	</select>
    	</div>
		</td>
		<td class="col-md-2">
			<select name="data[products][{{t}}][productBatch]" class="form-control input-sm pbat" style="width:90%">
    		<option value="">产品批次</option>
    	</select>
		</td>
		<td class="col-md-2">
			<div class="input-group title-group">
			<input name="data[products][{{t}}][num]" class="form-control input-sm num-float-only" />
			<div class="input-group-addon">{{unit}}
			<input type="hidden" name="data[products][{{t}}][unitName]" value="{{unit}}" /></div></div>
		</td>
		<td>
			<a href="/javascript:" class="del">删除</a>
		</td>
	</tr>
</script>
<script>
seajs.use('statics/app/warehouse/js/alloction.js');
</script>
<?php }?>