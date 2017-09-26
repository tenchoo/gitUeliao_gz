<link rel="stylesheet" href="/app/member/trade/css/style.css"/>
<div class="pull-right frame-content">
<div class="frame-tab">
  <ul class="clearfix list-unstyled frame-tab-hd">
    <li class="active">
      <a href="javascript:">退货管理</a>
    </li>
  </ul>
</div>
<div class="frame-list">
    <div class="frame-list-search">
      <form action="<?php $this->createUrl('index')?>">
		<select name="state" class="form-control input-xs">
			<option value="" >全部</option>
			<option value="0" <?php if( $state === 0 ){ echo 'selected';}?>>待审核</option>
        </select>
        <label class="order-time">交易时间：<input type="text" name="createTime1" class="form-control input-xs input-date" id="starttime" readonly style="width:130px" value="<?php echo $createTime1?>">-<input type="text" name="createTime2" class="form-control input-xs input-date" id="endtime" readonly style="width:130px" value="<?php echo $createTime2?>"></label>
        <input type="text" name="orderId" class="form-control input-xs" placeholder="请输入订单编号" value="<?php echo $orderId?>">
        <button class="btn btn-xs btn-cancel" type="submit">查找</button>
      </form>
    </div>
    <div class="frame-list-bd buylist">
      <table>
        <thead>
          <tr><th class="title">产品信息</th><th width="100">单价</th><th width="100">数量</th><th width="100">金额(元)</th><th width="80">操作</th></tr>
          <tr class="list-page-head"><td colspan="5">
            <?php $this->widget('widgets.ZPagerNavigate', array('pages' => $pages,'type' => "mini",));?>
          </td></tr>
        </thead>
        <tfoot class="list-page-foot">
          <tr class="spacing"><td colspan="5"></td></tr>
          <tr><td colspan="5">
      <?php $this->widget('widgets.ZPagerNavigate', array('pages' => $pages, 'maxLinkCount' => 10));?>
          </td></tr>
        </tfoot>
        <?php if( !empty( $list ) ) { ?>
        <?php foreach( $list as $val ) { ?>

        <tbody class="list-page-body">
          <tr class="spacing"><td colspan="5"></td></tr>
          <tr class="list-body-hd">
            <td colspan="5">
              <div class="order-info inline-block pull-left">
      					<span>退货单号：<?php echo $val['refundId'];?></span>
      					<span>订单编号：<?php echo $val['orderId'];?></span>
      					<span>客户：<?php echo $val['member']['shortname'];?></span>
    					</div>
    					<div class="inline-block pull-right">
      					<span><?php echo $val['createTime']; ?></span>
              </div>
            </td>
          </tr>
          <?php $rows = count($val['products']);
          	foreach( $val['products'] as $key=>$pval ) {
			if( $pval['tailId']>0 ){
				$url = $this->homeUrl.'/tailproduct/detail-'.$pval['tailId'].'.html';
			}else{
				$url = $this->homeUrl.'/product/detail-'.$pval['productId'].'.html';
			}
		?>
          <tr class="list-body-bd">
            <td class="title">
              <div class="c-img pull-left">
		  <?php echo CHtml::link( CHtml::image($this->imageUrl($pval['mainPic'],50,false), $pval['title'],array('height'=>50,'width'=>50)),$url ,array('target'=>'_blank') );?>
		  </div>
              <div class="product-title">
                <?php echo CHtml::link( '【'.$pval['serialNumber'].'】'.$pval['title'] , $url ,array('target'=>'_blank') );?>
              </div>
              <p class="text-minor"><span><?php echo $pval['singleNumber'].' '.$pval['color'];?></span></p>
            </td>
            <td class="price"><?php echo Order::priceFormat($pval['price']);?></td>
            <td class="num"><?php echo Order::quantityFormat($pval['num']);?></td>
            <?php if($key=='0'){ ?>
            <td rowspan="<?php echo $rows;?>" class="total-price">
            	<?php echo Order::priceFormat($val['realPayment']); ?>
            </td>
            <td rowspan="<?php echo $rows;?>" class="operations" data-orderid="<?php echo $val['orderId']; ?>">
			<a href="<?php echo $this->createUrl('view',array('id'=>$val['refundId']));?>" class="text-link">查看退款单</a><br>
			<?php if( $val['canPrint'] ){?>
			<a href="<?php echo $this->createUrl('print',array('id'=>$val['refundId']));?>" class="print text-link">打印退款单</a>
			<?php } ?>
           </td>
            <?php } ?>
          </tr>
          <?php } ?>
        </tbody>
        <?php } ?>
        <?php } ?>
      </table>
    </div>
</div>
</div>
<script>
seajs.use('app/member/settlement/js/list.js');
</script>