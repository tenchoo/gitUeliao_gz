<link rel="stylesheet" href="/themes/classic/statics/app/order/css/style.css" />

<div class="panel panel-default search-panel">
   <div class="panel-body">
    <form class="pull-left form-inline" method="post" role="search">
     <select class="form-control input-sm" name="data[type]">
		<option value="">付款类型</option>
		<?php foreach ( $payments as $val ){
			if( $val['type']=='0' ) {
		?>
			<option value="<?php echo $val['paymentId'];?>" <?php echo ($val['paymentId']==$dataArr['type'])?'SELECTED':''?>><?php echo $val['paymentTitle'];?></option>
		<?php }} ?>
		</select>
     <div class="form-group">
	 收款金额：
      <input type="text" class="form-control input-sm" value="<?php echo $dataArr['amount']?>" name="data[amount]" > 元
     </div>
	 <div class="form-group">
	 上传凭证：
      <input type="text" class="form-control input-sm" value="<?php echo $dataArr['voucher']?>" name="data[voucher]" />
	  <a href="javascript::" class="btn btn-sm btn-default">上传</a>
     </div>
	<button class="btn btn-success">提交信息</button>
    </form>
   </div>
  </div>
   <?php $this->beginContent('//layouts/_error');$this->endContent();?>
<?php $this->beginContent('_orderinfo',array('model' => $model ,'member'=>$member,'payments' => $payments));$this->endContent();?>
	<table class="table table-condensed table-bordered order">
   <thead>
    <tr>
     <td>产品信息</td>
	 <td>产品编号</td>
	 <td>颜色</td>
     <td>单价（元）</td>
     <td>购买数量</td>
     <td>小计</td>
	  </tr>
	  </thead>
	  <tbody>
	 <?php foreach( $model->products as $pval) :?>
	  <tr>
     <tr class="order-list-bd">
     <td>
     <div class="c-img pull-left"><a href="javascript:"><img src="<?php echo $this->img(false).$pval['mainPic'];?>" alt="" width="100" height="100"/></a></div>
	 <div class="product-title"><?php echo $pval['title'];?></div>
	 </td>
	 <td><?php echo $pval['serialNumber'];?></td>
	 <td><?php $spec = explode(':',$pval['specifiaction']); echo $spec['1'];?></td>
     <td> <?php echo Order::priceFormat($pval['price']);?></td>
     <td><?php echo $pval['num'];?></td>
     <td><?php echo number_format($pval['num']*$pval['price'],2);?></td>
	  </tr>
	<?php endforeach;?>
	<tr><td colspan="6" align="right">
		运费 ： <?php echo number_format($model['freight'],2);?>
		总额 ：  <?php echo number_format($model['realPayment'],2);?>
   </tbody>
 </table>