<link rel="stylesheet" href="/app/member/information/css/style.css"/>
<div class="pull-right frame-content">
  <div class="frame-tab">
    <ul class="clearfix list-unstyled frame-tab-hd">
      <li class="active">
        <a href="javascript:">系统消息</a>
      </li>
    </ul>
  </div>
      <div class="frame-list information">
        <div class="frame-list-bd">
          <table>
            <thead>
              <tr><th colspan="2" class="title"></th><th width="300">内容</th><th width="100">操作</th></tr>
              <tr class="list-page-head"><td colspan="4">
                <div class="pull-left">
                  <label class="checkbox-inline"><input class="checkedall" type="checkbox">全选</label>
				  <button type="button" class="btn btn-cancel btn-xs bdel">批量删除</button>
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
                  <label class="checkbox-inline"><input class="checkedall" type="checkbox">全选</label>
				  <button type="button" class="btn btn-cancel btn-xs bdel">批量删除</button>
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
                <td class="check"><input type="checkbox" name="ids[]" value="<?php echo $val->messageId?>"></td>
                <td>
                  <div class="c-img pull-left"></div>
                  <div class="product-title">
				  <?php //if($val->state=='0'){echo '(new)';}?>
				  <?php //echo $val->title?>
				  <?php echo $val->createTime;?>
				  </div>
                </td>
                <td><?php echo $val->content;?></td>
                <td>
                <?php echo CHtml::link('删除',array('delete','ids'=>$val->messageId),array('class'=>'text-link'))?>
                </td>
              </tr>
              <?php }?>
              <?php }else{?>
              	<tr><td class="text-center" colspan="4">暂无消息</td></tr>
              <?php }?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
<script>seajs.use('app/member/trade/js/message.js');</script>