<link rel="stylesheet" href="/themes/classic/statics/app/order/css/style.css" />
<?php $this->beginContent('_orderinfo',array('model' => $model ,'member'=>$member,'payments' => $payments));$this->endContent();?>
<form  method="post" action="">
	<table class="table table-condensed table-bordered order">
   <thead>
    <tr>
     <td>产品信息</td>
	 <td>产品编号</td>
	 <td>颜色</td>
     <td>购买数量</td>
	 <td>已分拣数量</td>
     <td>分拣数量</td>
	  </tr>
	  </thead>
	  <tbody>
	 <?php foreach( $model->products as $pval) :?>
	  <tr>
     <tr class="list-bd">
     <td>
     <div class="c-img pull-left"><a href="javascript:"><img src="<?php echo $this->img(false).$pval['mainPic'];?>" alt="" width="100" height="100"/></a></div>
	 <div class="product-title"><?php echo $pval['title'];?></div>
	 </td>
	 <td><?php echo $pval['serialNumber'];?></td>
	 <td><?php $spec = explode(':',$pval['specifiaction']); echo $spec['1'];?></td>
     <td><?php echo $pval['num'];?> 码</td>
	 <td><?php echo $pval['packingNum'];?> 码</td>
     <td>
	 <?php if( $pval['num']>$pval['packingNum'] ){ ?>
	 <input type="text" name="pack[<?php echo $pval['stockId']?>]"/> 码
	 <?php }?>
	 </td>
	  </tr>
	<?php endforeach;?>
	 </table><br/><br/>
	 <?php $this->beginContent('//layouts/_error');$this->endContent();?><br/>
	<div align="center">
		<button class="btn btn-success">确认分拣</button>
	</div>
 </form>