<link rel="stylesheet" href="/themes/classic/statics/app/member/css/style.css" />
  <div class="panel panel-default search-panel">
   <div class="panel-body">
    <form role="search" class="pull-left form-inline" action="<?php echo $this->createUrl( 'index' );?>">
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
    <!-- <button class="btn btn-sm btn-default">批量导出</button> -->
   </div>
   <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>
  <table class="table table-condensed table-bordered">
   <colgroup><col width="24"><col width="100"><col><col width="10%"><col width="10%"><col width="10%"><col width="10%"><col width="10%"><col width="10%"><col width="10%"></colgroup>
   <thead>
    <tr>
     <td></td>
     <td>手机号</td>
     <td>客户名称</td>
     <td>信用额度(元)</td>
	 <td>已用额度(元)</td>
	 <td>可用额度(元)</td>
     <td>还款周期(个月)</td>
     <td>业务员</td>
     <td>账户状态</td>
     <td>操作</td>
    </tr>
   </thead>
   </table>
   <br>
   <table class="table table-condensed table-bordered table-hover">
    <colgroup><col width="24"><col width="100"><col><col width="10%"><col width="10%"><col width="10%"><col width="10%"><col width="10%"><col width="10%"><col width="10%"></colgroup>
   <tbody>
   <?php foreach( $list as $_list  ):?>
    <tr>
     <td><input type="checkbox" value="<?php echo $_list['memberId'];?>" name="memberId[]"/></td>
     <td><?php echo $_list['phone'];?></td>
     <td><?php echo $_list['companyname']?></td>
     <td><?php echo number_format( $_list['credit'],2 );?> </td>
	 <td><?php echo number_format( $_list['usedCredit'],2 );?> </td>
	 <td><?php echo number_format( $_list['validCredit'],2 );?> </td>
	 <td><?php echo $_list['billingCycle'];?></td>
     <td><?php echo $_list['saleman']?></td>
     <td><?php if( $_list['memberState'] == 'Normal' ){ ?>正常 <?php } else { ?>冻结<?php }?></td>
     <td>
		<a href="<?php echo $this->createUrl('bill',array('memberId' => $_list['memberId']));?>">账单</a>
		<a href="<?php echo $this->createUrl('repayment',array('memberId' => $_list['memberId']));?>">还款</a>
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
<?php $this->beginContent('//layouts/_del');$this->endContent();?>