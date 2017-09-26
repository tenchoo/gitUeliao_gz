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
        客户名称：<?php echo $data['companyname']?>
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
        <?php
          $api = new ApiClient('www','service',false);
          $url = $api->createUrl('/product/detail',array('id'=>$data['productId']));
        ?>
        <tfoot>
          <tr>
            <td colspan="3" class="text-center">
              <button type="submit" class="btn btn-success">提交申请</button>
            </td>
          </tr>
        </tfoot>
        <tbody class="list-page-body">
          <tr>
            <td>
              <div class="c-img pull-left"><?php echo CHtml::link( CHtml::image($this->imageUrl($data['mainPic'],50,false), $data['title'],array('height'=>50,'width'=>50) ),$url ,array('target'=>'_blank') );?></div>
              <div class="product-title"><?php echo CHtml::link( '【'.$data['serialNumber'].'】'.$data['title'] , $url ,array('target'=>'_blank') );?></div>
            </td>
            <td><?php echo $data['price']?>元/<?php echo $data['unitName']?></td>
            <td><input type="text" name="applyPrice" value="<?php echo $data['applyPrice']?>" class="form-control input-xs price-only">元/<?php echo $data['unitName']?></td>
          </tr>
        </tbody>
      </table>
    </div>
    </form>
  </div>
</div>
<script>
  seajs.use('app/member/applyprice/js/add')
</script>