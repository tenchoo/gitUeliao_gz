<link rel="stylesheet" href="/themes/classic/statics/app/warehouse/css/style.css" />
<?php $this->beginContent('//layouts/_error2');$this->endContent();?>
<?php $this->beginContent('_tab',array('active'=>'packarea','productId'=>$productId));$this->endContent();?>
<div class="clearfix alert alert-warning">
  产品编号：<strong class="text-warning"><?php echo $serialNumber;?></strong>
</div>
<form action="" class="form-horizontal" method="post">
	<table class="table table-condensed table-bordered import">
	 <colgroup><col width="20%"><col width="20%"><col></colgroup>
    <thead>
    <tr>
     <td>仓库</td>
     <td>默认分拣区域</td>
     <td>操作</td>
    </tr>
	<tbody>
	<?php foreach ( $list as $val ):?>
		<tr id="w_<?php echo $val['warehouseId']?>">
		<td>
		<input type="hidden" name="data[<?php echo $val['warehouseId']?>]" value="<?php echo $val['positionId']?>"/><?php echo $val['wTitle']?>
		</td>
		<td><?php echo $val['pTitle']?></td>
		<td><a href="javascript:;" class="del" title="删除">删除</a></td>
	</tr>
	<?php endforeach;?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="3" align="center" >
				<div class="col-md-5">
					<?php echo CHtml::dropDownList('','',$warehouses,array('class'=>'form-control input-sm cate1','empty'=>'请选择仓库'))?>
				  <select class="form-control input-sm cate2">
					<option value="default">请选择分区</option>
				  </select>
				   <button class="btn btn-sm btn-default"  id="btn-add" type="button"><span class="glyphicon glyphicon-plus"></span>添加</button>
				  </div>
				</td>
			</tr>
		</tfoot>
	</table>
<br>
<div align="center">
<input class="btn btn-success addlm" type="submit" value="提交保存" />
</div>
</form>
<script>seajs.use('statics/app/product/create/js/packarea.js')</script>

<script type="text/html" id="choose-list">
	<tr id="w_{{wid}}">
		<td>
		<input type="hidden" name="data[{{wid}}]" value="{{pid}}"/>{{wtitle}}
		</td>
		<td>{{ptitle}}</td>
		<td><a href="javascript:;" class="del" title="删除">删除</a></td>
	</tr>
</script>