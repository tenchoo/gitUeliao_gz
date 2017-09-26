 <?php $this->beginContent('//layouts/_error2');$this->endContent();?>
 <br>
<form action="" class="form-horizontal form-inline" method="post" >
	<table class="table table-condensed table-bordered import">
	 <colgroup><col width="20%"><col></colgroup>
    <thead>
    <tr>
     <td>仓库管理员（<?php echo $title;?>）</td>
     <td>操作</td>
    </tr>
	<tbody>
	<?php foreach ( $list as $val ):?>
		<tr id="w_<?php echo $val['userId']?>">
		<td>
		<input type="hidden" name="user[]" value="<?php echo $val['userId']?>"/><?php echo $val['username']?>
		</td>
		<td><a href="javascript:;" class="del" title="删除">删除</a></td>
	</tr>
	<?php endforeach;?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="2">
				<div class="col-md-5">
					<input type="text" class="form-control input-sm" name="username"  data-suggestion="username" data-search="username=%s" data-api="/api/search_username" autocomplete="off" placeholder="请输入员工姓名" />
					<input type="hidden" id="userId" >
				   <button class="btn btn-sm btn-default"  id="btn-add" type="button" disabled ><span class="glyphicon glyphicon-plus"></span>添加</button>
				  </div>
				</td>
			</tr>
		</tfoot>
	</table>
<br>
<div align="center">
<input class="btn btn-success addlm" type="submit" value="提交保存" />
</div>
</form>
<script>seajs.use('statics/app/warehouse/js/manager.js')</script>
<script type="text/html" id="choose-list">
	<tr id="w_{{id}}">
		<td>
		<input type="hidden" name="user[]" value="{{id}}"/>{{username}}
		</td>
		<td><a href="javascript:;" class="del" title="删除">删除</a></td>
	</tr>
</script>