<!-- 操作提示消息框 -->
<?php $this->beginContent('//layouts/_error2');$this->endContent();?>
<?php $this->beginContent('//layouts/_success');$this->endContent();?>
<!-- 操作提示消息框 -->

<!-- 条件过虑档位开始 -->
<div class="panel panel-default search-panel">
  <div class="panel-body">
    <form role="search" class="pull-left form-inline" action="<?php echo $this->createUrl('productdetail');?>">
      <div class="form-group">
        成交时间:
        <input type="text" name="start" value="<?php echo Yii::app()->request->getQuery('start');?>" class="form-control input-sm input-date" readonly id="starttime" onclick="WdatePicker({dateFmt:'yyyy-MM'})" />
        到
        <input type="text" name="end" value="<?php echo Yii::app()->request->getQuery('end');?>" class="form-control input-sm input-date" readonly id="endtime"  onclick="WdatePicker({dateFmt:'yyyy-MM'})"/>
        <input type="text" name="product" value="<?php echo Yii::app()->request->getQuery('product');?>" placeholder="产品编码" class="form-control input-sm" data-suggestion="searchproduct" data-search="q=%s" data-api="/statistic/ajax/product/"  autocomplete="off" />
        <input type="text" name="memberName" value="<?php echo Yii::app()->request->getQuery('memberName');?>" placeholder="客户名称" class="form-control input-sm" data-suggestion="searchmember" data-search="q=%s&amp;t=2" data-api="/statistic/ajax/member/" autocomplete="off" />
        <input type="hidden" name="member" value="<?php echo Yii::app()->request->getQuery('member');?>">        
      </div>
      <button class="btn btn-sm btn-default">查找</button>
      <?php
        if($_GET && $data_list && $this->checkAccess('/statistic/excel/productdetail')) {
          echo CHtml::link('导出数据', $this->createUrl('/statistic/excel/productdetail', $_GET), ['class'=>"btn btn-primary",'role'=>"button"]);
        }
         ?>
    </form>
  </div>
</div>
<!-- 条件过虑档位结束 -->

<!--报表台头开始 -->
<?php if($data_list):?>
<div class="container-fluid">
  <div class="col-md-6">产品：<?php echo $account;?></div>
  <div class="col-md-6 text-right">报表区间：<?php echo $start;?> - <?php echo $end;?></div>
</div>
<?php endif;?>
<!--报表台头结束 -->

<!-- 数据显示区开始 -->
<div id="tableHead">
<table class="table table-condensed table-bordered">
	<colgroup>
    <col width="20%">
    <col width="10%">
    <col width="10%">
    <col width="20%">
    <col width="20%">
    <col width="20%">
  </colgroup>
	 <thead>
    <tr class="text-center">
      <td class="a">订单号</td>
      <td class="b">产品编号</td>
      <td class="b">颜色</td>
      <td class="a">单价</td>
      <td class="a">销售量</td>
      <td class="a">销售金额</td>
    </tr>
  </thead>
</table>
</div>
<br>
<table class="table table-condensed table-bordered">
  <colgroup>
    <col width="20%">
    <col width="10%">
    <col width="10%">
    <col width="20%">
    <col width="20%">
    <col width="20%">
  </colgroup>
  <tbody>
    <!-- 统计列表数据 -->
    <?php if(!$data_list):?>
    <tr>
      <td colspan="6" class="text-center">无数据显示</td>
    </tr>
    <?php
    else:
      foreach($data_list as $record){
    ?>
    <tr class="text-center">
      <td><?php echo $record['orderId'];?></td>
      <td><?php echo $record['serialNumber'];?></td>
      <td><?php echo $record['singleNumber'];?></td>
      <td><?php echo $record['price'];?></td>
      <td><?php echo $record['quantity'];?></td>
      <td><?php echo $record['total'];?></td>
    </tr>
    <?php }?>
    <?php endif; ?>
  </tbody>
</table>
<!-- 合计数据 -->

<br>
<?php if($data_list):?>
<div class="navbar-fixed-bottom" style="width:100%;padding:0 15px">
<table class="table table-condensed table-bordered ">
  <colgroup>
    <col width="20%">
    <col width="10%">
    <col width="10%">
    <col width="20%">
    <col width="20%">
    <col width="20%">
  </colgroup>
   <thead>
    <tr class="text-center">
      <td>合计</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td><?php echo $quantitys;?></td>
      <td><?php echo $prices;?></td>
    </tr>
  </thead>
</table>
</div>
<?php endif; ?>
<div class="navbar-fixed-top headerss hide" style="width:100%;padding:0 15px"></div>
    
<!-- 数据显示区结束 -->
<script>
  seajs.use('statics/app/statistic/js/list.js');
</script>