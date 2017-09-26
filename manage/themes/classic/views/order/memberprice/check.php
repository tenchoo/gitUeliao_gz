<link rel="stylesheet" href="/themes/classic/statics/app/order/css/style.css" />

<div class="panel panel-default">
	<div class="panel-heading clearfix">
	 <span class="col-md-4">单号：<?php echo $applyPriceId;?></span>
	 <span class="col-md-4">客户名称：<?php echo $companyname;?></span>
	 <span class="col-md-4">业务员：<?php echo $saleman;?></span>
	</div>
</div>
<form action="" class="form-horizontal member-price" @submit.stop.prevent="submit">
<table class="table table-condensed table-bordered order">
	<thead>
    <tr>
	 <td>产品信息</td>
	 <td  width="100px">单价</td>
     <td width="40%">批发价</td>
	</tr>
    </thead>
    <tbody class="list-page-body">
	 <tr class="list-bd">
   <td>
   <div class="c-img pull-left">
     <a href="javascript:"><img src="<?php echo $this->img().$mainPic;?>_50" alt="" width="50" height="50"/></a>
   </div>
	 <div class="product-title">【<?php echo $serialNumber;?>】<?php echo $title;?></div>
	 </td>
     <td> <?php echo Order::priceFormat( $price );?>元/<?php echo $unitName;?></td>
	<td class="form-inline"><input type="text" name="applyPrice" value="<?php echo Order::priceFormat($applyPrice,'');?>" class="form-control input-sm price-only"/>元/<?php echo $unitName;?></td>
    </tr>
    </tbody>
</table>
<br>
<br>
<div class="form-group">
    <label class="control-label col-md-2" for="">审核结果：</label>
    <div class="col-md-4">
      <label class="radio-inline"><input v-model="state" type="radio" name="state" value="1" checked />审核通过</label>
      <label class="radio-inline"><input v-model="state" type="radio" name="state" value="2"/>审核不通过</label>
    </div>
</div>
<div class="form-group">
    <label class="control-label col-md-2" for="">反馈内容：</label>
    <div class="col-md-4">
      <textarea name="remark" v-model="remark" class="form-control"></textarea>
    </div>
</div>
<?php $this->beginContent('//layouts/_error');$this->endContent();?>
<div class="form-group">
    <div class="col-md-offset-2 col-md-10">
      <button type="submit" class="btn btn-success">提交审核</button>
    </div>
</div>
</form>
<script>seajs.use('statics/app/order/js/memberprice.js');</script>