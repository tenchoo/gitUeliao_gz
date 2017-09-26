<div class="panel panel-default search-panel">
  <div class="panel-body">
  <form action="<?php echo $this->createUrl('index2');?>" class="pull-left form-inline">
	<input type="text" autocomplete="off" data-api="/statistic/ajax/member/" data-search="q=%s&amp;t=2" data-suggestion="searchmember" class="form-control input-sm" placeholder="客户名称" value="" name="memberName">
	<input type="hidden" value="" name="memberId">
	<button class="btn btn-sm btn-default"  id="btn-add" disabled  >查找</button>
	</form>
  </div>
</div>
<?php if( !empty( $memberName ) ) { ?>
<div class="clearfix well well-sm list-well">
<span >客户：<?php echo $memberName;?> </span>
<span style="padding-left:20px;">当前结算方式：<?php echo $isCredit?'':'非';?>月结 </span>
<?php if( !empty( $list ) ){ ?>
  <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
<?php }?>
</div>
<br>

<?php if( !empty( $mlist ) ){ ?>
<table class="table table-condensed table-bordered  table-hover">
<colgroup><col><col width="18%"><col width="18%"><col width="18%"><col width="10%"></colgroup>
  <thead>
  <tr class="">
    <td colspan="5" class=" alert-info">
   <span class="first">月结账单</span></td>
  </tr>
    <tr>
      <td>结算月份</td>
	  <td>总应收金额</td>
	  <td>已收款</td>
	  <td>未收款</td>
	  <td>操作</td>
    </tr>
  </thead>
  <tbody>
    <?php foreach( $mlist as $item ):?>
   <tr>
      <td><?php echo $item['month'];?></td>
	  <td><?php echo $item['payments'];?></td>
	  <td><?php echo $item['receipt'];?></td>
	  <td><?php echo $item['notReceive'];?></td>
	  <td><a href="<?php echo $this->createUrl('add',array('month'=>$item['month'],'memberId'=>$item['memberId']));?>">收款</a></td>
    </tr>
   <?php endforeach; ?>
  </tbody>
</table>
<br/>
<?php }?>

<?php if( !empty( $list ) ){ ?>
<table class="table table-condensed table-bordered  table-hover">
<colgroup><col><col width="15%"><col width="18%"><col width="18%"><col width="18%"><col width="10%"></colgroup>
  <thead>
   <tr class="">
    <td colspan="6" class="alert-warning">
   <span class="first">非月结账单</span></td>
  </tr>
    <tr>
      <td>结算单号</td>
	  <td>订单编号</td>
	  <td>总应收金额</td>
	  <td>已收款</td>
	  <td>未收款</td>
	  <td>操作</td>
    </tr>
  </thead>
  <tbody>
   <?php foreach( $list as $item ):?>
   <tr>
      <td><?php echo $item['settlementId'];?></td>
	  <td><?php echo $item['orderId'];?></td>
	  <td><?php echo $item['realPayment'];?></td>
	  <td><?php echo $item['receipt'];?></td>
	  <td><?php echo $item['notReceive'];?></td>
	  <td><a href="<?php echo $this->createUrl('add',array('settlementId'=>$item['settlementId']));?>">收款</a></td>
    </tr>
   <?php endforeach; ?>
  </tbody>
</table>
<br/>
<div class="clearfix well well-sm list-well">
  <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
</div>
<?php }}?>

<script>seajs.use('statics/app/finance/js/index.js');</script>