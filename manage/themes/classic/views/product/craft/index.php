    <div class="panel panel-default search-panel">
   <div class="panel-body">
    <form role="search" class="pull-left form-inline" >
     <div class="form-group">
      <div class="inline-block pull-left"><input type="text" name="craftCode" value="<?php echo $craftCode;?>" placeholder="请输入工艺编号" class="form-control input-sm" /></div>
     </div>
     <button class="btn btn-sm btn-default">查找</button>
    </form>

	<div class="pull-right">
      <a class="btn btn-default" href="<?php echo $this->createUrl( 'add' );?>">新增</a>
    </div>
   </div>
  </div>

  <div class="clearfix well well-sm list-well">
   <div class="pull-left form-inline">
    <label class="checkbox-inline"> <input type="checkbox" class="checkedall"/> 全选 </label>
    <!-- <button class="btn btn-sm btn-default btn-export">批量导出</button> -->
   </div>
  </div>
  <table class="table table-striped table-condensed table-hover table-bordered">
  <colgroup><col width="50"><col width="8%"><col width="8%"><col><col width="10%"><col width="10%"></colgroup>
   <thead>
    <tr>
    <td></td>
	 <td colspan="2">工艺编号</td>
     <td>工艺名称</td>
     <td>是否有子分类</td>
     <td>操作</td>
    </tr>
   </thead>
   </table>
   <br>
   <table class="table table-condensed table-bordered table-hover">
	<colgroup><col width="50"><col width="8%"><col width="8%"><col><col width="10%"><col width="10%"></colgroup>
   <tbody>
   <?php foreach(  $list as $val  ):?>
    <tr>
     <td><input type="checkbox" value="<?php echo $val['craftId'];?>"/></td>
     <td><?php echo $val['craftCode'];?></td>
	 <td></td>
	 <td><?php echo $val['title'];?>
		<?php if( !empty($val['icon']) ):?>
			 <img src="<?php echo $this->img().$val['icon'];?>" alt="" width="20" height="20">
		<?php endif;?>
	 </td>
     <td><?php if($val['hasLevel'] ){ ?><span class="text-danger">是</span><?php }else{ ?>否<?php }?></td>
     <td>
		<a href="<?php echo $this->createUrl('edit',array('id' => $val['craftId']));?>">编辑</a>
	<?php if( !isset( $val['childs']) ){ ?>
		<a href="#" class="del" data-toggle="modal" data-target=".del-confirm" data-id="<?php echo $val['craftId'] ?>" data-rel="<?php echo $this->createUrl('del');?>">删除</a>
	<?php }?>
	<?php if($val['hasLevel'] ){ ?>
		<br><a href="<?php echo $this->createUrl('add',array('pid' => $val['craftId']));?>">新增子分类</a>
	<?php }?>
	</td>
    </tr>
	<?php if( isset( $val['childs']) ){
			 foreach( $val['childs'] as $cval  ):
	?>
		<tr>
		 <td><input type="checkbox" value="<?php echo $val['craftId'];?>"/></td>
		 <td></td>
		 <td><?php echo $cval['craftCode'];?></td>
		 <td><?php echo $cval['title'];?> </td>
		 <td>否</td>
		 <td>
			<a href="<?php echo $this->createUrl('edit',array('id' => $cval['craftId']));?>">编辑</a>
			<a href="#" class="del" data-toggle="modal" data-target=".del-confirm" data-id="<?php echo $cval['craftId'] ?>" data-rel="<?php echo $this->createUrl('del');?>">删除</a>
		</td>
		</tr>
	 <?php endforeach;?>
	<?php }?>
   <?php endforeach;?>
   </tbody>
  </table>
  <div class="clearfix well well-sm list-well">
   <div class="pull-left form-inline">
    <label class="checkbox-inline"> <input type="checkbox" class="checkedall"/> 全选 </label>
   <!--  <button class="btn btn-sm btn-default btn-export">批量导出</button> -->
   </div>
  </div>
 <?php  $this->beginContent('//layouts/_del');$this->endContent();?>
 <script>
seajs.use('statics/app/product/default/js/index.js');
</script>