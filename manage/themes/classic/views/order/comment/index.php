  <div class="panel panel-default search-panel">
   <div class="panel-body">
    <form role="search" class="pull-left form-inline" action="<?php echo $this->createUrl('index');?>">
	反馈时间:  <input type="text" name="createTime1" value="<?php echo $condition['createTime1']; ?>" class="form-control input-sm input-date" id="starttime" readonly/>
		到 <input type="text" name="createTime2" value="<?php echo $condition['createTime2']; ?>" class="form-control input-sm input-date" id="endtime" readonly/>
     <div class="form-group">
      <input type="text" name="orderId" value="<?php echo $condition['orderId'];?>" placeholder="请输入订单编号" class="form-control input-sm" />
     </div>
     <button class="btn btn-sm btn-default">查找</button>
    </form>
   </div>
  </div>
  <div class="clearfix well well-sm list-well">
   <div class="pull-left form-inline">
    <!-- <label class="checkbox-inline"> <input type="checkbox" class="checkedall"/> 全选 </label>
    <button class="btn btn-sm btn-default btn-export">批量导出</button> -->
   </div>
  <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>
  <table class="table table-condensed table-bordered">
   <colgroup><col width="40%" /><col width="40%" /><col width="20%" /></colgroup>
   <thead>
    <tr>
     <td>产品信息</td>
     <td>反馈内容</td>
     <td>操作</td>
    </tr>
   </thead>
   </table>
   <br>
   <?php foreach(  $list as $val  ){?>
   <table class="table table-condensed table-bordered">
   <colgroup><col width="40%" /><col width="40%" /><col width="20%" /></colgroup>
   <tbody>

	<tr class="list-hd">
    <td colspan="7">
		<!-- <input type="checkbox" name="orderId[]" value="<?php echo $val['orderId'];?>"/> -->
		<span class="first"><?php echo $val['createTime'];?></span>
		<span>订单编号：<?php echo $val['orderId'];?></span>
		<span>客户：<?php echo $val['nickName'];?></span>
	  </td>
  </tr>
	<?php $count = count($val['products']);foreach( $val['products'] as $key=>$pval  ){ ?>
	 <tr class="list-bd">
   <td>
   <div class="c-img pull-left">
     <a href="javascript:"><img src="<?php echo $this->img(false).$pval['mainPic'];?>_200" alt="" width="50" height="50"/></a>
   </div>
	 <div class="product-title"><a href="javascript:"><?php echo $pval['title'];?></a></div>
	 <p>产品编号：<?php echo $pval['serialNumber'];?></p>
	 </td>
     <td class="comment"> <?php echo $pval['content'];?>
    <div>
	 <?php if(!empty($pval['reply'])) { ?>
	 <span class="text-success">解释：</span><?php echo $pval['reply'];?>
	  <?php } ?>
    </div>
	  <form action="<?php echo $this->createUrl('edit',array('commentId'=>$pval['commentId']))?>" method="post" class="hide">
	  <textarea name="reply" placeholder="解释内容"/><?php echo $pval['reply'];?></textarea>
    <a href="javascript:" class="save-comment">保存</a>
    <a href="javascript:" class="cancel-comment">取消</a>
	  </form>
	 </td>
     <td>
		<?php if(!empty($pval['reply'])) { ?>
		<a href="javascript:" class="edit-comment">编辑</a>
		<?php }else{ ?>
		<a href="javascript:" class="add-comment">解释</a>
		<?php } ?>
		<a href="<?php echo $this->createUrl('del',array('commentId'=>$pval['commentId']))?>">删除</a>
	 </td>
    </tr>
	  <?php } ?>
	  </tbody>
  </table>
  <br>
   <?php }?>

  <div class="clearfix well well-sm list-well">
   <div class="pull-left form-inline">
    <!-- <label class="checkbox-inline"> <input type="checkbox" class="checkedall"/> 全选 </label>
    <button class="btn btn-sm btn-default btn-export">批量导出</button> -->
   </div>
    <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>
<script>seajs.use('statics/app/order/js/ordercomment.js');</script>