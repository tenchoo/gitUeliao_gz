<link rel="stylesheet" href="/app/member/trade/css/style.css"/>
<div class="pull-right frame-content order-check">
<div class="frame-tab">
	<ul class="clearfix list-unstyled frame-tab-hd">
	  <li class="active">
	    <a href="javascript:">订单审核</a>
	  </li>
	</ul>
 </div>
<form  method="post" action="">
        <div class="order-details-list">
          <div class="hd">
            <span class="item"><?php echo $orderType;?>：<?php echo $model->orderId;?></span>
            <span class="item">下单日期：<?php echo $model->createTime;?></span>
          </div>
          <ul class="bd list-unstyled">
            <li>
              <span class="item">客户名称：<?php echo $member['companyname'];?></span>
 			  <span class="item">联系人：<?php echo $model->name;?>（<?php echo $model->tel;?>）</span>
            </li>
            <li>
              <span class="item">提货方式：
			  <?php echo CHtml::dropDownList('data[deliveryMethod]',$model->deliveryMethod,$deliveryMethod,array('class'=>'form-control input-xs'))?>
			  </span>
			  <span class="item">支付方式：
			  <?php  if ( $model->payState >1 ){ ?>
				<?php echo $payModel[$model->payModel]['paymentTitle']; ?>
			  <?php } else{ ?>
			  <select name="data[payModel]" class="form-control input-xs">
			  <?php foreach ($payModel as $val){?>
				<option value="<?php echo $val['paymentId']?>" <?php if( $model->payModel == $val['paymentId'] ){ echo 'selected';}?>>
					<?php echo $val['paymentTitle']?>
				</option>
			  <?php }?>
			  </select>
			  <?php }?>
			  <?php if( !empty($creditInfo) ){ ?>
			  客户当前可用信用额度为：<?php echo $creditInfo['validCredit'];?>
			  <?php }?>
			  </span>
            </li>
			<li><span>　收货人：
              <input type="text" value="<?php echo $model->name;?>" class="form-control input-xs input-note" name="data[name]"/></span></li>
			<li><span>联系电话：
              <input type="text" value="<?php echo $model->tel;?>" class="form-control input-xs input-note" name="data[tel]"/></span></li>
            <li><span>收货地址：
              <input type="text" value="<?php echo $model->address;?>" class="form-control input-xs input-note" name="data[address]"/></span></li>
            <li><span>订单备注：
              <input type="text" value="<?php echo $model->memo;?>" class="form-control input-xs input-note" name="data[memo]"/></span></li>
          </ul>
        </div>
 <div class="frame-list order-details">
        <div class="frame-list-bd">
          <table>
            <thead>
              <tr>
	 <th>产品编号</th>
	 <th>颜色</td>
	<?php if( $model->orderType != '2') { ?>
	 <th>购买数量</th>
	 <th>备货数量</th>
	<?php }else { ?>
	 <th>数量</th>
	<?php }?>
	 <th>单价（元）</th>
     <th>金额（元）</th>
     <th>赠板</th>
	 <th>备注</th>
	 <th>操作</th>
	</tr>
    </thead>
    <tbody class="list-page-body">
    <?php
	$products = $model->products;
	$n = count( $products );
	foreach( $products  as $pval) :?>
    <tr class="list-body-bd">
    <td ><?php echo $pval['singleNumber'];?></td>
  <td ><?php echo $pval['color'];?></td>
  <?php if( $model->orderType != '2') { ?>
   <td ><?php echo Order::quantityFormat( $pval['num'] );?></td>
  <?php }?>
  <?php if($pval->saleType == 'whole'){ ?>
   <td><?php echo $pval['num'];?></td>
   <td data-unit="<?php echo $pval['price']*100;?>"> <?php echo Order::priceFormat( $pval['price'] );?></td>
     <td data-price="<?php echo ($pval['isSample']=='1')?0:$pval['num']*$pval['price']*100;?>">
    <?php $t = ($pval['isSample']=='1')?'0':$pval['num']*$pval['price'];
     echo Order::priceFormat( $t );?>
  </td>
   <td><input type="checkbox" disabled /></td>
  <td><input type="text" name ="data[products][<?php echo $pval->orderProductId;?>][remark]" value="<?php echo $pval['remark'];?>" class="form-control input-xs"/></td>
  <td></td>
  <?php }else{ ?>
  <td><input type="text" name="data[products][<?php echo $pval->orderProductId;?>][num]" value="<?php echo $pval['num'];?>" class="form-control num-float-only input-xs"/> </td>
   <td data-unit="<?php echo $pval['price']*100;?>"> <?php echo Order::priceFormat( $pval['price'] );?></td>
     <td data-price="<?php echo ($pval['isSample']=='1')?0:$pval['num']*$pval['price']*100;?>">
    <?php $t = ($pval['isSample']=='1')?'0':$pval['num']*$pval['price'];
     echo Order::priceFormat( $t );?>
  </td>
   <td><input type="checkbox" name ="data[products][<?php echo $pval->orderProductId;?>][isSample]" value="1" <?php if($pval['isSample']=='1'){echo 'checked';}?> <?php if($pval['num']>4){echo 'disabled';}?>/></td>
  <td><input type="text" name ="data[products][<?php echo $pval->orderProductId;?>][remark]" value="<?php echo $pval['remark'];?>" class="form-control input-xs"/></td>
  <td>
	<?php if( $n > 1 ){ ?> <a href="javascript:" class="text-link del-product">删除</a><?php } ?>
  </td>
  <?php } ?>
    </tr>
     <?php endforeach;?>
		<tr>
    <td colspan="9" class="text-right" >
     <span>运费 ： <input type="text" name="data[freight]" value="<?php echo Order::priceFormat( $model->freight );?>" class="form-control input-xs price-only"/></span>
     <span class="p-total">商品金额 ：<?php echo Order::priceFormat( $model->realPayment-$model->freight );?></span>
    <span class="a-total">总金额 ：<?php echo Order::priceFormat( $model->realPayment );?></span>
      </td></tr>
       </tbody>
    </table>
     </div>
 </div>
 <?php
 $changeDeposit = false;
 if( $model->orderType == '1') {
	$deposit = $model->deposit;
	if( !empty( $deposit ) && $deposit->amount > 0 ){
		$changeDeposit = true;
 ?>
  <div class="order-details-list">
    <div class="hd">订金信息</div>
    <ul class="bd list-unstyled">
        <li>
		<span class="item">订金金额：<?php echo $deposit->amount;?></span>
		<span class="item">尾款：<?php echo bcsub($model->realPayment,$deposit->amount);?></span>
		</li>
	</ul>
  </div><br/>
 <?php }}?>
