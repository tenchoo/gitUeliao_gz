  <div class="panel panel-default search-panel">
   <div class="panel-body">
    <form role="search" class="pull-left form-inline" action="<?php echo $this->createUrl('checklist');?>">
     <select class="form-control input-sm">
		<option>按手机号</option></select>
     <div class="form-group">
      <input type="text" name="keyword" value="<?php echo $keyword;?>" placeholder="请输入手机号" class="form-control input-sm" />
     </div>
     <button class="btn btn-sm btn-default">查找</button>
    </form>
   </div>
  </div>
  <div class="clearfix well well-sm list-well">
   <div class="pull-left form-inline">
    <label class="checkbox-inline"> <input type="checkbox" class="checkedall"/> 全选 </label>
    <!-- <button class="btn btn-sm btn-default btn-export">批量导出</button>  -->
   </div>
   <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>
  <table class="table table-condensed table-bordered">
   <colgroup><col width="24"><col><col width="25%"><col width="20%"><col width="15%"><col width="15%"></colgroup>
   <thead>
    <tr>
     <td></td>
     <td>手机号</td>
     <td>客户名称</td>
     <td>业务员</td>
     <td>状态</td>
     <td>操作</td>
    </tr>
   </thead>
  </table>
  <br>
  <table class="table table-condensed table-bordered table-hover">
  <colgroup><col width="20"><col><col width="25%"><col width="20%"><col width="15%"><col width="15%"></colgroup>
   <tbody>
   <?php foreach(  $users as $user  ):?>
    <tr>
     <td><input type="checkbox" value="<?php echo $user['memberId'];?>"/></td>
     <td><?php echo $user['phone'];?></td>
     <td><?php echo $user['companyname']?></td>
	  <td><?php echo $user['saleman']?></td>
     <td><?php echo $user['isCheck']?'审核不通过':'待审核';?></td>
     <td>
		<a href="<?php echo $this->createUrl('default/check',array('memberId' => $user['memberId'],'type'=>'do'));?>">审核</a>
		<a href="#" class="del" data-toggle="modal" data-target=".del-confirm" data-id="<?php echo $user['memberId'] ?>" data-rel="<?php echo $this->createUrl('remove');?>">删除</a>
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
<?php $this->beginContent('//layouts/_del');$this->endContent();?>
<script>seajs.use('statics/app/member/js/checklist.js');</script>