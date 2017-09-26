  <div class="panel panel-default search-panel">
   <div class="panel-body">
    <form role="search" method="post" class="pull-left form-inline">
     <div class="form-group">
      <input type="text" name="phoneEmail" value="<?php if (!empty( $param )) { echo $param['phoneEmail']; }?>" placeholder="请输入员工姓名" class="form-control input-sm" />
     </div>
     <button class="btn btn-sm btn-default">查找</button>
    </form>
    <a class="btn btn-sm btn-default pull-right" href="<?php echo $this->createUrl('addedit')?>">新增</a>
   </div>
  </div>
  <div class="clearfix well well-sm list-well">
   <div class="pull-left form-inline">
    <label class="checkbox-inline"> <input type="checkbox" class="checkedall"/> 全选 </label>
    <!-- <button class="btn btn-sm btn-default btn-export">批量导出</button> -->
   </div>
 <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>
  <table class="table table-condensed table-bordered">
   <colgroup><col width="24"><col><col width="25%"><col width="20%"><col width="15%"><col width="15%"></colgroup>
   <thead>
    <tr>
     <td></td>
     <td>姓名</td>
     <td>手机号</td>
	   <td>呢称</td>
     <td>添加时间</td>
     <td>操作</td>
    </tr>
   </thead>
   </table>
   <br>
   <table class="table table-condensed table-bordered table-hover">
   <colgroup><col width="20"><col><col width="25%"><col width="20%"><col width="15%"><col width="15%"></colgroup>
   <tbody>
   <?php foreach(  $list as $user  ):?>
    <tr>
     <td><input type="checkbox" value="<?php echo $user['memberId'];?>"/></td>
     <td><?php echo $user['username'];?></td>
	   <td><?php echo $user['phone'];?></td>
     <td><?php echo $user['nickName'];?></td>
	   <td><?php echo $user['register'];?></td>
     <td>
		<a href="<?php echo $this->createUrl('addedit',array('memberId' =>$user['memberId'],'from'=>urlencode( Yii::app()->request->url)));?>">编辑</a>
		<?php if($user['memberId']!= tbConfig::model()->get( 'default_saleman_id' )){ ?>
			<?php if( $user['state'] == 'Normal' ) { ?>
		<a href="#" class="del" data-id="<?php echo $user['memberId'] ?>" data-rel="<?php echo $this->createUrl('remove');?>">冻结</a>
		<?php }else{ ?>
		<a href="#" class="undel" data-id="<?php echo $user['memberId'] ?>" data-rel="<?php echo $this->createUrl('thaw');?>">解冻</a>
		<?php } ?>
		<?php }?>
		</td>
    </tr>
   <?php endforeach;?>
   </tbody>
  </table>
  <div class="clearfix well well-sm list-well">
   <div class="pull-left form-inline">
    <label class="checkbox-inline"> <input type="checkbox" class="checkedall"/> 全选 </label>
    <!-- <button class="btn btn-sm btn-default btn-export">批量导出</button> -->
   </div>
  <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>


<script>seajs.use('statics/app/member/js/salesmanlist.js');</script>
