    <div class="panel panel-default search-panel">
   <div class="panel-body">
	<div class="pull-right">
      <a class="btn btn-primary" href="<?php echo $this->createUrl( 'add' );?>">新增</a>
    </div>
   </div>
  </div>

  <table class="table table-striped table-condensed table-bordered">
   <colgroup><col width="20"><col width="20%"><col><col width="10%"></colgroup>
   <thead>
    <tr>
	  <td>ID</td>
	 <td>呆滞级别</td>
     <td>默认时长</td>
     <td>操作</td>
    </tr>
   </thead>
   </table>
   <br>
   <table class="table table-condensed table-bordered table-hover">
   <colgroup><col width="20"><col width="20%"><col><col width="10%"></colgroup>
  <tbody>
   <?php foreach(  $list as $val  ):?>
    <tr>
	  <td><?php echo $val['id'];?></td>
     <td><?php echo $val['title'];?>
		<?php if ( !empty ( $val['logo'] ) ){?>
		<img src="<?php echo $this->img().$val['logo'];?>" alt="" height="20">
		<?php }?>
	 </td>
	 <td><?php echo $val['conditions'];?>小时</td>
     <td>
		<a href="<?php echo $this->createUrl('edit',array('id' =>$val['id']));?>">编辑</a>
		<a href="#" class="del" data-toggle="modal" data-target=".del-confirm" data-id="<?php echo $val['id'] ?>" data-rel="<?php echo $this->createUrl('del');?>">删除</a>
	</td>
    </tr>
   <?php endforeach;?>
   </tbody>
  </table>
 <?php $this->beginContent('//layouts/_del');$this->endContent();?>