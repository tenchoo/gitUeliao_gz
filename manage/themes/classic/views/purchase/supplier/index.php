<div class="panel panel-default search-panel">
  <div class="panel-body">
  <form action="<?php echo $this->createUrl('index');?>" class="pull-left form-inline">
	<input type="text" value="<?php echo $factoryNumber;?>" name="factoryNumber" class="form-control input-sm" placeholder="工厂编号"/>
	<input type="text" value="<?php echo $keyword;?>" name="keyword" class="form-control input-sm" placeholder="工厂名称"/>
	<button class="btn btn-sm btn-default">查找</button>
	</form>
  <div class="pull-right">
    <a class="btn btn-sm btn-default" href="<?php echo $this->createUrl('add');?>">添加厂商</a>
  </div>
  </div>
</div>
<div class="clearfix well well-sm list-well">
  <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
</div>
<table class="table table-condensed table-bordered">
<colgroup><col width="10%"><col><col width="20%"><col width="15%"><col width="15%"></colgroup>
  <thead>
    <tr>
      <td>工厂编号</td>
	  <td>工厂名称</td>
	  <td>联系人</td>
	  <td>联系电话</td>
      <td>管理</td>
    </tr>
  </thead>
</table>
<br>
<table class="table table-condensed table-bordered table-hover">
<colgroup><col width="10%"><col><col width="20%"><col width="15%"><col width="15%"></colgroup>
  <tbody>
    <?php foreach( $list as $item ): ?>
    <tr>
	  <td><?php echo $item->factoryNumber;?></td>
      <td><?php echo $item->shortname;?></td>
	  <td><?php echo $item->contact;?></td>
	  <td><?php echo $item->phone;?></td>
      <td>
        <a href="<?php echo $this->createUrl('account',array('id'=>$item->supplierId));?>">账号管理</a>
        <a href="<?php echo $this->createUrl('edit',array('id'=>$item->supplierId));?>">编辑</a>
		<a href="#" class="del" data-toggle="modal" data-target=".del-confirm" data-id="<?php echo $item->supplierId ?>" data-rel="<?php echo $this->createUrl('del');?>">删除</a>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<div class="clearfix well well-sm list-well">
  <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
</div>
<?php $this->beginContent('//layouts/_del');$this->endContent();?>