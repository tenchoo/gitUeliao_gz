<link rel="stylesheet" href="/app/member/trade/css/style.css"/>
<div class="pull-right frame-content">
      <div class="frame-list">
        <div class="frame-tab">
          <ul class="clearfix list-unstyled frame-tab-hd">
            <li class="active">
              <a href="javascript:">客户反馈</a>
            </li>
          </ul>
        </div>
        <div class="frame-list-bd comment">
          <table>
            <thead>
              <tr><th class="title" width="300">产品信息</th><th >反馈内容</th><th width="100">操作</th></tr>
              <tr class="list-page-head"><td colspan="3">
                <?php $this->widget('widgets.ZPagerNavigate', array('pages' => $pages,'type' => "mini",));?>
              </td></tr>
            </thead>
            <tfoot class="list-page-foot">
              <tr class="spacing"><td colspan="3"></td></tr>
              <tr><td colspan="3">
                <?php $this->widget('widgets.ZPagerNavigate', array('pages' => $pages, 'maxLinkCount' => 10));?>
              </td></tr>
            </tfoot>
            <tbody class="list-page-body">
              <?php foreach ($list as $val){ ?>
              <tr>
                <td class="title">
                  <div class="c-img pull-left"><img src="<?php $this->imageUrl($val['mainPic'],50)?>" width="50" height="50"/></div>
                  <div class="product-title"><?php echo $val['serialNumber']?> <?php echo $val['title']?></div>
                  <p class="text-minor"><?php echo $val['specifiaction']?></p>
                </td>
                <td><?php echo $val['content']?><br/>
					         <p class="text-minor"><?php echo $val['createTime']?></p>
				        </td>
                <td>
                <a href="<?php echo $this->createUrl('edit',array('id'=>$val['commentId']));?>" class="text-link">修改</a>
                </td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
      </div>