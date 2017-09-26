   <div class="panel panel-default search-panel">
        <div class="panel-body">
			<div class="pull-right">
			<a class="btn btn-sm btn-default" href="<?php echo $this->createUrl('position',array('warehouseId' =>$warehouseId));?>">返回分区列表</a>
            </div>
        </div>
    </div>

<table class="table table-condensed table-bordered table-bordered table-hover import">
  <thead>
    <tr>
	<td width="60">负责人</td>
    <td width="20%">员工姓名</td>
	<td ></td>
    </tr>
  </thead>
  <tbody>
    <?php foreach( $list as $item ): ?>
   <tr id="w_<?php echo $item['userId']?>">
	  <td>
	  <input class="manage" type="radio" name = "isManager" value="<?php echo $item['userId'] ?>"
			<?php if( $item['isManage'] =='1' ){ echo 'checked';}?> >
	</td>
      <td><?php echo $item['user'];?></td>
      <td><a href="#" class="del" data-toggle="modal" data-target=".del-confirm" data-id="<?php echo $item['userId'] ?>" data-rel="<?php echo $this->createUrl('userlist',array('positionId'=>$positionId,'op'=>'del'));?>" title="删除">删除</a></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
<tfoot>
			<tr>
				<td colspan="3">
				<div class="col-md-12">
				<form action="" class="form-horizontal form-inline" method="post" >
					<input type="text" class="form-control input-sm" name="username"  data-suggestion="username" data-search="username=%s" data-api="/api/search_username" autocomplete="off" placeholder="请输入员工姓名" />
					<input type="hidden" id="userId" name="userId">
				   <button class="btn btn-sm btn-default"  id="btn-add" type="submit" disabled ><span class="glyphicon glyphicon-plus"></span>添加</button>
				</form>
				  </div>
				</td>
			</tr>
		</tfoot>
	</table>
 <?php $this->beginContent('//layouts/_del');$this->endContent();?>
<script>seajs.use('statics/app/warehouse/js/packUser.js')</script>
<script type="text/html" id="choose-list">
	<tr id="w_{{id}}">
	 <td>
	  <input class="manage" type="radio" name = "isManager" value="{{id}}" >
	</td>
		<td>{{username}}</td>
		<td><a href="#" class="del" data-toggle="modal" data-target=".del-confirm" data-id="{{id}}" data-rel="<?php echo $this->createUrl('userlist',array('positionId'=>$positionId,'op'=>'del'));?>" title="删除">删除</a></td>
	</tr>
</script>