<div class="panel panel-default search-panel">
  <div class="panel-body">
  <form action="<?php echo $this->createUrl('list');?>" class="pull-left form-inline">
	<input type="text" autocomplete="off" data-api="/statistic/ajax/member/" data-search="q=%s&amp;t=2" data-suggestion="searchmember" class="form-control input-sm" placeholder="客户名称" value="<?php echo $memberName; ?>" name="memberName">
	&nbsp;&nbsp;申请时间:  <input type="text" name="t1" value="<?php echo $t1; ?>" class="form-control input-sm input-date" id="starttime" readonly/>
		到 <input type="text" name="t2" value="<?php echo $t2; ?>" class="form-control input-sm input-date" id="endtime" readonly/>
	<input type="hidden" value="<?php echo $memberId; ?>" name="memberId" >
	<button class="btn btn-sm btn-default" >查找</button>
	</form>
  </div>
</div>
<div class="clearfix well well-sm list-well">
  <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
</div>
<table class="table table-condensed table-bordered table-hover">
<colgroup><col><col width="15%"><col width="15%"><col width="15%"><col width="15%"><col width="10%"></colgroup>
  <thead>
    <tr>
	  <td>客户</td>
      <td>结算单号/结算月份</td>
	  <td>金额</td>
	  <td>申请人</td>
	  <td>申请时间</td>
	  <td>操作</td>
    </tr>
  </thead>
  <tbody>
   <?php foreach( $list as $item ):?>
   <tr>
      <td><?php echo $item['companyname'];?></td>
      <td><?php echo $item['settlementId'];?></td>
	  <td><?php echo $item['amount'];?></td>
	  <td><?php echo $item['username'];?></td>
	  <td><?php echo $item['createTime'];?></td>
	  <td><?php echo $item['stateTitle'];?><br>
		<a href="<?php echo $this->createUrl('view',array('id'=>$item['id']));?>">查看</a>
	</td>
    </tr>
   <?php endforeach; ?>
  </tbody>
</table>
<br/>
<div class="clearfix well well-sm list-well">
  <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
</div>
<script>seajs.use('statics/app/finance/js/index.js');</script>