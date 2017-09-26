<div class="panel panel-default search-panel">
  <div class="panel-body">
  <form action="<?php echo $this->createUrl('index');?>" class="pull-left form-inline">
	<?php echo CHtml::dropDownList('warehouseId',$warehouseId,$warehouse,array('class'=>'form-control input-sm','empty'=>'所有仓库'))?>

	<input type="text" value="<?php echo $username;?>" name="username" class="form-control input-sm" placeholder="输入员工姓名"/>
	<button class="btn btn-sm btn-default">查找</button>
	</form>
	<div class="pull-right">
	当前所属角色组：<?php echo $roleName;?>(roleId:<?php echo $roleId;?>)
    <a class="btn btn-sm btn-default" href="<?php echo $this->createUrl('setrole');?>">设置所属角色组</a>
	</div>
  </div>
</div>
  <div class="clearfix well well-sm list-well">
   <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>
<table class="table table-condensed table-bordered">
   <colgroup><col width="20%"><col width="20%"><col width="15%"><col width="15%"><col width="15%"><col width="15%"></colgroup>
   <thead>
    <tr>
	 <td>姓名</td>
     <td>手机号</td>
     <td>所属部门</td>
	  <td>职位</td>
     <td>所属仓库</td>
     <td>操作</td>
    </tr>
   </thead>
   </table>
   <br>
  <table class="table table-condensed table-bordered table-hover">
  <colgroup><col width="20%"><col width="20%"><col width="15%"><col width="15%"><col width="15%"><col width="15%"></colgroup>
   <tbody>
   <?php foreach(  $list as $val  ):?>
    <tr>
    <td><?php echo $val['username'];?></td>
	<td><?php echo $val['account'];?></td>
     <td><?php echo (isset($departments[$val['departmentId']]))?$departments[$val['departmentId']]:'';?></td>
     <td><?php echo $val['positionName'];?></td>
     <td><?php echo array_key_exists( $val['warehouseId'],$warehouse )?$warehouse[$val['warehouseId']]:'';?></td>
     <td>
		<a href="<?php echo $this->createUrl('chooseware',array('id' => $val['userId']));?>">编辑所属仓库</a>
	</td>
    </tr>
    </tr>
   <?php endforeach;?>
   </tbody>
  </table>
  <div class="clearfix well well-sm list-well">
   <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>