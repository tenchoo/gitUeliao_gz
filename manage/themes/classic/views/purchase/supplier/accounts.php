<?php
/** 供应商帐号列表 */
?>
<div class="panel panel-default search-panel">
    <div class="panel-body">
        <div class="pull-right form-inline"><a href="<?php echo $this->createUrl('newaccount',['id'=>Yii::app()->request->getQuery('id')]);?>" class="btn btn-default btn-sm">添加账号</a></div>
    </div>
</div>

<table class="table table-condensed table-bordered">
    <colgroup><col><col width="25%"></colgroup>
    <thead>
    <tr class="list-hd">
        <td>账号</td>
        <td>管理</td>
    </tr>
    </thead>

    <tbody>
    <?php foreach($sellers as $row){?>
    <tr>
        <td><?php echo $row->account;?></td>
        <td><?php echo CHtml::link('编辑', $this->createUrl('editaccount',['id'=>$row->uid]));?></td>
    </tr>
    <?php } ?>
    </tbody>
</table>