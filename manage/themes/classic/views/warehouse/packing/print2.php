<form  method="post" action="">
	<table class="table table-condensed table-bordered order">
   <thead>
    <?php foreach( $details as $pval) : ?>
	<?php for($i=1;$i<=$pval['batchs'];$i++ ){ ?>
	<tr>
	<td witdh="100px"><input type="checkbox" name="" value=""/></td>
	<td>客户名称 : <?php echo $companyname;?><br/>
		产品编号 : <?php echo $pval['singleNumber'];?><br/>
		数量 : <?php echo $pval['unitRate'];?><br/>
	</td>
	<tr>
	<?php }?>
	<?php if( $pval['bulkNumber']>0 ){ ?>
	<tr>
	<td witdh="100px"><input type="checkbox" name="" value=""/></td>
	<td>客户名称 : <?php echo $companyname;?><br/>
		产品编号 : <?php echo $pval['singleNumber'];?><br/>
		数量 : <?php echo Order::quantityFormat( $pval['bulkNumber'] );?><br/>
	</td>
	<tr>
	<?php }?>
	<?php endforeach;?>
	 </table><br/><br/>
	 <div align="center">
		<button class="btn btn-success">打印</button>
	</div>
 </form>