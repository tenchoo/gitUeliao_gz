<div class="pull-right frame-content">
  <div class="frame-list">
    <link rel="stylesheet" href="/modules/button/css/style.css"/>
    <link rel="stylesheet" href="/modules/form/css/style.css"/>
    <link rel="stylesheet" href="/app/member/applyprice/css/style.css"/>
    <div class="frame-tab">
      <ul class="clearfix list-unstyled frame-tab-hd">
        <li class="active">
          <a href="javascript:">批发管理</a>
        </li>
      </ul>
    </div>
    <div class="frame-list-search">
      <div class="pull-left">
        <form action="<?php echo $this->createUrl('index');?>" method="get">
          <select name="state" class="form-control input-xs">
            <option value="">所有状态</option>
            <?php foreach ($stateTitle as $key => $val){ ?>
            <option value="<?php echo $key;?>" <?php if( !is_null($state) && $state == $key){ ?> selected <?php }?>><?php echo $val;?></option>
            <?php }?>
          </select>
          <input type="text" class="form-control input-xs" placeholder="请输入客户名称或单号" value="<?php echo $keyword;?>" name="keyword">
          <button type="submit" class="btn btn-xs btn-cancel">查找</button>
        </form>
      </div>
      <div class="pull-right">
        <a href="<?php echo $this->createUrl('add');?>" class="btn btn-success btn-xs">批发价格申请</a>
      </div>
    </div>
    <div class="frame-list-bd">
      <table>
        <thead>
          <tr><th class="title">产品信息</th><th width="140">批发价</th><th width="140" class="text-center">状态</th><th width="110" class="text-center">操作</th></tr>
          <tr class="list-page-head"><td colspan="4">
            <div class="pull-right">
              <?php $this->widget('widgets.ZPagerNavigate', array('pages' => $pages,'type' => "mini",));?>
            </div>
          </td></tr>
        </thead>
        <tfoot class="list-page-foot">
          <tr class="spacing"><td colspan="4"></td></tr>
          <tr><td colspan="4">
            <div class="pull-right">
              <?php $this->widget('widgets.ZPagerNavigate', array('pages' => $pages, 'maxLinkCount' => 10));?>
            </div>
          </td></tr>
        </tfoot>
        <?php foreach ($list as $val){
         $url = $this->homeUrl.'/product/detail-'.$val['productId'].'.html';
        ?>
        <tbody class="list-page-body">
          <tr class="spacing"><td colspan="4"></td></tr>
          <tr class="title"><td>单号：<?php echo $val['applyPriceId'];?></td><td colspan="2">客户名称：<?php echo $val['companyname']?></td><td class="text-center"><?php echo $val['createTime']?></td></tr>
          <tr>
            <td>
              <div class="c-img pull-left"><?php echo CHtml::link( CHtml::image($this->imageUrl($val['mainPic'],50,false), $val['title'],array('height'=>50,'width'=>50) ),$url ,array('target'=>'_blank') );?></div>
              <div class="product-title"><?php echo CHtml::link( '【'.$val['serialNumber'].'】'.$val['title'] , $url ,array('target'=>'_blank') );?></div>
            </td>
            <td><?php echo $val['applyPrice']?>元/<?php echo $val['unitName']?></td>
            <td class="stat <?php switch ($val['state']) {
              case 0:
                $style = 'text-warning';
                break;
              case 2:
                $style = 'text-success';
                break;
              case 3:
                $style = 'text-minor';
                break;
              default:
                $style = '';
                break;
            }
            echo $style;
             ?>"><?php echo $val['stateTitle']?></td>
            <td class="option">
              <?php if($val['state']==1){ ?>
              <a href="<?php echo $this->createUrl('invalid',array('id'=>$val['applyPriceId']));?>" class="text-link invalid">取消</a>
			   <?php }else if($val['state']== 3){ ?>
              <?php }else { ?>
              <a href="<?php echo $this->createUrl('edit',array('id'=>$val['applyPriceId']));?>" class="text-link">修改</a>
              <a href="<?php echo $this->createUrl('del',array('id'=>$val['applyPriceId']));?>" class="text-link del">删除</a>
              <?php } ?>
            </td>
          </tr>
        </tbody>
        <?php } ?>
      </table>
    </div>
  </div>
</div>
<script>seajs.use('app/member/applyprice/js/list')</script>