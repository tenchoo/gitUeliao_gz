<form  method="post" action="<?php echo $this->createUrl('print2');?>">
	<table class="table table-condensed table-bordered order">
   <thead>
   <tr><th colspan="7"><?php echo $companyname;?></td><tr>
    <tr>
	 <td>产品编号</td>
	 <td>颜色</td>
	 <td>数量</td>
	 <td>整卷数量</td>
	 <td>零码数量</td>
	 <td>整卷打印</td>
	 <td>零码打印</td>
	  </tr>
	  </thead>
	  <tbody>
	 <?php foreach( $details as $pval) : ?>
	 <tr class="order-list-bd">
	 <td><?php echo $pval['singleNumber'];?></td>
	 <td><?php echo $pval['color'];?></td>
     <td><?php echo Order::quantityFormat( $pval['packingNum'] );?> 码 </td>
	 <td><?php echo $pval['batchNumber'];?> 码 </td>
	 <td><?php echo Order::quantityFormat($pval['bulkNumber'] );?> 码 </td>
	 <td><?php echo $pval['batchs'];?> </td>
	 <td><?php echo $pval['bulk'];?>  </td>
	  </tr>
	<?php endforeach;?>
	 </table><br/><br/>
	 <div align="center">
	 <input type="hidden" name="companyname" value="<?php echo $companyname;?>"/>
	  <input type="hidden" name="details" value='<?php echo json_encode($details);?>'/>
		<button class="btn btn-success">打印预览</button>
	</div>
 </form>