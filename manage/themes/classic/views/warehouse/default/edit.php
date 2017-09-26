<link rel="stylesheet" href="/themes/classic/statics/app/order/css/style.css" />
<form  method="post" action="">
<div class="panel panel-default">
	<ul class="list-group">
		<li class="list-group-item clearfix">
			<span class="col-md-4">入库单号：<?php echo $data['warrantId'];?></span>
			<span class="col-md-4">操作时间：<?php echo $data['createTime'];?></span>
			<span class="col-md-4">操作员：<?php echo $data['operator'];?></span>
		</li>
		<li class="list-group-item clearfix">
			<span class="col-md-4">工厂编号：<?php echo $data['factoryNumber'];?></span>
			<span class="col-md-4">工厂名称：<?php echo $data['factoryName'];?></span>
			<span class="col-md-4">联系人：<?php echo $data['contactName'];?></span>
		</li>
		<li class="list-group-item clearfix">
			<span class="col-md-4">联系电话：<?php echo $data['phone'];?></span>
			<span class="col-md-4">地址：<?php echo $data['address'];?></span>
		</li>
		<li class="list-group-item clearfix">
			<span class="col-md-12">备注：<?php echo $data['remark'];?></span>
		</li>
	</ul>
</div>
	<table class="table table-condensed table-bordered order">
   <thead>
    <tr>
	 <td>工厂产品编号</td>
	 <td>采购单号</td>
	 <td>产品编号</td>
	 <td>颜色</td>
     <td>采购数量</td>
     <td>发货数量</td>
	 <td>入库数量</td>
	 <td>入库时间</td>
	 <td>操作</td>
	  </tr>
	  </thead>
	  <tbody>
	 <?php  foreach( $data['products'] as $key=>$pval) :?>
	  <tr>
     <tr class="order-list-bd">
	 <td><?php echo $pval['corpProductNumber'];?></td>
	 <td><?php echo $pval['orderId'];?></td>
	 <td><?php echo $pval['singleNumber'];?></td>
	 <td><?php echo $pval['color'];?></td>
	 <td> </td>
	 <td> </td>
     <td><input type="text" name="data[products][<?php echo $key;?>][num]" value="<?php echo $pval['num'];?>"/> 码</td>
	 <td><input type="text" name="data[products][<?php echo $key;?>][storageTime]" value="<?php echo $pval['storageTime'];?>"/></td>
     <td><a href="javascript::">删除</a>
	 </td>
	  </tr>
	<?php  endforeach;?>
	 </table>
	 <?php $this->beginContent('//layouts/_error');$this->endContent();?><br/>
	<div align="center">
		<button class="btn btn-success">保存</button>
	</div>
 </form>