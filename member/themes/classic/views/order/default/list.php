<link rel="stylesheet" href="/app/member/trade/css/style.css"/>
<div class="pull-right frame-content">
      <div class="frame-tab">
        <ul class="clearfix list-unstyled frame-tab-hd">
		<?php foreach ($tabs as $key=>$val){ ?>
          <li <?php if($type == $key){ echo ' class="active"';}?>>
			<a href="<?php echo $val['url']?>">
				<?php echo $val['title']?><?php if( $key>0 ){ ?><b class="text-warning"><?php echo $val['count']?></b><?php }?>
			</a>
		  </li>
		<?php }?>
        </ul>
      </div>
      <div class="frame-list buylist">
        <div class="frame-list-search">
          <form action="<?php $this->createUrl('index')?>">
            <select name="type" class="form-control input-xs">
			<?php foreach ($tabs as $key=>$val){ ?>
			 <option value="<?php echo $key?>" <?php if($type == $key){ echo 'selected';}?>><?php echo $val['title']?></option>
			<?php }?>
            </select>
            <label class="order-time">交易时间：<input type="text" name="createTime1" class="form-control input-xs input-date" id="starttime" readonly value="<?php echo $condition['createTime1']?>">-<input type="text" name="createTime2" class="form-control input-xs input-date" id="endtime" readonly value="<?php echo $condition['createTime2']?>"></label>
            <input type="text" name="orderId" class="form-control input-xs" placeholder="请输入订单编号" value="<?php echo $condition['orderId']?>">
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
                <td colspan="6" <?php if($val['oType'] == '1'){ ?>class="book"<?php }else if($val['oType'] == '2'){ ?>class="obligate"<?php }else if($val['oType'] == '3'){ ?>class="tailproduct"<?php } ?>>
                  <div class="order-info inline-block pull-left">
                    <span><?php echo $val['orderType'].'：'.$val['orderId']; ?></span>
					<?php if( $this->userType =='saleman' ) { ?>
					 <span>客户名称：<?php echo $val['companyname'];?></span>
					<?php } ?>
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
					<?php if ( $val['freight'] >0) { ?>
						<p class="text-minor">(含运费:<?php echo $val['freight']; ?>)</p>
					<?php } ?>
                </td>
                <td rowspan="<?php echo $rows;?>" class="status">
			<?php if( array_key_exists('keep', $val ) ) { ?>
			<p><?php echo $val['keep']['stateTitle']; ?></p>
			<?php if( $val['keep']['state']!= '2' ) { ?>
				<p>留货至：</p>
				<p><?php echo $val['keep']['expireTime']; ?></p>
			<?php }?>
			<?php }else{?>
			<p><?php echo $val['stateTitle'];?></p>
			<?php echo $val['payModel']? $payments[$val['payModel']]['paymentTitle']:'未付款'?><br />
			<?php //$this->beginContent('_pay',array('payState' => $val['payState'] ,'payModel'=>$val['payModel'],'paymentlist'=>$val['paymemt'],'payments' => $payments));$this->endContent();?>
			<?php }?>
                </td>
                <td rowspan="<?php echo $rows;?>" class="operations" data-orderid="<?php echo $val['orderId']; ?>">
			<?php $this->beginContent('_state',array('buttons' => $val['buttons'],'orderId'=>$val['orderId'] ));$this->endContent();?>
               </td>
                <?php } ?>
              </tr>
              <?php } ?>
            </tbody>
            <?php } ?>
            <?php } ?>
          </table>
        </div>
        <div class="cancel-order-tip hide">
					<p>您确定要取消该订单吗？取消订单后，不能恢复。</p>
					<p>
					  <select name="closeReason">
							<?php foreach( $closeReasons as $val ) { ?>
							<option value="<?php echo $val;?>"><?php echo $val;?></option>
							<?php } ?>
						</select>
					</p>
				</div>
      </div>
    </div>
    <script>seajs.use('app/member/trade/js/buylist.js');</script>