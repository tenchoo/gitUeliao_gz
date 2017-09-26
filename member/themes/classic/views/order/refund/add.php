<link rel="stylesheet" href="/app/member/trade/css/style.css"/>
<div class="pull-right frame-content order-check">
<div class="frame-tab">
  <ul class="clearfix list-unstyled frame-tab-hd">
    <li class="active">
      <a href="javascript:">申请退货</a>
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
            <span class="item">提货方式：<?php echo $model->deliveryMethod;?></span>
			<span class="item">支付方式：<?php echo $model->payModel;?></span>
        </li>
		<li><span>收货地址：<?php echo $model->address;?></span>
		</li>
        <li class="memo"><span class="pull-left">订单备注：</span><div style="padding-left:60px;"><?php echo $model->memo;?></div></li>
    </ul>
</div>
 <div class="frame-list order-details">
    <div class="frame-list-bd">
      <table>
       <thead>
       <tr>
       <th width="30"></th>
	 <th>产品编号</th>
	 <th  width="80">颜色</td>
	 <th width="80">购买数量</th>
	 <th width="100">可退货数量</th>
	 <th>单价（元）</th>
     <th width="80">赠板</th>
	<th>退款小计（元）</th>
	</tr>
    </thead>
    <tbody class="list-page-body">
    <?php foreach( $products as $pval) :?>
    <tr class="list-body-bd">
    <td style="text-align:left;padding-left:15px">
	<?php if( $pval['canRefund'] > 0 ){ ?>
		<input type="checkbox">
	<?php }?>
	</td>
    <td><?php echo $pval['singleNumber'];?></td>
    <td><?php echo $pval['color'];?></td>
    <td><?php echo Order::quantityFormat( $pval['num'] );?></td>
  <td>
  <input type="text" name="products[<?php echo $pval['orderProductId'];?>]"class="form-control num-float-only input-xs" value="<?php echo $pval['canRefund'];?>" style="width:60px" disabled />
  </td>
    <td data-unit="<?php echo $pval['price']*100;?>"> <?php echo Order::priceFormat( $pval['price'] );?></td>
   <td><?php echo ($pval['isSample']=='1')?'是':'否';?></td>
    <td data-price="<?php echo $pval['total']*100;?>"><?php echo Order::priceFormat( $pval['total'] );?></td>
    </tr>
     <?php endforeach;?>
       </tbody>
	     <tfoot class="list-page-foot">
    <tr>
    <td colspan="9">
      <div class="pull-left">
        <label class="checkbox-inline"><input type="checkbox" style="margin-top:3px" class="selectall">全选</label>
      </div>
      <div class="pull-right">
        退款总额 ：<span class="total">0.00</span>
      </div>
      </td></tr>
      </tfoot>
    </table>
     </div>
 </div>
 <br>
  <div class="clearfix">
    <label class="pull-left"> <span class="text-warning">*</span>退货理由：</label>
    <div class="pull-left form-group textarea-group">
      <textarea name="cause" class="form-control" style="height:60px"><?php echo $cause?></textarea>
    </div>
  </div>
  <div class="text-center btn-group"><br/>
	<a class="btn btn-sm" href="<?php echo Yii::app()->request->getUrlReferrer();?>">取消</a>
	<button type="submit" class="btn btn-sm btn-success">申请退货</button>
 </div>
 </form>
</div>
<script>
  seajs.use('app/member/trade/js/refund.js');
</script>