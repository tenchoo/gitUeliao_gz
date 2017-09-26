 <?php $this->beginContent('//layouts/_error2');$this->endContent();?>
 <br>

	<table class="table table-condensed table-bordered table-bordered table-hover import">
	 <colgroup><col width="20%"><col></colgroup>
    <thead>
    <tr>
     <td>仓库归单员</td>
     <td>操作</td>
    </tr>
	<tbody>
	<?php foreach ( $list as $val ):?>
		<tr id="w_<?php echo $val['userId']?>">
		<td><?php echo $val['username']?></td>
		<td><a href="#" class="del" data-toggle="modal" data-target=".del-confirm" data-id="<?php echo $val['userId'] ?>" data-rel="<?php echo $this->createUrl('users',array('op'=>'del'));?>" title="删除">删除</a></td>
	</tr>
	<?php endforeach;?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="2">
				<div class="col-md-5">
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
<script>seajs.use('statics/app/warehouse/js/mergeuser.js')</script>
<script type="text/html" id="choose-list">
	<tr id="w_{{id}}">
		<td>{{username}}</td>
		<td><a href="#" class="del" data-toggle="modal" data-target=".del-confirm" data-id="{{id}}" data-rel="<?php echo $this->createUrl('users',array('op'=>'del'));?>" title="删除">删除</a></td>
	</tr>
</script>