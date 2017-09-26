<link rel="stylesheet" href="/themes/classic/statics/app/order/css/style.css" />
  <div class="panel panel-default search-panel">
   <div class="panel-body">
    <form role="search" class="pull-left form-inline" action="<?php echo $this->createUrl('index');?>">
	生成时间:  <input type="text" name="createTime1" value="<?php echo $createTime1; ?>" class="form-control input-sm input-date" id="starttime" readonly/>
		到 <input type="text" name="createTime2" value="<?php echo $createTime1; ?>" class="form-control input-sm input-date" id="endtime" readonly/>
     <div class="form-group">
      <input type="text" name="orderId" value="<?php echo $orderId;?>" placeholder="请输入订单编号" class="form-control input-sm" />
     </div>
     <button class="btn btn-sm btn-default">查找</button>
    </form>
   </div>
  </div>
  <div class="clearfix well well-sm list-well">
  <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>
  <table class="table table-condensed table-bordered">
   <thead>
    <tr>
     <td>产品信息</td>
	 <td width="12%">单价(元)</td>
     <td width="12%">结算数量</td>
	 <td width="15%">金额(元)</td>
	 <td width="12%">状态</td>
     <td width="12%">操作</td>
    </tr>
   </thead>
   </table>
    <?php foreach(  $list as $val  ){?>
	<br/>
    <table class="table table-condensed table-bordered">
   <thead>
   <tbody>
	<tr class="list-hd">
    <td colspan="7">
<!--列表表头只保留订单编号，客户，跟单员，结算单号（长度够则放，不够可以不放)-->
		<span class="first">结算单号：<?php echo $val['settlementId'];?></span>
		<span>订单编号：<?php echo $val['orderId'];?></span>
		<span>客户：<?php echo $val['member']['shortname'];?></span>
		<span>业务员：<?php echo $val['salesman'];?></span>
	  </td>
  </tr>
	<?php $count = count($val['products']);foreach( $val['products'] as $key=>$pval  ){ ?>
	<tr class="list-bd">
   <td>
	 <div class="c-img pull-left">
     <img src="<?php echo $this->img().$pval['mainPic'];?>_50" alt="" width="50" height="50"/>
   </div>
	 <div class="product-title"><?php echo $pval['title'];?></div>
	 <p><?php echo $pval['singleNumber'].' '.$pval['color'];?></p></td>
   <td width="12%"><?php echo Order::priceFormat($pval['price']);?></td>
   <td width="12%"><?php echo Order::quantityFormat($pval['deliveryNum']);?></td>
	<?php if($key=='0'){?>
	<td rowspan="<?php echo $count;?>" width="15%"><?php echo Order::priceFormat($val['realPayment']);?></td>
	<td rowspan="<?php echo $count;?>" width="12%">已结算</td>
    <td rowspan="<?php echo $count;?>" width="12%">
		<a href="<?php echo $this->createUrl('view',array('id'=>$val['settlementId']));?>">查看结算单</a><br>
		<a href="<?php echo $this->createUrl('print',array('id'=>$val['settlementId']));?>" class="print">打印结算单</a>
	</td>
	<?php } ?>
    </tr>
	<?php } ?>
   </tbody>
  </table>
   <?php }?><br>
  <div class="clearfix well well-sm list-well">
    <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>
<script>seajs.use('statics/app/order/js/applypricelist.js');</script>