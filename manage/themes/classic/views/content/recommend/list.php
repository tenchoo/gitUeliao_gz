
  <a class="pull-right btn btn-sm btn-primary" href="<?php echo $this->createUrl('productlist',array('recommendId'=>$recommendId));?>"><span class="glyphicon glyphicon-plus"></span> 添加推荐产品</a>
  <br>
  <br>
  <br>
  <table class="table table-condensed table-hover table-bordered">
   <thead>
    <tr>
    <!-- <td width="25px"></td> -->
     <td>产品标题</td>
     <td width="25%">推荐时间</td>
     <td width="15%">操作</td>
    </tr>
   </thead>
   <tbody>
   <?php foreach( $list as $val ):?>
    <tr>
     <!-- <td><input type="checkbox" value="<?php echo $val['id'];?>"/></td> -->
     <td><img src="<?php echo $this->img().$val['mainPic'];?>_50" width="50" height="50"/>
		<?php echo $val['serialNumber'];?>
		<?php echo $val['title'];?></td>
     <td><?php echo $val['recommendTime'];?></td>
     <td>
		<a href="#" class="del" data-toggle="modal" data-target=".del-confirm" data-id="<?php echo $val['id'] ?>" data-rel="<?php echo $this->createUrl('delrecommend');?>">删除推荐</a>
	</td>
    </tr>
   <?php endforeach;?>
   </tbody>
  </table>
 <?php $this->beginContent('//layouts/_del');$this->endContent();?>