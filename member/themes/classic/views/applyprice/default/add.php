<div class="pull-right frame-content">
  <div class="frame-list">
  <link rel="stylesheet" href="/modules/button/css/style.css"/>
  <link rel="stylesheet" href="/modules/form/css/style.css"/>
  <link rel="stylesheet" href="/app/member/applyprice/css/style.css"/>
    <form action="" method="post">
    <div class="frame-tab">
      <ul class="clearfix list-unstyled frame-tab-hd">
        <li class="active">
          <a href="javascript:">批发申请</a>
        </li>
      </ul>
    </div>
    <div class="frame-list-search">
        客户名称：
        <select class="form-control input-xs" <?php if($memberId){?>disabled<?php }?>>
          <option value="">请选择</option>
          <?php foreach ($memberList  as $val){ ?>
          <option value="<?php echo $val['memberId'];?>"<?php if($val['memberId'] == $memberId){?> selected<?php } ?>><?php echo $val['companyname'];?></option>
          <?php }?>
        </select>
        <input type="hidden" name="memberId" value="<?php echo $memberId;?>">
    </div>
    <div class="frame-list-bd">
      <table>
        <thead>
          <tr>
            <th class="title">产品信息</th>
            <th width="180">单价</th>
            <th width="180">批发价</th>
          </tr>
        </thead>
        <?php if( !empty($productInfo) ) { ?>
        <?php
          $api = new ApiClient('www','service',false);
          $url = $api->createUrl('/product/detail',array('id'=>$productInfo['productId']));
        ?>
        <tfoot>
          <tr>
            <td colspan="3" class="text-center">
              <button href="<?php echo $this->createUrl('searchproduct',array('memberId'=>''));?>" class="btn btn-cancel add-product">重选产品</button>
              <button type="submit" class="btn btn-success">提交申请</button>
            </td>
          </tr>
        </tfoot>
        <tbody class="list-page-body">
          <tr>
            <td>
              <div class="c-img pull-left"><?php echo CHtml::link( CHtml::image($this->imageUrl($productInfo['mainPic'],50,false), $productInfo['title'],array('height'=>50,'width'=>50) ),$url ,array('target'=>'_blank') );?></div>
              <div class="product-title"><?php echo CHtml::link( '【'.$productInfo['serialNumber'].'】'.$productInfo['title'] , $url ,array('target'=>'_blank') );?></div>
            </td>
            <td><?php echo $productInfo['price']?>元/<?php echo $productInfo['unit']?></td>
            <td><input type="text" name="applyPrice" value="<?php echo $productInfo['tradePrice']?>" class="form-control input-xs price-only" maxlength="7">元/<?php echo $productInfo['unit']?><input type="hidden" name="productId" value="<?php echo $productInfo['productId'];?>"></td>
          </tr>
        </tbody>
        <?php }else{?>
        <tbody class="list-page-body">
          <tr>
            <td colspan="3" class="text-center">
              <br>
              还未添加产品，现在去 <a href="<?php echo $this->createUrl('searchproduct',array('memberId'=>''));?>" class="text-link add-product">添加产品</a>
              <br>
              <br>
          </tr>
        </tbody>
        <?php }?>
      </table>
    </div>
    </form>
  </div>
</div>
<script>
  seajs.use('app/member/applyprice/js/add');
</script>

