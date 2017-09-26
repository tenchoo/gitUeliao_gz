<link rel="stylesheet" href="/app/member/trade/css/style.css"/>
 <div class="pull-right frame-content">
  <div class="frame-tab">
    <ul class="clearfix list-unstyled frame-tab-hd">
      <li class="active">
        <a href="javascript:">客户反馈</a>
      </li>
    </ul>
  </div>
  <div class="frame-box comment">
    <div class="frame-box-hd">订单编号：<?php echo $model->orderId;?></div>
    <div class="frame-box-bd">
      <form  method="post" action="">
      <table>
        <tbody>
        <?php foreach( $model->products as $pval) :?>
          <tr>
            <td class="title">
              <div class="c-img pull-left"><img src="<?php $this->imageUrl($pval['mainPic'],50)?>" width="50" height="50"/></div>
              <div class="product-title"><?php echo $pval['serialNumber']?> <?php echo $pval['title']?></div>
              <p class="text-minor"><?php echo $pval['specifiaction']?></p>
            </td>
            <td><div class="form-group textarea-group"><textarea name="data[<?php echo $pval['orderProductId']?>]"></textarea></div></td>
          </tr>
          <?php endforeach;?>
        </tbody>
      </table>
      <div class="text-center"><button class="btn btn-success btn-xs" type="submit" data-loading="提交信息...">提交信息</button></div>
      </form>
    </div>
  </div>
</div>
<script>
  seajs.use('app/member/trade/js/comment.js');
</script>