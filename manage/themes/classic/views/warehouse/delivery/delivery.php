<div class="panel panel-default">
   <div class="panel-heading clearfix">
	<span class="col-md-4">订单编号：<?php echo $model['orderId'];?></span>
	<span class="col-md-4">发货仓库：<?php echo $model['warehouseId'];?></span>
	<span class="col-md-4">配送方式：<?php echo $deliveryMethod;?></span>
	</div>
    <ul class="list-group">
	    <li class="list-group-item clearfix">
			<span class="col-md-4">备注：<?php echo $model['memo'];?></span>
		</li>
    </ul>
  </div>
<form  method="post" action="">
	<table class="table table-condensed table-bordered">
   <thead>
    <tr>
	 <td>产品编号</td>
	 <td>颜色</td>
     <td>数量</td>
	 <td>备注</td>
	  </tr>
	  </thead>
	  <tbody>
	 <?php foreach( $model->products as $pval) :
		$unit = array_key_exists( $pval->productId,$units )?$units[$pval->productId]['unit']:'';
	 ?>
	  <tr>
     <tr class="order-list-bd">
	 <td><?php echo $pval['singleNumber'];?></td>
	 <td><?php echo $pval['color'];?></td>
     <td><?php echo Order::quantityFormat( $pval['deliveryNum'] );echo $unit;?> </td>
	 <td><?php echo $pval['remark'];?></td>
	  </tr>

	<?php endforeach;?>
	 </table><br/>
	  <div class="panel panel-default">
		<ul class="list-group">
	    <li class="list-group-item clearfix">
			<span class="col-md-2 text-right">收货地址：</span>
			<span class="col-md-6"><input type="text" name="data[address]" value="<?php echo $model['address'];?> ( <?php echo $model['name'];?>  收 ) <?php echo $model['tel'];?>" size="100" class="form-control input-sm"/></span>
		</li>
		<?php if( $scenario == 'logistics' ){ ?>
	    <li class="list-group-item clearfix">
			<span class="col-md-2 text-right">选择物流：</span>
				<span class="col-md-6"><select name="data[logistics]" class="form-control input-sm">
					<option value="">请选择物流公司</option>
					<?php foreach( $logisticsList as $key=>$val ){ ?>
					<option value="<?php echo $key;?>"><?php echo $val;?></option>
					<?php } ?>
				</select>
			</span>
		</li>
		<li class="list-group-item clearfix">
			<span class="col-md-2 text-right">物流编号：</span>
			<span class="col-md-6"><input type="text" name="data[logisticsNo]" value="" size="50" class="form-control input-sm"/></span>
		</li>
		<?php }else if( $scenario == 'ladingCode'  ){ ?>
		<li class="list-group-item clearfix">
			<span class="col-md-2 text-right">提货码：</span>
			<span class="col-md-2"><input type="text" name="data[ladingCode]" value="" size="50" class="form-control input-sm"/></span>
			<a class="btn btn-success btn-sm send-code" href="<?php echo $this->createUrl('deliverycode',array( 'id'=>$model->orderId ))?>">发送提货码</a>
		</li>
		 <script>
		 	seajs.use('statics/app/warehouse/js/delivery.js')
		 </script>
		<?php } ?>
    </ul>
  </div>
	 <br/>
	 <?php $this->beginContent('//layouts/_error');$this->endContent();?><br/>
	<div align="center">
		<button class="btn btn-success">发货</button>
	</div>
 </form>