<?php if( $model->orderType != '2') { ?>
  <div class="order-details-list">
    <div class="hd">分批交货</div>
    <ul class="bd list-unstyled">
	     <?php foreach( $model->batches as $bval) :?>
        <li>
        <span>交货日期： <input type="text" name="data[batches][exprise][]" value="<?php echo $bval->exprise;?>" class="form-control input-xs input-date" /></span>
        <span>备注：<input type="text" name="data[batches][remark][]" value="<?php echo $bval->remark;?>" class="form-control input-xs input-note"/></span>
			 <a class="text-link del-batch" href="javascript:">删除</a>
        </li>
		 <?php endforeach;?>
		<li><a href="javascript:" class="text-link add-batch">添加批次</a></li>
	</ul>
  </div><br/>
<?php }?>
  <div align="center">
	<a class="btn btn-xs" href="<?php echo $this->createUrl('index');?>">取消</a>
	<?php if( $changeDeposit ){ ?>
	<a class="btn btn-xs" href="<?php echo $this->createUrl('changedeposit',array('id'=>$model->orderId));?>">修改订金</a>
	<?php } ?>
	<button type="submit" class="btn btn-xs btn-success">确定</button>
 </div>
 </form>
</div>
<script type="text/html" id="batch">
  <li  class="list-group-item clearfix">
  <span class="col-md-4">交货日期： <input type="text" name="data[batches][exprise][]" value="" class="form-control input-xs input-date" /></span>
  <span>备注：<input type="text" name="data[batches][remark][]" value="" class="form-control input-xs input-note"/></span>
  <a href="javascript:" class="text-link del-batch">删除</a>
  </li>
</script>
<script>seajs.use('app/member/trade/js/ordercheck.js');</script>