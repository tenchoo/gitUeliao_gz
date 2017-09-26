  <div class="panel panel-default search-panel">
   <div class="panel-body">
    <form role="search" class="pull-left form-inline" <?php echo $this->createUrl('index');?>>
     <div class="form-group">
      <input type="text" name="keyword" value="<?php echo $keyword;?>" placeholder="请输入组名称" class="form-control input-sm" />
     </div>
     <button class="btn btn-sm btn-default">查找</button>
    </form>
	 <a class="btn btn-sm btn-default pull-right" href="<?php echo $this->createUrl('add')?>">新增</a>
   </div>
  </div>
  <br>
  <table class="table table-condensed table-bordered">
   <colgroup><col><col width="20%"></colgroup>
   <thead>
    <tr>
     <td >客户组名称</td>
     <td>操作</td>
    </tr>
   </thead>
  </table>
  <br>
  <table class="table table-condensed table-bordered  table-hover">
   <colgroup><col><col width="20%"></colgroup>
   <tbody>
   <?php foreach(  $data as $val  ):?>
    <tr>
     <td><?php echo $val['title'];?></td>
     <td>
		<?php if( $val['groupId']>1 ){ ?>
		<a href="<?php echo $this->createUrl('edit',array('groupId' => $val['groupId']));?>">编辑</a>
		<?php } ?>
		<?php if( $val['groupId']>2 ){ ?>
		<a href="#" class="del" data-toggle="modal" data-target=".del-confirm" data-id="<?php echo $val['groupId'] ?>" data-rel="<?php echo $this->createUrl('del');?>">删除</a>
		<?php } ?>
	</td>
    </tr>
   <?php endforeach;?>
   </tbody>
  </table>

<?php $this->beginContent('//layouts/_del');$this->endContent();?>