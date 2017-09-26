<link rel="stylesheet" href="/themes/classic/statics/app/role/css/style.css">
<div class="panel panel-default search-panel">
  <div class="panel-body">
  <form action="<?php echo $this->createUrl('index');?>" class="pull-left form-inline">
	<select name="state" class="form-control input-sm cate1">
        <option value="">正常</option>
		<option value="1" <?php if( $state == '1'){ echo 'selected';}?>>已关闭</option>
    </select>

	<input type="text" value="<?php echo $identity;?>" name="identity" class="form-control input-sm" placeholder="输入标识"/>
	<button class="btn btn-sm btn-default">查找</button>
	</form>
   <div class="pull-right">
    <a class="btn btn-sm btn-default" href="<?php echo $this->createUrl('add');?>">添加推荐位</a>
	</div>
  </div>
</div>
<table class="table table-condensed table-hover table-bordered">
   <thead>
    <tr>
    <td width="18%">推荐位标识</td>
    <td width="18%">推荐位名称</td>
    <td width="18%">已推荐数/总数</td>
    <td width="18%">生成时间</td>
    <td width="18%">最后更新</td>
    <td width="10%">操作</td>
    </tr>
   </thead>
  <tbody>
    <?php foreach( $list as $item ): ?>
    <tr>
	  <td><?php echo $item['identity'];?></td>
	  <td><span title="<?php echo $item['remark'];?>"><?php echo $item['title'];?></span></td>
      <td><a href="<?php echo $this->createUrl('list',array('recommendId'=>$item['recommendId']) );?>" >已推荐(<?php  echo $item['num'];?>/<?php echo $item['maxNum'];?>)</a></td>
	  <td><?php echo $item['createTime'];?></td>
	  <td><?php echo $item['updateTime'];?></td>
      <td>
	  <?php if($item['state'] == '1') { ?>
	  <a href="#" class="del" data-toggle="modal" data-target=".del-confirm" data-id="<?php echo $item['recommendId'];?>" data-rel="<?php echo $this->createUrl('active');?>">激活</a>
	  <?php }else{ ?>
	   <a href="<?php echo $this->createUrl('edit',array('recommendId'=>$item['recommendId']) );?>">编辑</a>
	   <a href="#" class="del" data-toggle="modal" data-target=".del-confirm" data-id="<?php echo $item['recommendId'];?>" data-rel="<?php echo $this->createUrl('close');?>">关闭</a>
	  <?php } ?>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php $this->beginContent('//layouts/_del');$this->endContent();?>