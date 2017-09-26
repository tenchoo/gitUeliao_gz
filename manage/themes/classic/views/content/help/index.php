    <div class="panel panel-default search-panel">
   <div class="panel-body">
    <form role="search" class="pull-left form-inline" action="<?php echo $this->createUrl('index');?>">
	<div class="form-group">
		<select name="categoryId" class="form-control input-sm">
		<option value="">请选择类目</option>
		<?php foreach ( $category as $c ){ if( $c['type']=='1' || $c['parentId']>0 ) continue; ?>
		<option value="<?php echo $c['categoryId'];?>" <?php if($c['categoryId']==$categoryId){echo 'selected';}?>><?php echo $c['title'];?></option>
		<?php if(isset($c['childs'])){
			foreach ( $c['childs'] as $cval ){ if( $c['type']=='1' ) continue; ?>
			<option value="<?php echo $cval['categoryId'];?>" <?php if($cval['categoryId']==$categoryId){echo 'selected';}?>>---<?php echo $cval['title'];?></option>
		<?php }}}?>
		</select>
	 </div>
     <div class="form-group">
      <input type="text" name="title" value="<?php echo $title;?>" placeholder="请输入信息标题" class="form-control input-sm" />
     </div>
     <button class="btn btn-sm btn-default">查找</button>
    </form>
   </div>
  </div>
  <div class="clearfix well well-sm list-well">
   <div class="pull-left form-inline">
    <label class="checkbox-inline"> <input type="checkbox" class="checkedall"/> 全选 </label>
    <button class="btn btn-sm btn-default batch-del">批量删除</button>
   </div>
    <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>
  <table class="table table-condensed table-hover table-bordered">
   <thead>
    <tr>
    <td width="25px"></td>
     <td>标题</td>
     <td width="15%">所属分类</td>
	 <td width="15%">更新时间</td>
     <td width="15%">操作</td>
    </tr>
   </thead>
   <tbody>
   <?php foreach( $list as $val ):?>
    <tr>
     <td><input type="checkbox" value="<?php echo $val['helpId'];?>"/></td>
     <td><?php echo $val['title'];?></td>
     <td><?php echo $val['category'];?></td>
     <td><?php echo $val['updateTime'];?></td>
     <td>
		<a href="<?php echo $this->createUrl('edit',array('id' => $val['helpId']));?>">编辑</a>
		<a href="#" class="del" data-toggle="modal" data-target=".del-confirm" data-id="<?php echo $val['helpId'] ?>" data-rel="<?php echo $this->createUrl('del');?>">删除</a>
	</td>
    </tr>
   <?php endforeach;?>
   </tbody>
  </table>
  <div class="clearfix well well-sm list-well">
   <div class="pull-left form-inline">
    <label class="checkbox-inline"> <input type="checkbox" class="checkedall"/> 全选 </label>
    <button class="btn btn-sm btn-default batch-del">批量删除</button>
   </div>
   <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>
 <?php $this->beginContent('//layouts/_del');$this->endContent();?>
 <script>
  seajs.use('statics/app/help/js/list.js');
</script>