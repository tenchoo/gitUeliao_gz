<link rel="stylesheet" href="/app/member/information/css/style.css"/>
 <div class="pull-right frame-content">
      <div class="frame-list information">
        <div class="frame-list-bd">
          <table>
            <thead>
              <tr><th colspan="2" class="title">信息</th><th width="100">操作</th></tr>
              <tr class="list-page-head"><td colspan="4">
                <div class="pull-left">
                  <label for="selectall" class="checkbox-inline"><input type="checkbox">全选</label>
                  <a href="" class="btn btn-cancel btn-xs">批量删除</a>
                </div>
                <?php $this->widget('widgets.ZPagerNavigate', array(
				        'pages' => $pages,
				        'type' => "mini"
				        )
				    );?>
              </td></tr>
            </thead>
            <tfoot class="list-page-foot">
              <tr class="spacing"><td colspan="4"></td></tr>
              <tr><td colspan="4">
                <div class="pull-left">
                  <label for="selectall" class="checkbox-inline"><input type="checkbox">全选</label>
                  <a href="" class="btn btn-cancel btn-xs">批量删除</a>
                </div>
                <?php $this->widget('widgets.ZPagerNavigate', array(
				        'pages' => $pages,
				        'maxLinkCount' => 10, //显示分页数量
				        )
				    );?>
              </td></tr>
            </tfoot>
            <tbody class="list-page-body">
            <?php if($model){?>
            <?php foreach ($model as $val){?>
              <tr>
                <td class="check"><input type="checkbox" name="ids[]" value="<?php echo $val->logisticsMessageId?>"></td>
                <td>
                  <div class="c-img pull-left">
				  <img src="<?php echo $this->img(false).$val->product->mainPic.'_50';?>" />
				  </div>
                  <div class="product-title"> 订单号：<?php echo $val->orderId;?><br/>
				  <?php if($val->state == '1'){ ?>
					卖家已发货，请注意查收
				  <?php }else if($val->state == '2'){ ?>
					产品已签收
				  <?php } ?>
				  </div>
				  <?php //if($val->state=='0'){echo '(new)';}?>
				  <?php //echo $val->createTime;?>
			   </td>
                <td>
				<?php if(!empty($val->deliveryId)){ ?>
				<a href="<?php echo $this->createUrl('/order/default/expressinfo',array('packingId'=>$val->deliveryId));?>" class='text-link'>查看</a>
				<?php }else{ ?>
				<a href="<?php echo $this->createUrl('/order/default/expressinfo',array('orderId'=>$val->orderId));?>" class='text-link'>查看</a>
				<?php } ?>
				<a href="<?php echo $this->createUrl('dellogistics',array('ids'=>$val->logisticsMessageId));?>" class='text-link'>删除</a>
                </td>
              </tr>
              <?php }?>
              <?php }else{?>
              	<p>暂无消息</p>
              <?php }?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
<script>seajs.use('app/member/trade/js/addresses.js');</script>