<link rel="stylesheet" href="/themes/classic/statics/app/order/css/style.css" />
  <div class="panel panel-default search-panel">
   <div class="panel-body">
    <form role="search" class="pull-left form-inline" action="<?php echo $this->createUrl(Yii::app()->getController()->getAction()->id);?>">
	成交时间:  <input type="text" name="createTime1" value="<?php echo $createTime1; ?>" class="form-control input-sm input-date" readonly id="starttime"/>
		到 <input type="text" name="createTime2" value="<?php echo $createTime2; ?>" class="form-control input-sm input-date" readonly id="endtime"/>
     <div class="form-group">
      <input type="text" name="orderId" value="<?php echo $orderId;?>" placeholder="请输入订单编号" class="form-control input-sm" />
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
  <colgroup><col width="40%"/><col width="10%"/><col width="10%"/><col width="10%"/><col width="15%"/><col width="15%"/></colgroup>
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
   <colgroup><col width="40%"/><col width="10%"/><col width="10%"/><col width="10%"/><col width="15%"/><col width="15%"/></colgroup>
   <tbody>

	<tr class="list-hd <?php if($val['oType'] == '1'){ ?>book<?php }else if($val['oType'] == '2'){ ?>obligate<?php }else if($val['oType'] == '3'){ ?>tailproduct<?php } ?>">
    <td colspan="6">
		<!-- <input type="checkbox" name="orderId[]" value="<?php //echo $val['orderId'];?>"/> -->
		<span class="first"><?php echo $val['orderType']?>：<?php echo $val['orderId'];?></span>
		<span>客户：<?php echo $val['member']['companyname'];?></span>
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
		<?php echo Order::priceFormat($val['realPayment']);?><br/>
		（运费<?php echo $val['freight']?>元）
	</td>
	<td rowspan="<?php echo $count;?>" >
		 <?php if($val['state'] == '7'){ ?>
			 已取消
		 <?php }else if( $val['cState'] == '0' ){ ?>
			 待审核
		 <?php }else if( $val['cState'] == '1' ){ ?>
			 已取消
		<?php }else{ ?>
			 不同意取消
		 <?php } ?>
	 </td>
     <td rowspan="<?php echo $count;?>">
		<?php if( $val['state'] != '7' && $val['cState'] == '0' ) { ?>
			<a href="<?php echo $this->createUrl('checkclose',array('id'=>$val['id']));?>" class="text-link">审核</a><br/>
		<?php } ?>
		<?php echo CHtml::link('订单详情',array('view','id'=>$val['id'] )  );?>
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
