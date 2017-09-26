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
	  <td><a href="<?php echo $this->createUrl('check',array('id'=>$item['id']));?>">审核</a></td>
    </tr>
   <?php endforeach; ?>
  </tbody>
</table>
<br/>
<div class="clearfix well well-sm list-well">
  <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
</div>
<script>seajs.use('statics/app/finance/js/index.js');</script>