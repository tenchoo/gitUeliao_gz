<!-- 操作提示消息框 -->
<?php $this->beginContent('//layouts/_error2');$this->endContent();?>
<!-- 操作提示消息框 -->

<!-- 条件过虑档位开始 -->
<div class="panel panel-default search-panel">
  <div class="panel-body">
    <form role="search" class="pull-left form-inline" action="<?php echo $this->createUrl('index'); ?>">
      <div class="form-group">
      时间:
      <input type="text" name="start" value="<?php echo $start;?>" class="form-control input-sm input-date" readonly id="starttime" />
	  -<input type="text" name="end" value="<?php echo $end;?>" class="form-control input-sm input-date" readonly id="starttime" />
	  <?php echo CHtml::dropDownList('warehouseId',$warehouseId,$warehouse,array('class'=>'form-control input-sm','empty'=>'请选择仓库'))?>
        <input type="text" name="product" value="<?php echo $product; ?>" placeholder="产品编码" class="form-control input-sm" data-suggestion="searchproduct" data-search="q=%s" data-api="/statistic/ajax/single/"  autocomplete="off" />
      </div>
      <button class="btn btn-sm btn-default">查找</button>
    </form>
	<div class="pull-right">
     <?php
        if($_GET && $data_list && $this->checkAccess('/statistic/export/excel')) {
          echo CHtml::link('导出当前数据', $this->createUrl('/statistic/export/excel', $_GET), ['class'=>"btn btn-primary",'role'=>"button"]);
        }
        ?>
    </div>

  </div>
</div>
<!-- 条件过虑档位结束 -->

<!--报表台头开始 -->
<?php if($data_list):?>
<br>
<div class="row">
  <div class="col-md-6">所属仓库：<?php echo $warehouse[$warehouseId];?>&nbsp;&nbsp;产品编号：<?php echo $product;?></div>
  <div class="col-md-6 text-right">报表区间：<?php echo $start;?> - <?php echo $end;?></div>
</div>
<?php endif;?>
<!--报表台头结束 -->


<!-- 数据显示区开始 -->
<div id="tableHead">
<table class="table table-condensed table-bordered">
	<colgroup>
	<col>
	<col width="12%">
    <col width="12%">
    <col width="12%">
    <col width="12%">
    <col width="12%">
    <col width="12%">
    <col width="12%">
  </colgroup>
  <thead>
    <tr class="text-center">
	  <td>时间</td>
	  <td>单号</td>
      <td>来源</td>
      <td>来源单号</td>
      <td>操作人</td>
      <td>入库</td>
      <td>出库</td>
      <td>结余</td>
    </tr>
  </thead>
</table>
</div>
<br>
<table class="table table-condensed table-bordered">
  <colgroup>
	<col>
	<col width="12%">
    <col width="12%">
    <col width="12%">
    <col width="12%">
    <col width="12%">
    <col width="12%">
    <col width="12%">
  </colgroup>
  <tbody>
    <!-- 统计列表数据 -->
    <?php if(!$data_list):?>
    <tr>
      <td colspan="8" class="text-center">无数据显示</td>
    </tr>
    <?php else: ?>
    <tr>
      <td colspan="7">上期结余</td>
      <td class="text-right"><?php echo $surplus;?></td>
    </tr>
    <?php foreach($data_list as $record){ ?>
    <tr class="text-center <?php echo $record['class']?>">
	  <td><?php echo $record['createTime'];?></td>
	  <td><?php echo $record['id'];?></td>
      <td><?php echo $record['source'];?></td>
      <td><?php echo $record['sourceId'];?></td>
      <td><?php echo $record['operator'];?></td>
      <td><?php echo $record['in'];?></td>
      <td><?php echo $record['out'];?></td>
      <td><?php echo $record['surplus'];?></td>
    </tr>
    <?php }?>
    <?php endif; ?>
  </tbody>
</table>
<!-- 数据显示区结束 -->
<br>
<?php if($data_list):?>
<div class="navbar-fixed-bottom" style="width:100%;padding:0 15px">
<table class="table table-condensed table-bordered text-center">
  <colgroup>
	<col>
	<col width="12%">
    <col width="12%">
    <col width="12%">
    <col width="12%">
    <col width="12%">
    <col width="12%">
    <col width="12%">
  </colgroup>
   <thead>
    <td colspan="2">本期结余</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
	  <td>&nbsp;</td>
      <td><?php echo $totalIn;?></td>
	  <td><?php echo $totalOut;?></td>
      <td><?php echo $record['surplus'];?></td>
  </thead>
</table>
</div>
<?php endif; ?>

<div class="navbar-fixed-top headerss hide" style="width:100%;padding:0 15px"></div>

<script>
  seajs.use('statics/app/statistic/js/list.js');
</script>