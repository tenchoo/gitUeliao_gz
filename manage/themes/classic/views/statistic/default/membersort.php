<!-- 操作提示消息框 -->
<?php $this->beginContent('//layouts/_error2');$this->endContent();?>
<?php $this->beginContent('//layouts/_success');$this->endContent();?>
<!-- 操作提示消息框 -->

<!-- 条件过虑档位开始 -->
<div class="panel panel-default search-panel">
  <div class="panel-body">
    <form role="search" class="pull-left form-inline" action="<?php echo $this->createUrl('membersort');?>">
      <div class="form-group">
      成交时间:
      <input type="text" name="start" value="<?php echo Yii::app()->request->getQuery('start');?>" class="form-control input-sm input-date" readonly id="starttime" onclick="WdatePicker({dateFmt:'yyyy-MM'})" />
      到
      <input type="text" name="end" value="<?php echo Yii::app()->request->getQuery('end');?>" class="form-control input-sm input-date" readonly id="endtime"  onclick="WdatePicker({dateFmt:'yyyy-MM'})"/>
        <input type="text" name="product" value="<?php echo Yii::app()->request->getQuery('product');?>" placeholder="产品编码" class="form-control input-sm" data-suggestion="searchproduct" data-search="q=%s" data-api="/statistic/ajax/product/"  autocomplete="off" />
      </div>
      <button class="btn btn-sm btn-default">查找</button>
      <?php
        if($_GET && $data_list && $this->checkAccess('/statistic/excel/membersort')) {
          echo CHtml::link('导出数据', $this->createUrl('/statistic/excel/membersort', $_GET), ['class'=>"btn btn-primary",'role'=>"button"]);
        }
         ?>
    </form>
  </div>
</div>
<!-- 条件过虑档位结束 -->

<!--报表台头开始 -->
<?php if($data_list):?>
<br>
<div class="row">
  <div class="col-md-6">&nbsp;</div>
  <div class="col-md-6 text-right">报表区间：<?php echo $start;?> - <?php echo $end;?></div>
</div>
<?php endif;?>
<!--报表台头结束 -->

<!-- 数据显示区开始 -->
<div id="tableHead">
<table class="table table-condensed table-bordered">
	<colgroup>
    <col width="10%">
    <col width="30%">
    <col width="30%">
    <col width="30%">
  </colgroup>
	 <thead>
    <tr class="text-center">
      <td>名次</td>
      <td>客户名称</td>
      <td>成交量</td>
      <td>交易金额</td>
    </tr>
  </thead>
</table>
</div>
<br>
<table class="table table-condensed table-bordered">
  <colgroup>
    <col width="10%">
    <col width="30%">
    <col width="30%">
    <col width="30%">
  </colgroup>

  <tbody>
    <!-- 统计列表数据 -->
    <?php if(!$data_list):?>
    <tr>
      <td colspan="4" class="text-center">无数据显示</td>
    </tr>
    <?php
    else:
      foreach($data_list as $record){
    ?>
    <tr class="text-center">
      <td><?php echo $record['id'];?></td>
      <td class="text-left"><?php echo $record['memberName'];?></td>
      <td class="text-right"><?php echo $record['quantity'];?></td>
      <td class="text-right"><?php echo $record['price'];?></td>
    </tr>
    <?php }?>
    <!-- 合计数据 -->
    <?php endif; ?>
  </tbody>
</table>
<!-- 数据显示区结束 -->
<br>
<?php if($data_list):?>
<div class="navbar-fixed-bottom" style="width:100%;padding:0 15px">
<table class="table table-condensed table-bordered ">
  <colgroup>
    <col width="10%">
    <col width="30%">
    <col width="30%">
    <col width="30%">
  </colgroup>
   <thead>
    <tr class="text-center">
      <td>合计</td>
      <td>&nbsp;</td>
      <td><?php echo $quantitys;?></td>
      <td><?php echo $prices;?></td>
    </tr>
  </thead>
</table>
</div>
<?php endif; ?>
<div class="navbar-fixed-top headerss hide" style="width:100%;padding:0 15px"></div>
<script>
  seajs.use('statics/app/statistic/js/list.js');
</script>