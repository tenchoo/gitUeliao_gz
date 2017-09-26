<link rel="stylesheet" href="/themes/classic/statics/app/member/css/style.css" />
  <div class="panel panel-default search-panel">
   <div class="panel-body">
    <form role="search" class="pull-left form-inline" action="<?php echo $this->createUrl( Yii::app()->getController()->getAction()->id);?>">
     <!--select class="form-control input-sm">
		<option>客户等级</option> <option>2</option> <option>3</option> <option>4</option> <option>5</option>
	 </select-->
     <div class="form-group">
     <input type="text" name="keyword" value="<?php echo $keyword;?>" placeholder="请输入手机号" class="form-control input-sm" />
	 <input type="text" name="companyname" value="<?php echo $companyname;?>" placeholder="请输入客户名称" class="form-control input-sm" />
     </div>
     <button class="btn btn-sm btn-default">查找</button>
    </form>
   </div>
  </div>
  <div class="clearfix well well-sm list-well">
   <div class="pull-left form-inline">
    <label class="checkbox-inline"> <input type="checkbox" class="checkedall"/> 全选 </label>
    <!-- <button class="btn btn-sm btn-default">批量导出</button> -->
	<?php if( isset($action)&& $action =='distribution' ){ ?>
		<button class="btn btn-sm btn-default distribution" data-toggle="modal" data-target=".distribution-confirm">分配业务员</button>
		 <?php $this->beginContent('_saleman',array( 'saleList' => $saleList));$this->endContent();?>
	<?php } ?>
   </div>
   <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>
  <table class="table table-condensed table-bordered">
   <colgroup><col width="24"><col><col width="15%"><col width="15%"><col width="10%"><col width="15%"><col width="15%"><col width="15%"></colgroup>
   <thead>
    <tr>
     <td></td>
     <td>手机号</td>
     <td>客户名称</td>
     <td>所属客户组</td>
     <td>客户等级</td>
     <td>业务员</td>
     <td>注册时间</td>
     <td>操作</td>
    </tr>
   </thead>
   </table>
   <br>
   <table class="table table-condensed table-bordered table-hover">
   <colgroup><col width="20"><col><col width="15%"><col width="15%"><col width="10%"><col width="15%"><col width="15%"><col width="15%"></colgroup>
   <tbody>
   <?php foreach( $users as $user  ):?>
    <tr>
     <td><input type="checkbox" value="<?php echo $user['memberId'];?>" name="memberId[]"/></td>
     <td><?php echo $user['phone'];?></td>
     <td><?php echo $user['companyname']?></td>
     <td><?php echo $user['group'];?></td>
     <td>
	 <?php if(isset($levelList[$user['level']])){ ?>

	<?php echo $levelList[$user['level']]['title']?>
	  <?php } ?>
	 </td>
     <td><?php echo $user['saleman']?></td>
     <td><?php echo $user['register'];?></td>
     <td>
		<a href="<?php echo $this->createUrl('default/view',array('memberId' => $user['memberId']));?>">查看</a>
		<?php if( !isset($action) || $action =='distribution' ){ ?>
		<a href="<?php echo $this->createUrl('default/modify',array('memberId' => $user['memberId']));?>">编辑</a>
		<?php } ?>
		<?php if( $user['state'] == 'Normal' ) { ?>
		<a href="#" class="del" data-id="<?php echo $user['memberId'] ?>" data-rel="<?php echo $this->createUrl('remove');?>">冻结</a>
		<?php }else{ ?>
		<a href="#" class="undel" data-id="<?php echo $user['memberId'] ?>" data-rel="<?php echo $this->createUrl('thaw');?>">解冻</a>
		<?php } ?>
		</td>
    </tr>
   <?php endforeach;?>
   </tbody>
  </table>
  <div class="clearfix well well-sm list-well">
   <div class="pull-left form-inline">
     <label class="checkbox-inline"> <input type="checkbox" class="checkedall"/> 全选 </label>
    <!--<button class="btn btn-sm btn-default">批量导出</button>-->
   </div>
   <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>


  <script>seajs.use('statics/app/member/js/distribution.js');</script>