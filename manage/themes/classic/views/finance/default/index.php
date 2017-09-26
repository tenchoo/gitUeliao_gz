<nav class="navbar navbar-default">
      <form class="navbar-form navbar-left" action="<?php echo $this->createUrl('index');?>">
        <div class="form-group">
		 <input type="text" name="month" value="<?php echo  $month;?>" class="form-control input-sm input-date" readonly id="month"  size="8" onFocus="WdatePicker({dateFmt:'yyyy-MM',onpicked:cFunc})"/>
		 <input type="hidden" name="d" value="<?php echo  $d;?>" />
        </div>
      </form>

	   <ul class="nav navbar-nav">
		<?php for( $i= 1;$i<=$days;$i++ ){ ?>
		<li <?php if( $i == $d ){ ?>class="active"<?php }?> rel="<?php echo $i;?>"> <a href="javascript:;" style="padding-left: 5px;padding-right: 6px"> <?php echo $i;?></a></li>
		<?php }?>
		<li <?php if( $d == 'monthPay' ){ ?>class="active"<?php }?>  rel="monthPay"> <a href="javascript:;" style="padding-left: 5px;padding-right: 6px" > 月结账单 </a></li>
      </ul>
</nav>

<div class="clearfix well well-sm list-well">
  <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
</div>

<?php if(  $d == 'monthPay' ){ ?>
<table class="table table-condensed table-bordered  table-hover">
<colgroup><col><col width="15%"><col width="15%"><col width="15%"><col width="15%"><col width="10%"></colgroup>
  <thead>
    <tr>
	  <td>客户</td>
      <td>结算月份</td>
	  <td>总应收金额</td>
	  <td>已收款</td>
	  <td>未收款</td>
	  <td>操作</td>
    </tr>
  </thead>
  <tbody>
    <?php foreach( $list as $item ):?>
   <tr>
	  <td><?php echo $item['member'];?></td>
      <td><?php echo $item['month'];?></td>
	  <td><?php echo $item['payments'];?></td>
	  <td><?php echo $item['receipt'];?></td>
	  <td><?php echo $item['notReceive'];?></td>
	  <td><a href="<?php echo $this->createUrl('add',array('month'=>$item['month'],'memberId'=>$item['memberId']));?>">收款</a></td>
    </tr>
   <?php endforeach; ?>
  </tbody>
</table>
<?php } else { ?>
<table class="table table-condensed table-bordered  table-hover">
<colgroup><col><col width="10%"><col width="10%"><col width="12%"><col width="12%"><col width="12%"><col width="12%"><col width="10%"></colgroup>
  <thead>
    <tr>
	  <td>客户</td>
      <td>结算单号</td>
	  <td>订单编号</td>
	  <td>总应收金额</td>
	  <td>已收款</td>
	  <td>未收款</td>
	  <td>结算时间</td>
	  <td>操作</td>
    </tr>
  </thead>
  <tbody>
   <?php foreach( $list as $item ):?>
   <tr>
	  <td><?php echo $item['member'];?></td>
      <td><?php echo $item['settlementId'];?></td>
	  <td><?php echo $item['orderId'];?></td>
	  <td><?php echo $item['realPayment'];?></td>
	  <td><?php echo $item['receipt'];?></td>
	  <td><?php echo $item['notReceive'];?></td>
	  <td><?php echo $item['createTime'];?></td>
	  <td><a href="<?php echo $this->createUrl('add',array('settlementId'=>$item['settlementId']));?>">收款</a></td>
    </tr>
   <?php endforeach; ?>
  </tbody>
</table>
<?php } ?>
<div class="clearfix well well-sm list-well">
  <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
</div>
<script>
	$from = $('.navbar-form');
	var $d =  $('input[name="d"]');
    function cFunc(who){
		$d.val( 'all' );
		$from.submit();   
    }
</script>
<script>seajs.use('statics/app/finance/js/index.js');</script>