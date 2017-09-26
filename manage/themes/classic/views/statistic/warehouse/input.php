<?php
/**
 * Created by PhpStorm.
 * User: yagas
 * Date: 2016-08-04
 * Time: 15:17
 */
$this->beginContent('//layouts/_error2');$this->endContent();?>

<!-- 条件过虑档位开始 -->
<div class="panel panel-default search-panel">
    <div class="panel-body">
        <form role="search" class="pull-left form-inline" action="<?php echo $this->createUrl('/statistic/warehouse/input');?>">
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
        <col>
        <col>
        <col>
        <col>
        <col>
        <col>
        <col>
        <col>
        <col>
        <col>
        <col>
        <col>
    </colgroup>
    <thead>
    <tr class="text-center">
        <td>入库时间</td>
        <td>入库单号</td>
        <td>供应商</td>
        <td>入库类型</td>
        <td>货品名称</td>
        <td>明细</td>
        <td>米数</td>
        <td>码数</td>
        <td>仓库</td>
        <td>仓位</td>
        <td>承运人</td>
        <td>备注</td>
    </tr>
    </thead>

    <tbody>
    <!-- 统计列表数据 -->
    <?php if(!$data_list):?>
        <tr>
            <td colspan="12" class="text-center">无数据显示</td>
        </tr>
        <?php
    else:
        $last = array_pop($data_list);
        foreach($data_list as $record){
			if( $record['number'] == '0' && $record['meter']> 0 ){			
				$record['number'] = $this->meterTOyd( $record['meter'] );
			}else if( $record['meter'] == '0' && $record['number']>0 ){
				$record['meter'] = $this->ydTOmeter( $record['number'] );
			}
       ?>
            <tr class="text-center">
                <td><?php echo $record['dealTime'];?></td>
                <td><?php echo $record['warrantId'];?></td>
                <td><?php echo $record['supplier'];?></td>
                <td><?php echo $record['source'];?></td>
                <td><?php echo $record['singleNumber'];?></td>
                <td><?php echo $record['detail'];?></td>
                <td><?php echo $record['meter'];?></td>
                <td><?php echo $record['number'];?></td>
                <td><?php echo $record['warehouse'];?></td>
                <td><?php echo $record['positionName'];?></td>
                <td><?php echo $record['driver'];?></td>
                <td><?php echo $record['remark'];?></td>
            </tr>
        <?php }?>
    <?php endif; ?>
    </tbody>
</table>