  <div class="clearfix well well-sm list-well">
   <div class="pull-left form-inline">
    <label class="checkbox-inline"> <input type="checkbox" class="checkedall"/> 全选 </label>
    <button class="btn btn-sm btn-default">批量删除</button>
	<button class="btn btn-sm btn-default">下架</button>
	<a  class="btn btn-sm btn-default" href="<?php echo $this->createUrl('addad',array('adPositionId'=>$adPositionId));?>">上传广告</a>
   </div>
     <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>
  <table class="table table-condensed table-hover table-bordered">
   <thead>
    <tr>
    <td width="25px"></td>
     <td>广告标题</td>
     <td width="25%">有效时间</td>
	 <td width="15%">状态</td>
     <td width="15%">操作</td>
    </tr>
   </thead>
   <tbody>
   <?php foreach( $list as $val ):?>
    <tr>
     <td><input type="checkbox" value="<?php echo $val['adId'];?>"/></td>
     <td><?php if($val['image']){?>
		<img src="<?php echo $this->img(),$val['image'];?>" width="125px"/>
		<?php }?>
		<?php echo $val['title'];?></td>
     <td><?php echo $val['activeTime'];?></td>
     <td><span class="<?php if($val['active']){echo 'text-danger';}?>"><?php echo $val['stateTitle'];?></span></td>
     <td>
		<a href="<?php echo $this->createUrl('editad',array('id' => $val['adId']));?>">编辑</a>
		<a href="#" class="del" data-toggle="modal" data-target=".del-confirm" data-id="<?php echo $val['adId'] ?>" data-rel="<?php echo $this->createUrl('delad');?>">删除</a>
	</td>
    </tr>
   <?php endforeach;?>
   </tbody>
  </table>
  <div class="clearfix well well-sm list-well">
   <div class="pull-left form-inline">
    <label class="checkbox-inline"> <input type="checkbox" class="checkedall"/> 全选 </label>
    <button class="btn btn-sm btn-default">批量删除</button>
	<button class="btn btn-sm btn-default">下架</button>
   </div>
     <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>
 <?php $this->beginContent('//layouts/_del');$this->endContent();?>