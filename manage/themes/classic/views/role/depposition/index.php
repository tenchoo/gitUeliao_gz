<div class="panel panel-default search-panel">
  <div class="panel-body">
  <form action="<?php echo $this->createUrl('index');?>" class="pull-left form-inline">
	<input type="text" value="<?php echo $positionName;?>" name="positionName" class="form-control input-sm" placeholder="输入职位名称"/>
	<button class="btn btn-sm btn-default">查找</button>
	</form>
   <div class="pull-right">
    <a class="btn btn-sm btn-default" href="<?php echo $this->createUrl('add',['dep'=>Yii::app()->request->getQuery('id')]);?>">添加新职位</a>
	</div>
  </div>
</div>
  <div class="clearfix well well-sm list-well">
   <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>
  <table class="table table-condensed table-bordered">
   <colgroup><col><col width="30%"><col width="30%"></colgroup>
   <thead>
    <tr>
	   <td>职位名称</td>
	   <td>所属部门</td>
     <td width="20%">操作</td>
    </tr>
   </thead>
  </table>
  <br>
  <table class="table table-condensed table-bordered">
   <colgroup><col><col width="30%"><col width="30%"></colgroup>
   <tbody>
   <?php foreach(  $dataList as $val  ):?>
    <tr>
    <td><?php echo $val->positionName;?></td>
	<td><?php echo $val->department->departmentName;?></td>
     <td>
		<a href="<?php echo $this->createUrl('edit',array('id' => $val->depPositionId));?>">编辑</a>
		<a href="javascript:void(0);" class="del" data-toggle="modal" data-target=".del-confirm" data-id="<?php echo $val['depPositionId'] ?>" data-rel="<?php echo $this->createUrl('del');?>">删除</a>
	</td>
    </tr>
   <?php endforeach;?>
   </tbody>
  </table>
  <div class="clearfix well well-sm list-well">
   <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>
 <?php $this->beginContent('//layouts/_del');$this->endContent();?>