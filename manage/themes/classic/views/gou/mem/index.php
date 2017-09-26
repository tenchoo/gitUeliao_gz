    <div class="panel panel-default search-panel">
   <div class="panel-body">
    <form role="search" class="pull-left form-inline" action="<?php echo $this->createUrl(Yii::app()->getController()->getAction()->id);?>">
     <div class="form-group">
      <div class="inline-block pull-left">
		<input type="text" name="title" value="<?php echo $title;?>" placeholder="请输入送货员名称" class="form-control input-sm" />
	 </div>
     </div>
     <button class="btn btn-sm btn-default">查找</button>
    </form>
	<div class="pull-right">
      <a class="btn btn-default" href="<?php echo $this->createUrl( 'add' );?>">新增</a>
    </div>
   </div>
  </div>

  <div class="clearfix well well-sm list-well">
    <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>
  <table class="table table-striped table-condensed table-hover table-bordered">
   <colgroup><col width="8%"><col width="8%"><col><col width="10%"></colgroup>
   <thead>
    <tr>
	 <td>id</td>
     <td>姓名</td>
	 <td>前端访问地址</td>
     <td>操作</td>
    </tr>
   </thead>
   </table>
   <br>
   <table class="table table-condensed table-bordered table-hover">
  <colgroup><col width="8%"><col width="8%"><col><col width="10%"></colgroup>
  <tbody>
   <?php foreach(  $list as $val  ):?>
    <tr>
     <td><?php echo $val['deliverymanId'];?></td>
	  <td><?php echo $val['title'];?></td>
	 <td><a href="<?php echo $val['url'];?>" target="_blank"><?php echo $val['url'];?></a></td>
     <td>
		<a href="<?php echo $this->createUrl('edit',array('id' =>$val['deliverymanId']));?>">编辑</a>
		<a href="#" class="del" data-toggle="modal" data-target=".del-confirm" data-id="<?php echo $val['deliverymanId'] ?>" data-rel="<?php echo $this->createUrl('del');?>">删除</a>
	</td>
    </tr>
   <?php endforeach;?>
   </tbody>
  </table>
  <div class="clearfix well well-sm list-well">
   <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>
 <?php $this->beginContent('//layouts/_del');$this->endContent();?>