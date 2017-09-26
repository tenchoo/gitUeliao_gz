<link rel="stylesheet" href="/themes/classic/statics/app/member/css/style.css" />
<div class="panel panel-default search-panel">
  <div class="panel-body">
  <form action="<?php echo $this->createUrl('index');?>" class="pull-left form-inline">
	<?php echo CHtml::dropDownList('departmentId',$departmentId,$departments,array('class'=>'form-control input-sm','empty'=>'所有部门'))?>

	<input type="text" value="<?php echo $username;?>" name="username" class="form-control input-sm" placeholder="输入员工姓名"/>
	<button class="btn btn-sm btn-default">查找</button>
	</form>
   <div class="pull-right">
    <a class="btn btn-sm btn-default" href="<?php echo $this->createUrl('add');?>">添加新员工</a>
	</div>
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
   <colgroup><col width="20%"><col width="20%"><col width="15%"><col width="15%"><col width="15%"><col width="15%"></colgroup>
   <thead>
    <tr>
	 <td>姓名</td>
     <td>手机号</td>
     <td>所属部门</td>
	  <td>职位</td>
     <td>所属角色</td>
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
    <td><!-- <input type="checkbox" value="<?php echo $val['userId'];?>"/> -->
	<?php echo $val['username'];?></td>
	<td><?php echo $val['account'];?></td>
     <td><?php echo (isset($departments[$val['departmentId']]))?$departments[$val['departmentId']]:'';?></td>
     <td><?php echo $val['positionName'];?></td>
     <td><?php echo $val['roles'];?></td>
     <td>
		<a href="<?php echo $this->createUrl('view',array('id' => $val['userId']));?>">查看</a>
		<a href="<?php echo $this->createUrl('edit',array('id' => $val['userId']));?>">编辑</a>
		<?php if($val['userId']>1){
			if( $val['state'] == '0' ) { ?>
			<a href="#" class="del" data-id="<?php echo $val['userId'] ?>" data-rel="<?php echo $this->createUrl('del');?>">冻结</a>
		<?php }else{ ?>
			<a href="#" class="undel" data-id="<?php echo $val['userId'] ?>" data-rel="<?php echo $this->createUrl('thaw');?>">解冻</a>
		<?php }  } ?>
	</td>
    </tr>
   <?php endforeach;?>
   </tbody>
  </table>
  <div class="clearfix well well-sm list-well">
   <div class="pull-left form-inline">
    <!-- <label class="checkbox-inline"> <input type="checkbox" class="checkedall"/> 全选 </label>
    <button class="btn btn-sm btn-default btn-export">批量导出</button> -->
   </div>
   <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>
 <script>
seajs.use('statics/app/role/js/user.js');
 </script>