<div class="panel panel-default search-panel">
  <div class="panel-body">
  <form action="<?php echo $this->createUrl('index');?>" class="pull-left form-inline">
	<input type="text" value="<?php echo $driverName;?>" name="driverName" class="form-control input-sm" placeholder="输入部门名称"/>
	<button class="btn btn-sm btn-default">查找</button>
	</form>
   <div class="pull-right">
    <a class="btn btn-sm btn-default" href="<?php echo $this->createUrl('add');?>">添加驾驶员</a>
	</div>
  </div>
</div>
  <div class="clearfix well well-sm list-well">
   <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>
  <table class="table table-condensed table-bordered">
   <colgroup><col width="20%"><col width="20%"><col width="20%"><col><col width="10%"></colgroup>
   <thead>
    <tr>
	 <td>姓名</td>
	 <td>手机</td>
	 <td>性别</td>
	 <td>身份证号</td>
     <td>操作</td>
    </tr>
   </thead>
   </table>
   <br>
  <table class="table table-condensed table-bordered table-hover">
   <colgroup><col width="20%"><col width="20%"><col width="20%"><col><col width="10%"></colgroup>
   <tbody>
   <?php foreach(  $list as $val  ):?>
    <tr>
    <td><?php echo $val['driverName'];?></td>
	<td><?php echo $val['phone'];?></td>
	<td><?php echo $val['gender'];?></td>
	<td><?php echo $val['idcard'];?></td>
     <td>
		<a href="<?php echo $this->createUrl('edit',array('id' => $val['driverId']));?>">编辑</a>
		<a href="#" class="del" data-toggle="modal" data-target=".del-confirm" data-id="<?php echo $val['driverId'] ?>" data-rel="<?php echo $this->createUrl('del');?>">删除</a>
	</td>
    </tr>
   <?php endforeach;?>
   </tbody>
  </table>
  <div class="clearfix well well-sm list-well">
   <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>
 <?php $this->beginContent('//layouts/_del');$this->endContent();?>