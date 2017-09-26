<link rel="stylesheet" href="/app/member/trade/css/style.css"/>
<div class="pull-right frame-content">
<div class="frame-tab">
    <ul class="clearfix list-unstyled frame-tab-hd">
      <li class="active">
        <a href="javascript:">订单跟踪</a>
      </li>
    </ul>
 </div>
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
     <div class="hd">订单跟踪</div>
      <ul class="bd list-unstyled">
		<?php foreach( $trace as $dval ){ ?>
		 <li>
			<span><?php echo $dval['createTime'];?> <?php echo $dval['subject'];?></span>		
		</li>
		<?php }?>
	  </ul>
    </div>
</div>
