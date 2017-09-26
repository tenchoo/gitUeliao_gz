<link rel="stylesheet" href="/themes/classic/statics/app/order/css/style.css" />
  <div class="panel panel-default search-panel">
   <div class="panel-body">
    <form role="search" class="pull-left form-inline" action="<?php echo $this->createUrl(Yii::app()->getController()->getAction()->id);?>">
	成交时间:  <input type="text" name="createTime1" value="<?php echo $condition['createTime1']; ?>" class="form-control input-sm input-date" readonly id="starttime"/>
		到 <input type="text" name="createTime2" value="<?php echo $condition['createTime2']; ?>" class="form-control input-sm input-date" readonly id="endtime"/>
     <div class="form-group">
      <input type="text" name="orderId" value="<?php echo $condition['orderId'];?>" placeholder="请输入订单编号" class="form-control input-sm" />
     </div>
     <button class="btn btn-sm btn-default">查找</button>
    </form>
   </div>
  </div>
  <div class="clearfix well well-sm list-well">
   <div class="pull-left form-inline">
    <!-- <label class="checkbox-inline"> <input type="checkbox" class="checkedall"/> 全选 </label>
    <button class="btn btn-sm btn-default">批量导出</button> -->
   </div>
  <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>
  <table class="table table-condensed table-bordered order">
  <colgroup><col width="30%"/><col width="10%"/><col width="10%"/><col width="10%"/><col width="15%"/><col width="15%"/></colgroup>
   <thead>
    <tr>
     <td>产品信息</td>
     <td>单价（元）</td>
     <td>数量</td>
     <td>总金额（元）</td>
	  <td>状态</td>
     <td>操作</td>
    </tr>
   </thead>
   </table>
   <br>
   <?php foreach(  $list as $val  ){?>
   <table class="table table-condensed table-bordered order">
   <colgroup><col width="30%"/><col width="10%"/><col width="10%"/><col width="10%"/><col width="15%"/><col width="15%"/></colgroup>
   <tbody>

	<tr class="list-hd <?php if($val['oType'] == '1'){ ?>book<?php }else if($val['oType'] == '2'){ ?>obligate<?php }else if($val['oType'] == '3'){ ?>tailproduct<?php } ?>">
    <td colspan="6">
		<!-- <input type="checkbox" name="orderId[]" value="<?php //echo $val['orderId'];?>"/> -->
   <span class="first"><?php echo $val['orderType']?>：<?php echo $val['orderId'];?></span>
		<span>客户：<?php echo $val['member']['shortname'];?></span>
		<span>业务员：<?php echo $val['username'];?></span>
		<span><?php echo $val['createTime'];?></span>
	  </td>
  </tr>


	<?php $count = count($val['products']);
		foreach( $val['products'] as $key=>$pval  ){
		$pval['num'] = ($val['state']>2 && $val['state']!='7')?$pval['packingNum']:$pval['num'];
	?>
	 <tr class="list-bd">
   <td>
   <div class="c-img pull-left">
     <a href="javascript:"><img src="<?php echo $this->img().$pval['mainPic'];?>_50" alt="" width="50" height="50"/></a>
   </div>
	 <div class="product-title"><?php echo $pval['title'];?></div>
	 <p><?php echo $pval['singleNumber'].' '.$pval['color'];?></p>
	 </td>
     <td> <?php echo Order::priceFormat($pval['price']);?></td>
     <td><?php echo Order::quantityFormat($pval['num']);?> <?php echo isset($units[$pval['productId']])?$units[$pval['productId']]['unit']:'';?></td>
	 <?php if($key=='0'){?>
     <td rowspan="<?php echo $count;?>">
		<?php echo Order::priceFormat($val['realPayment']);?>
		<?php if( $val['freight'] >0 ){ ?>
		<br/>
		（含运费<?php echo $val['freight']?>元）
		<?php } ?>
	</td>
	<td rowspan="<?php echo $count;?>" >
		<p><span class="text-danger"><?php echo $val['stateTitle'];?></span></p>
		<p><?php echo $val['payModel']?$payments[$val['payModel']]['paymentTitle']:'未付款'?></p>
		<?php if(isset($val['keep'])) { ?>
			<p>留货<?php echo $val['keep']['stateTitle']; ?></p>
			<p>留货至：<?php echo $val['keep']['expireTime']; ?></p>
		<?php } ?>
	 </td>
     <td rowspan="<?php echo $count;?>">
		<?php $this->beginContent('_state',array('val'=>$val,'state'=>$state));$this->endContent();?>
	 </td>
	 <?php } ?>
    </tr>
	  <?php } ?>
	  </tbody>
  </table>
  <br>
   <?php }?>


  <div class="clearfix well well-sm list-well">
   <div class="pull-left form-inline">
    <!-- <label class="checkbox-inline"> <input type="checkbox" class="checkedall"/> 全选 </label>
    <button class="btn btn-sm btn-default">批量导出</button> -->
   </div>
    <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>

<?php $this->beginContent('_cancle',array('reasons'=>$reasons));$this->endContent();?>
<?php $this->beginContent('//layouts/_del');$this->endContent();?>
<script>seajs.use('statics/app/order/js/order.js');</script>