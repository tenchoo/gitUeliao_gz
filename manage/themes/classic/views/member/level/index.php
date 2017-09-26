  <div class="panel panel-default search-panel">
   <div class="panel-body">
    <form role="search" class="pull-left form-inline" <?php echo $this->createUrl('index');?>>
     <div class="form-group">
      <input type="text" name="keyword" value="<?php echo $keyword;?>" placeholder="请输入等级名称" class="form-control input-sm" />
     </div>
     <button class="btn btn-sm btn-default">查找</button>
    </form>
	 <a class="btn btn-sm btn-default pull-right" href="<?php echo $this->createUrl('add')?>">添加客户等级</a>
   </div>
  </div>
  <br>
  <table class="table table-condensed table-bordered">
   <colgroup><col><col width="20%"><col width="20%"></colgroup>
   <thead>
    <tr>
     <td >等级名称</td>
	 <td >会员数</td>
     <td width="120px">操作</td>
    </tr>
   </thead>
   </table>
   <br>
   <table class="table table-condensed table-bordered table-hover">
   <colgroup><col><col width="20%"><col width="20%"></colgroup>
   <tbody>
   <?php foreach(  $data as $val  ):?>
    <tr>
     <td><img src="<?php echo $this->img(false).$val['logo'];?>" width="30"/><?php echo $val['title'];?></td>
	 <td ></td>
     <td>
		<a href="<?php echo $this->createUrl('edit',array('id' => $val['levelId']));?>">编辑</a>
		<a href="#" class="del" data-toggle="modal" data-target=".del-confirm" data-id="<?php echo $val['levelId'] ?>" data-rel="<?php echo $this->createUrl('del');?>">删除</a>
	</td>
    </tr>
   <?php endforeach;?>
   </tbody>
  </table>
<?php $this->beginContent('//layouts/_del');$this->endContent();?>