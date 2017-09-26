<?php
/**
 * Created by PhpStorm.
 * User: yagas
 * Date: 2016-08-04
 * Time: 14:13
 */
?>
<?php $this->beginContent('//layouts/_error2');$this->endContent();?>

<!-- 条件过虑档位开始 -->
<div class="panel panel-default search-panel">
    <div class="panel-body">
        <form role="search" class="pull-left form-inline" action="<?php echo $this->createUrl('/statistic/warehouse/index');?>">
            <div class="form-group">
                报表区间:
                <input type="text" name="start" value="<?php echo Yii::app()->request->getQuery('start');?>" class="form-control input-sm input-date" readonly id="starttime" onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})" />
                到
                <input type="text" name="end" value="<?php echo Yii::app()->request->getQuery('end');?>" class="form-control input-sm input-date" readonly id="endtime"  onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>
            </div>
            <button class="btn btn-sm btn-default">查找</button>
        </form>
    </div>
</div>
<!-- 条件过虑档位结束 -->

<!--报表台头开始 -->
<?php if($data_list):?>
    <div class="container-fluid">
        <div class="text-right">报表区间：<?php echo $start;?> - <?php echo $end;?></div>
    </div>
	<br>
<?php endif;?>
<!--报表台头结束 -->

<!-- 数据显示区开始 -->
<table class="table table-condensed table-bordered">
    <colgroup>
        <col width="20%">
        <col width="20%">
        <col width="20%">
        <col width="20%">
        <col width="20%">
    </colgroup>
    <thead>
    <tr class="text-center">
        <td>序号</td>
        <td>仓库</td>
        <td>入库数量</td>
        <td>出库数量</td>
        <td>结存数量</td>
    </tr>
    </thead>

    <tbody>
    <!-- 统计列表数据 -->
    <?php if(!$data_list):?>
        <tr>
            <td colspan="5" class="text-center">无数据显示</td>
        </tr>
        <?php
    else:
        $last = array_pop($data_list);
        foreach($data_list as $record){
            ?>
            <tr class="text-center">
                <td><?php echo $record['id'];?></td>
                <td><?php echo $record['warehouse'];?></td>
                <td class="text-right"><?php echo Order::quantityFormat($record['input']);?></td>
                <td class="text-right"><?php echo Order::quantityFormat($record['output']);?></td>
                <td class="text-right"><?php echo Order::quantityFormat($record['hand']);?></td>
            </tr>
        <?php }?>
        <!-- 合计数据 -->
        <tr class="text-center" style="background:#f5f5f5">
            <td>合计</td>
            <td>&nbsp;</td>
            <td class="text-right"><?php echo Order::quantityFormat($last['input']);?></td>
            <td class="text-right"><?php echo Order::quantityFormat($last['output']);?></td>
            <td class="text-right"><?php echo Order::quantityFormat($last['hand']);?></td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>
<!-- 数据显示区结束 -->
