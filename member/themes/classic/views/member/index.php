<div class="pull-right frame-content">
      <div class="frame-list">
      <link rel="stylesheet" href="/modules/button/css/style.css"/>
      <link rel="stylesheet" href="/modules/form/css/style.css"/>
        <div class="frame-tab">
          <ul class="clearfix list-unstyled frame-tab-hd">
            <li class="active">
              <a href="javascript:">客户管理</a>
            </li>
          </ul>
        </div>
        <div class="frame-list-search">
          <form action="<?php echo $this->createUrl('index');?>" method="get">
            <input type="text" class="form-control input-xs" placeholder="手机号码" name="tel" value="<?php echo $tel;?>"/>
            <input type="text" class="form-control input-xs" placeholder="企业名称" name="corp" value="<?php echo $corp;?>"/>
            <button class="btn btn-xs btn-cancel">查找</button>
          </form>
        </div>
        <div class="frame-list-bd">
          <table>
            <thead>
              <tr><th class="title">手机号码</th><th width="300">企业名称</th><th width="150">状态</th><th width="100">操作</th></tr>
              <tr class="list-page-head"><td colspan="4">
                <?php $this->widget('widgets.ZPagerNavigate', array('pages' => $pages,'type' => "mini",));?>
              </td></tr>
            </thead>
            <tfoot class="list-page-foot">
              <tr class="spacing"><td colspan="4"></td></tr>
              <tr><td colspan="4">
                <?php $this->widget('widgets.ZPagerNavigate', array('pages' => $pages, 'maxLinkCount' => 10));?>
              </td></tr>
            </tfoot>
            <tbody class="list-page-body">
              <?php foreach ($list as $val){ ?>
              <tr>
                <td><?php echo $val['phone']?></td>
                <td><?php echo $val['companyname']?></td>
                <td class="<?php if ( $val['isCheck'] == 1 ) { echo 'text-minor';} else if ( $val['isCheck'] == 2 ) { echo 'text-warning';}?>"><?php echo $val['checkTitle']?></td>
                <td>
                  <a href="<?php echo $this->createUrl('info',array('id'=>$val['memberId']));?>" class="text-link">编辑</a>
                  <a href="<?php echo $this->createUrl('info',array('type'=>'view','id'=>$val['memberId']));?>" class="text-link">查看</a>
                </td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
      </div>