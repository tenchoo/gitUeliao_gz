
<link rel="stylesheet" href="/app/member/trade/css/style.css"/>
<div class="pull-right frame-content">
  <div class="frame-tab">
    <ul class="clearfix list-unstyled frame-tab-hd">
      <li class="active">
        <a href="javascript:">取消订单管理</a>
      </li>
    </ul>
  </div>
    <div class="frame-list buylist">
        <div class="frame-list-search">
          <form action="<?php $this->createUrl('index')?>">
            <!--select name="type" class="form-control input-xs">

            </select-->
            <label class="order-time">交易时间：<input type="text" name="createTime1" class="form-control input-xs input-date" id="starttime" readonly value="<?php echo $createTime1?>">-<input type="text" name="createTime2" class="form-control input-xs input-date" id="endtime" readonly value="<?php echo $createTime2?>"></label>
            <input type="text" name="orderId" class="form-control input-xs" placeholder="请输入订单编号" value="<?php echo $orderId?>">
            <button class="btn btn-xs btn-cancel" type="submit">查找</button>
          </form>
        </div>
        <div class="frame-list-bd">
          <table>
            <thead>
              <tr><th class="title">产品信息</th><th width="80">单价</th><th width="50">数量</th><th width="100">实付款</th><th width="80">交易状态</th><th width="80">交易操作</th></tr>
              <tr class="list-page-head"><td colspan="6">
                <?php $this->widget('widgets.ZPagerNavigate', array('pages' => $pages,'type' => "mini",));?>
              </td></tr>
            </thead>
            <tfoot class="list-page-foot">
              <tr class="spacing"><td colspan="6"></td></tr>
              <tr><td colspan="6">
          <?php $this->widget('widgets.ZPagerNavigate', array('pages' => $pages, 'maxLinkCount' => 10));?>
              </td></tr>
            </tfoot>
            <?php if( !empty( $list ) ) { ?>
            <?php foreach( $list as $val ) { ?>
            <tbody class="list-page-body">
              <tr class="spacing"><td colspan="6"></td></tr>
              <tr class="list-body-hd">
                <td colspan="6">
                  <div class="order-info inline-block">
                    <span><?php echo CHtml::link('订单编号：'.$val['orderId'],array('view','id'=>$val['orderId'] )  );?></span>
                  </div>
                </td>
              </tr>
              <?php $rows = count($val['products']);
              	foreach( $val['products'] as $key=>$pval ) {
					$url = $this->homeUrl.'/product/detail-'.$pval['productId'].'.html';
				?>
              <tr class="list-body-bd">
                <td class="title">
                  <div class="c-img pull-left">
				  <?php echo CHtml::link( CHtml::image($this->imageUrl($pval['mainPic'],50,false), $pval['title'] ),$url ,array('target'=>'_blank') );?>

				  </div>
                  <div class="product-title">
                    <?php echo CHtml::link( '【'.$pval['serialNumber'].'】'.$pval['title'] , $url ,array('target'=>'_blank') );?>
                  </div>
                  <p class="text-minor"><span><?php echo $pval['specifiaction'];?></span></p>
                </td>
                <td class="price"><?php echo Order::priceFormat( $pval['price'] );?></td>
                <td class="num"><?php echo Order::quantityFormat( $pval['num'] );?></td>
                <?php if($key=='0'){ ?>
                <td rowspan="<?php echo $rows;?>" class="total-price">
                	<?php echo Order::priceFormat( $val['realPayment'] ); ?>
					<?php if ( $val['freight'] >0) { ?>
						<p class="text-minor">(含运费:<?php echo Order::priceFormat( $val['freight'] ); ?>)</p>
					<?php } else { ?>
						<p class="text-minor">(免运费)</p>
					<?php } ?>
                </td>
                <td rowspan="<?php echo $rows;?>" class="status">
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
                <td rowspan="<?php echo $rows;?>" class="operations">
				<?php if( $this->userType =='saleman' && $val['state'] != '7' && $val['cState'] == '0' ) { ?>
					<a href="<?php echo $this->createUrl('checkclose',array('id'=>$val['id']));?>" target="_blank"  class="text-link">审核</a><br/>
				<?php } ?>

				<?php echo CHtml::link('订单详情',array('view','id'=>$val['orderId'] ),array('class'=>'text-link')  );?>
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
  seajs.use('libs/my97datepicker/4.8.0/WdatePicker.js',function () {
    $('.frame-list-search').on('click', '.input-date', WdatePicker);
  });
</script>