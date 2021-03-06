<link rel="stylesheet" href="/app/member/trade/css/style.css" />
<form method="post" action="">
  <div class="pull-right frame-content order-check">
    <div class="order-details-list">
      <div class="hd">
        <span class="item"><?php echo $model->orderType;?>：<?php echo $model->orderId;?></span> 
        <span class="item">下单日期：<?php echo $model->createTime;?></span>
      </div>
      <ul class="bd list-unstyled">
        <li><span class="item">客户名称：<?php echo $member['companyname'];?></span> 
        <span class="item">联系人：<?php echo $model->name;?>（<?php echo $model->tel;?>）</span></li>
        <li><span class="item">提货方式： <?php echo $model->deliveryMethod;?></span><span class="item">支付方式：<?php echo $model->payModel;?></span></li>
        <li><span>收货地址： <input type="text" value="<?php echo $data['address'];?>" class="form-control input-xs input-note" name="data[address]" /></span></li>
        <li><span>订单备注： <input type="text" value="<?php echo $data['memo'];?>" class="form-control input-xs input-note" name="data[memo]" /></span></li>
      </ul>
    </div>
    <div class="frame-list order-details">
      <div class="frame-list-bd">
        <table>
          <thead>
            <tr>
              <th width="100">产品编号</th>
              <th>颜色</th>
              <th width="60">改前数量</th>
              <th width="90">改后数量</th>
              <th width="80">单价（元）</th>
              <th width="80">金额（元）</th>
              <th width="30">赠板</th>
              <th width="100">备注</th>
              <th width="49">操作</th>
            </tr>
          </thead>
          <tbody class="list-page-body">
    <?php
	$realPayment = 0;
	foreach( $model->products as $pval) :
		$pval['num'] = $data['products'][$pval->orderProductId]['changeNum'];
		$pval['remark'] = $data['products'][$pval->orderProductId]['remark'];
		$t = ($pval['isSample']=='1')?'0':$pval['num']*$pval['price'];
		$realPayment += $t;
	?>
    <tr class="list-body-bd">
              <td><?php echo $pval['singleNumber'];?></td>
              <td><?php echo $pval['color'];?></td>
              <td><?php echo Order::quantityFormat( $applyinfo[$pval->orderProductId]['oldNum'] );?></td>
              <td><input type="text" name="data[products][<?php echo $pval->orderProductId;?>][changeNum]" value="<?php echo $pval['num'];?>" class="form-control num-float-only input-xs" /></td>
              <td data-unit="<?php echo $pval['price']*100;?>"> <?php echo Order::priceFormat( $pval['price'] );?></td>
              <td data-price="<?php echo ($pval['isSample']=='1')?0:$pval['num']*$pval['price']*100;?>">
    <?php echo Order::priceFormat($t);?>
  </td>
              <td><input type="checkbox" name="data[products][<?php echo $pval->orderProductId;?>][isSample]" value="1" <?php if($pval['isSample']=='1'){echo 'checked';}?> <?php if($pval['num']>4){echo 'disabled';}?> style="position: static; margin: 0;" /></td>
              <td><input type="text" name="data[products][<?php echo $pval->orderProductId;?>][remark]" value="<?php echo $pval['remark'];?>" class="form-control input-xs" style="width: 120px;" /></td>
              <td><a href="javascript:" class="text-link del-product">删除</a></td>
            </tr>
     <?php endforeach;?>
		<tr>
              <td colspan="9" class="text-right"><span>运费 ： <input type="text" name="data[freight]" value="<?php echo Order::priceFormat($data['freight']);?>" class="form-control input-xs price-only" /></span> <span class="p-total">商品金额 ：<?php echo Order::priceFormat($realPayment);?></span> <span
                class="a-total">总金额 ：<?php echo Order::priceFormat($realPayment+$data['freight']);?></span></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <br />
<?php if( $model->orderType != '2') { ?>
  <div class="order-details-list">
      <div class="hd">分批交货</div>
      <ul class="bd list-unstyled">
	     <?php foreach( $model->batches as $key=>$bval) :?>
        <li><span>交货日期： <input type="text" name="data[batches][exprise][]" value="<?php echo $bval->exprise;?>" class="form-control input-xs input-date" readonly="readonly" /></span> <span>备注：<input type="text" name="data[batches][remark][]" value="<?php echo $bval->remark;?>"
            class="form-control input-xs input-note" /></span>
		<?php if($key>0){?>
			 <a class="text-link del-batch" href="javascript:">删除</a>
		<?php }?>
        </li>
		 <?php endforeach;?>
		<li><a href="javascript:" class="text-link add-batch">添加批次</a></li>
      </ul>
    </div>
    <br />
<?php }?>
	<div class="frame-box">
	 <div class="form-horizontal">
    <div class="form-group">
      <label class="control-label">审核结果：</label>
      <label class="radio-inline"><input type="radio" name="data[state]" value="1" <?php if( $data['state']=='1'){echo 'CHECKED';}?> />同意修改订单</label>
      <label class="radio-inline"> <input type="radio" name="data[state]" value="2" <?php if( $data['state']=='1'){echo 'CHECKED';}?> />不同意修改订单</label>
    </div>
    <div class="form-group textarea-group">
      <label class="control-label"> 审核反馈：</label>
      <textarea class="form-control" name="data[checkInfo]"><?php echo $data['checkInfo'];?></textarea>
      <span class="text-warning"><?php echo $this->getError();?></span>
    </div>
    </div>
    </div>
    <br />
    <div align="center">
      <button type="submit" class="btn btn-xs btn-success">提交信息</button>
    </div>
  </div>
</form>
<script type="text/html" id="batch">
  <li  class="list-group-item clearfix">
  <span class="col-md-4">交货日期： <input type="text" name="data[batches][exprise][]" value="" class="form-control input-xs input-date" readonly="readonly"/></span>
  <span>备注：<input type="text" name="data[batches][remark][]" value="" class="form-control input-xs input-note"/></span>
  <a href="javascript:" class="text-link del-batch">删除</a>
  </li>
</script>
<script>seajs.use('app/member/trade/js/ordercheck.js')</script>