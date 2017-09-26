<link rel="stylesheet" href="/app/member/trade/css/style.css"/>
<div class="pull-right frame-content">
<?php $this->beginContent('_orderinfo',array('model' => $model ,'member'=>$member,'payments' => null,'orderType'=>$orderType));$this->endContent();?>
 <div class="frame-list order-details">
    <div class="frame-list-bd">
    <table>
     <thead>
      <tr>
	 <th>产品编号</th>
	 <th>颜色</td>
	 <th>购买数量</th>
     <th>单价（元）</th>
     <th>金额（元）</th>
	 <th>赠板</th>
	 <th>备注</th>
	 </tr>
     </thead>
    <tbody class="list-page-body">
    <?php foreach( $model->products as $pval) :?>
     <tr class="list-body-bd">
      <td><?php echo $pval['serialNumber'];?></td>
	  <td><?php echo $pval['color'];?></td>
	  <td><?php echo Order::quantityFormat( $pval['num'] );?></td>
	  <td><?php echo Order::priceFormat( $pval['price'] );?></td>
	  <td>
		 <?php if( $pval['isSample'] == '1'){ ?>
		  <?php echo Order::priceFormat(0);?>
		 <?php }else { ?>
		   <?php echo Order::priceFormat($pval['num']*$pval['price']);?>
		 <?php }?>
	  </td>
	  <td><?php echo ($pval['isSample'])?'是':'否';?></td>
	  <td><?php echo $pval['remark'];?></td>
	 </tr>
    <?php endforeach;?>
    <tr class="total">
	 <td colspan="7">
		<span>运费 ： <?php echo Order::priceFormat($model['freight']);?>元</span>
		<span>商品金额 ： <?php echo Order::priceFormat($model['realPayment']-$model['freight']);?>元</span>
		<span>总金额 ：   <?php echo Order::priceFormat($model['realPayment']);?>元</span>
	  </td>
	 </tr>
     </tbody>
    </table>
   </div>
  </div>
   <div class="order-details-list">
     <div class="hd">分批交货</div>
      <ul class="bd list-unstyled">
		<?php foreach( $model->batches as $bval) :?>
		 <li>
			<span class="item">交货日期：<?php echo $bval->exprise;?></span>
			<span class="item memo"><span class="pull-left">备注：</span><div style="padding-left:36px"><?php echo $bval->remark;?></div></span>
		</li>
		<?php endforeach;?>
	  </ul>
    </div>
<form  method="post" action="">
	<div class="order-details-list">
    <div class="hd">订金信息 &nbsp;&nbsp;&nbsp;&nbsp;<span class="text-warning"><?php echo $this->getError();?></span></div>
    <ul class="bd list-unstyled">
        <li>
		<span class="item">订金金额：<input type="text" name ="amount" value="<?php echo $deposit->amount;?>"/></span>
		<span class="item">尾款：<?php echo bcsub($model->realPayment,$deposit->amount);?></span>
		</li>
	</ul>
  </div><br/>
   <div align="center">
	<a class="btn btn-xs" href="javascript:history.go(-1);">取消</a>
	<?php if( $model->state == '0'){ ?>
	<a class="btn btn-xs" href="<?php echo $this->createUrl('check',array('id'=>$model->orderId));?>">订单审核</a>
	<?php }?>
	<button type="submit" class="btn btn-xs btn-success">确定</button>
 </div>
</form>
</div>