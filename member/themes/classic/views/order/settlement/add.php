<link rel="stylesheet" href="/app/member/trade/css/style.css"/>
<div class="pull-right frame-content order-check">
<div class="frame-tab">
    <ul class="clearfix list-unstyled frame-tab-hd">
      <li class="active">
        <a href="javascript:">生成结算单</a>
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
			<?php echo CHtml::dropDownList('data[deliveryMethod]',$model->deliveryMethod,$deliveryMethod,array('class'=>'form-control input-xs'))?></span>
			<span class="item">支付方式：
			<select name="data[payModel]" class="form-control input-xs">
			<?php foreach ($payModel as $val){?>
				<option value="<?php echo $val['paymentId']?>" <?php if($model->payModel == $val['paymentId'] ){echo 'SELECTED';}?>>
					<?php echo $val['paymentTitle']?>
				</option>
			<?php }?>
			</select>
			</span>
        </li>
        <li> <span class="item">订单状态：<?php echo $model->state;?></span><span class="item">发货仓库：<?php echo $model->warehouseId;?></span>
		</li>
		<li><span>收货地址：
           <input type="text" value="<?php echo $model->address;?>" class="form-control input-xs input-note" name="data[address]"/></span>
		</li>
        <li><span>订单备注：
          <input type="text" value="<?php echo $model->memo;?>" class="form-control input-xs input-note" name="data[memo]"/></span>
		</li>
    </ul>
</div>
 <div class="frame-list order-details">
    <div class="frame-list-bd">
      <table>
       <thead>
       <tr>
	 <th>产品编号</th>
	 <th>颜色</td>
	 <th>购买数量</th>
	 <th>备货数量</th>
	 <th>结算数量</th>
	 <th>单价（元）</th>
     <th>金额（元）</th>
     <th>赠板</th>
	 <th>备注</th>
	 <th>操作</th>
	</tr>
    </thead>
    <tbody class="list-page-body">
    <?php $totalPayments = 0;
		foreach( $model->products as $pval) :
			$total = ($pval['isSample']=='1')?'0':$pval['num']*$pval['price'];
			$totalPayments += $total;?>
    <tr class="list-body-bd">
    <td><?php echo $pval['singleNumber'];?></td>
    <td><?php echo $pval['color'];?></td>
    <td><?php echo Order::quantityFormat( $pval['num'] );?></td>
	<td><?php echo Order::quantityFormat( $pval['num'] );?></td>
  <td>
  <input type="text" name="data[products][<?php echo $pval->orderProductId;?>][settlementNum]" value="<?php echo $pval['num'];?>" class="form-control num-float-only input-sm" max="<?php echo $pval['num'];?>"/>
  </td>
    <td data-unit="<?php echo $pval['price']*100;?>"> <?php echo Order::priceFormat( $pval['price'] );?></td>
     <td data-price="<?php echo $total*100;?>">
		<?php echo Order::priceFormat($total);?>
	</td>
   <td><input type="checkbox" name ="data[products][<?php echo $pval->orderProductId;?>][isSample]" value="1" <?php if($pval['isSample']=='1'){echo 'checked';}?> <?php if($pval['num']>4){echo 'disabled';}?>/></td>
  <td><input type="text" name ="data[products][<?php echo $pval->orderProductId;?>][remark]" value="<?php echo $pval['remark'];?>" class="form-control input-xs"/></td>
  <td><a href="javascript:" class="text-link del-product">删除</a></td>
    </tr>
     <?php endforeach;?>
		<tr>
    <td colspan="10" class="text-right" >
     <span>物流费 ： <input type="text" name="data[freight]" value="<?php echo Order::priceFormat($model->freight);?>" class="form-control input-xs price-only"/></span>
     <span class="p-total">商品金额 ：<?php echo Order::priceFormat($totalPayments);?></span>
    <span class="a-total">总金额 ：<?php echo Order::priceFormat($totalPayments+$model->freight);?></span>
      </td></tr>
       </tbody>
    </table>
     </div>
 </div>
  <div class="text-center btn-group"><br/>
	<a class="btn btn-xs" href="<?php echo $this->createUrl('/order/default/index',array('type'=>3));;?>">取消</a>
	<button type="submit" class="btn btn-xs btn-success">确定</button>
	<input name="printpush" value ="0" type="hidden"/>
	<button type="button" class="btn btn-xs btn-success submit-print">确定并打印</button>
 </div>
 </form>
</div>
<script>
seajs.use('app/member/settlement/js/add.js');
</script>