<div class="pull-right frame-content">
  <link rel="stylesheet" href="/modules/button/css/style.css"/>
  <link rel="stylesheet" href="/modules/form/css/style.css"/>
  <link rel="stylesheet" href="/app/member/applyprice/css/style.css"/>
  <div class="frame-list">
    <div class="frame-tab">
      <ul class="clearfix list-unstyled frame-tab-hd">
        <li class="active">
          <a href="javascript:">添加产品</a>
        </li>
      </ul>
    </div>
    <div class="frame-list-search text-center">
      <div class="pull-left">
        客户名称：<?php echo $companyname;?>
      </div>
      <div class="pull-right">
        <form action="" method="get">
          <input type="text" class="form-control input-xs" placeholder="输入产品编号" value="<?php echo $keyword;?>" name="keyword">
          <input type="hidden" name="memberId" value="<?php echo $memberId;?>">
          <button class="btn btn-xs btn-cancel">搜索</button>
        </form>
      </div>
    </div>
    <div class="frame-list-bd">
      <table>
        <thead>
          <tr>
            <th class="title">产品信息</th>
            <th width="180">单价</th>
            <th width="180">批发价</th>
          </tr>
          <tr class="list-page-head">
            <td colspan="3">
              <div class="pull-right">
                <?php $this->widget('widgets.ZPagerNavigate', array('pages' => $pages,'type' => "mini",));?>
              </div>
            </td>
          </tr>
        </thead>
        <tfoot class="list-page-foot">
          <tr>
            <td colspan="3">
              <div class="pull-right">
                <?php $this->widget('widgets.ZPagerNavigate', array('pages' => $pages, 'maxLinkCount' => 10));?>
              </div>
            </td>
          </tr>
        </tfoot>
        <tbody class="list-page-body">
            <?php foreach ($list as $val){
				$url = $this->homeUrl.'/product/detail-'.$val['productid'].'.html';
             ?>
            <tr>
            <td>
              <div class="c-img pull-left"><?php echo CHtml::link( CHtml::image( $val['picture'] , $val['title'],array('height'=>50,'width'=>50) ),$url ,array('target'=>'_blank') );?></div>
              <div class="product-title"><?php echo CHtml::link( '【'.$val['serial'].'】'.$val['title'] , $url ,array('target'=>'_blank') );?></div>
            </td>
            <td><?php echo $val['price']?>元/<?php echo $val['unit']?></td>
            <td>
              <span class="b-price"><?php echo $val['tradePrice']?>元/<?php echo $val['unit']?></span>
              <?php if( $val['state'] == 0 ){ ?>
              <?php if( $val['hasApply'] ){ ?>
              <span class="text-minor">已添加</span>
              <?php }else{ ?>
              <a href="<?php echo $this->createUrl('add',array('memberId'=>$memberId,'productId'=>$val['productid']));?>" class="text-link">添加</a>
              <?php }?>
              <?php }else{ ?>
              <span class="text-minor">已下架</span>
              <?php }?>
            </td>
          </tr>
          <?php } ?>

        </tbody>
      </table>
    </div>
  </div>
</div>