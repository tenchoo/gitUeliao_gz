<!-- 操作提示消息框 -->
<?php $this->beginContent('//layouts/_error2');$this->endContent();?>
<?php $this->beginContent('//layouts/_success');$this->endContent();?>
<!-- 操作提示消息框 -->

<!-- 条件过虑档位开始 -->
<div class="panel panel-default search-panel">
  <div class="panel-body">
    <form role="search" class="pull-left form-inline" action="<?php echo $this->createUrl('salemandays');?>">
      <div class="form-group">
      成交时间:
      <input type="text" name="start" value="<?php echo Yii::app()->request->getQuery('start');?>" class="form-control input-sm input-date" readonly id="starttime" onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})" />
      到
      <input type="text" name="end" value="<?php echo Yii::app()->request->getQuery('end');?>" class="form-control input-sm input-date" readonly id="endtime"  onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>
        <input type="text" name="salemanName" value="<?php echo Yii::app()->request->getQuery('salemanName');?>" placeholder="业务员名称" class="form-control input-sm" data-suggestion="searchsaleman" data-search="q=%s&amp;t=1" data-api="/statistic/ajax/member/" autocomplete="off" />
        <input type="hidden" name="saleman" value="<?php echo Yii::app()->request->getQuery('saleman');?>">
        <input type="text" name="memberName" value="<?php echo Yii::app()->request->getQuery('memberName');?>" placeholder="客户名称" class="form-control input-sm" data-suggestion="searchmember" data-search="q=%s&amp;t=2" data-api="/statistic/ajax/member/" autocomplete="off" />
        <input type="hidden" name="member" value="<?php echo Yii::app()->request->getQuery('member');?>">
        <input type="text" name="product" value="<?php echo Yii::app()->request->getQuery('product');?>" placeholder="产品编码" class="form-control input-sm" data-suggestion="searchproduct" data-search="q=%s" data-api="/statistic/ajax/product/"  autocomplete="off" />
      </div>      
      <button class="btn btn-sm btn-default">查找</button>      
        <?php
        if($_GET && $data_list && $this->checkAccess('/statistic/excel/salemandays')) {
          echo CHtml::link('导出数据', $this->createUrl('/statistic/excel/salemandays', $_GET), ['class'=>"btn btn-primary",'role'=>"button"]);
        }
         ?>
    </form>
  </div>
</div>
<!-- 条件过虑档位结束 -->

<!--报表台头开始 -->
<?php if($data_list):?>
<div class="container-fluid">
  <div class="col-md-6">业务员：<?php echo $account;?></div>
  <div class="col-md-6 text-right">报表区间：<?php echo $start;?> - <?php echo $end;?></div>
</div>
<?php endif;?>
<!--报表台头结束 -->

<!-- 数据显示区开始 -->
<table class="table table-condensed table-bordered">
  <colgroup>
    <col width="25%">
    <col width="36%">
    <col width="39%">
  </colgroup>
  <thead>
    <tr class="text-center">
      <td>月份</td>
      <td>销售量</td>
      <td>销售金额</td>
    </tr>
  </thead>

  <tbody>
    <!-- 统计列表数据 -->
    <?php if(!$data_list):?>
    <tr>
      <td colspan="3" class="text-center">无数据显示</td>
    </tr>
    <?php
    else:
      foreach($data_list as $record){
    ?>
    <tr class="text-center">
      <td><?php echo $record['dealTime'];?></td>
      <td class="text-right"><?php echo $record['quantity'];?></td>
      <td class="text-right"><?php echo $record['price'];?></td>
    </tr>
    <?php }?>
    <!-- 合计数据 -->
    <tr class="text-center" style="background:#f5f5f5">
      <td>合计</td>
      <td class="text-right"><?php echo $quantitys;?></td>
      <td class="text-right"><?php echo $prices;?></td>
    </tr>
    <?php endif; ?>
  </tbody>
</table>
<?php if($data_list):?>
  <div class="text-right"><?php echo CHtml::link('明细报表', $this->createUrl('salemandetaildays', $_GET));?></div>
<?php endif; ?>
<!-- 数据显示区结束 -->
<script>
  seajs.use('statics/app/statistic/js/list.js');
</script>
