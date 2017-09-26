<?php $this->beginContent('_tab',array('active'=>'voices','productId'=>$productId));$this->endContent();?>
<div class="clearfix alert alert-warning">
  <a href="<?php echo $this->createUrl('index',array('step'=>'addvoice','productId'=>$productId));?>" class="pull-right"><span class="glyphicon glyphicon-plus"></span>增加语音介绍</a>
  产品编号：<strong class="text-warning"><?php echo $serialNumber;?></strong>
</div>
<br>
<table class="table table-striped table-condensed table-hover table-bordered">
   <colgroup><col width="12%"><col><col width="12%"><col width="15%"><col width="15%"><col width="12%"></colgroup>
   <thead>
    <tr>
     <td>文件标题</td>
     <td>音频</td>
	  <td>排序</td>
     <td>添加时间</td>
     <td>更新时间</td>
     <td>操作</td>
    </tr>
   </thead>
   </table>
   <br>
   <table class="table table-condensed table-bordered">
  <colgroup><col width="12%"><col><col width="12%"><col width="15%"><col width="15%"><col width="12%"></colgroup>
   <tbody>
   <?php foreach(  $list as $val  ):?>
    <tr>
	 <td><?php echo $val['title'];?></td>
     <td><?php echo $val['sound'];?>
	 <?php if( $val['isMain'] == '1') { ?>
		<span class="text-danger">(主音频)</span>
		<?php }?></td>
	 <td><?php echo $val['sort'];?></td>
	 <td><?php echo $val['createTime'];?></td>
	 <td><?php echo $val['updateTime'];?></td>
     <td>
		<a href="<?php echo $this->createUrl('index',array('step'=>'editvoice','id' =>$val['id']));?>">编辑</a>
		<a href="#" class="del" data-toggle="modal" data-target=".del-confirm" data-id="<?php echo $val['id'] ?>" data-rel="<?php echo $this->createUrl('index',array('step'=>'delvoice'));?>">删除</a>
	</td>
    </tr>
   <?php endforeach;?>
   </tbody>
  </table>
  <br>

   <?php $this->beginContent('//layouts/_del');$this->endContent();?>