<div class="panel panel-default search-panel">
	<div class="panel-body">
		<form role="search" class="pull-left form-inline"
			action="<?php echo $this->createUrl(Yii::app()->getController()->getAction()->id);?>">
			成交时间: <input type="text" name="createTime1"
				value="<?php echo $condition['createTime1']; ?>"
				class="form-control input-sm input-date" id="starttime" readonly/> 到 <input
				type="text" name="createTime2"
				value="<?php echo $condition['createTime2']; ?>"
				class="form-control input-sm input-date" id="endtime" readonly/>
			<div class="form-group">
				<input type="text" name="orderId"
					value="<?php echo $condition['orderId'];?>" placeholder="请输入订单编号"
					class="form-control input-sm" />
			</div>
			<button class="btn btn-sm btn-default">查找</button>
		</form>
	</div>
</div>
<div class="clearfix well well-sm list-well">
    <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
</div>
<table class="table table-condensed table-bordered">
  <colgroup><col width="25%"><col width="10%"><col width="10%"><col width="15%"><col width="20%"><col width="20%"></colgroup>
	<thead>
	<tr>
		<td>产品信息</td>
		<td>单价（元）</td>
		<td>数量</td>
		<td>总金额（元）</td>
		<td>留货</td>
		<td>操作</td>
	</tr>
	</thead>
</table>
<br>
<?php foreach(  $data as $val ){
	$order = $val['order'];
?>
<table class="table table-condensed table-bordered">
  <colgroup><col width="25%"><col width="10%"><col width="10%"><col width="15%"><col width="20%"><col width="20%"></colgroup>
	<tbody>
		<tr class="list-hd">
			<td colspan="6">
				<span class="first">留货订单：<?php echo $order->orderInfo->orderId;?></span>
				<span>客户编号：<?php echo $order->orderInfo->memberId;?></span>
				<span>业务员：<?php echo $order->orderInfo->username;?></span>
				<span>下单时间：<?php echo $order->orderInfo->createTime;?></span>
			</td>
		</tr>
		<?php
			$count = $order->orderInfo->productsTotal;
			foreach( $order->orderInfo->products as $key=>$pval  ){ ?>
			<tr class="list-bd">
				<td>
					<div class="c-img pull-left">
						<img src="<?php echo $this->img().$pval['mainPic'];?>_50" alt="" width="50" height="50"/>
					</div>
					<div class="product-title"><?php echo $pval['title'];?></div>
					<p><?php echo $pval['specifiaction'];?></p>
				</td>
				<td> <?php echo Order::priceFormat($pval['price']);?></td>
				<td><?php echo Order::quantityFormat($pval['num']);?></td>
				<?php if($key=='0'){?>
					<td rowspan="<?php echo $count;?>">
						<?php echo number_format($order->orderInfo->realPayment,2);?><Br/>
						（运费<?php echo $order->orderInfo->freight;?>元）
					</td>
					<td rowspan="<?php echo $count;?>" >
						留货至:<?php echo date('Y-m-d', $order->expireTime);?>
						<br>
						申请延期时间：<?php echo $val['createTime'];?>
					</td>
					<td rowspan="<?php echo $count;?>">
					<?php if($val['state']=='0'){ ?>
						<a href="<?php echo $this->createUrl('checkdelay',array('id'=>$order->orderId));?>">延期审核</a>
					<?php }else{ ?>

					<?php echo $val['stateTitle'];?>
					<br>审核时间：<?php echo $val['checkTime'];?>
					<br>审核人：<?php echo tbUser::model()->getUserName( $val['userId'] ) ;?>
					<?php if(!empty($val['reason'])){ echo '<br>理由：'.$val['reason'];}?>
					<?php } ?>
					</td>
				<?php } ?>
			</tr>
		<?php } ?>
		</tbody>
   </table>
   <br>
	<?php }?>
<div class="clearfix well well-sm list-well">
    <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
</div>

