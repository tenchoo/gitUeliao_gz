<link href="/themes/classic/statics/app/order/css/style.css" rel="stylesheet"/>
<!-- 入库单信息 -->
<?php
$this->breadcrumb = '仓库管理,撤消入库,撤消入库申请详情';
$this->pageTitle = '撤消入库申请详情';
$warrant = $detail->warrant();
$details = $warrant->detail();
?>
<div class="panel panel-default">
    <div class="panel-heading clearfix">
        <span class="col-md-4">撤消入库申请单号：<?php echo $detail->repealId; ?></span>
        <span class="col-md-4">操作时间：<?php echo substr($detail->createTime, 0, 10); ?></span>
        <span class="col-md-4">操作员：<?php echo $detail->operator; ?></span>
    </div>
    <ul class="list-group">
        <li class="list-group-item clearfix">
            <span class="col-md-4">入库单号：<?php echo $warrant->warrantId; ?></span>
            <span class="col-md-4">操作时间：<?php echo substr($warrant->createTime, 0, 10); ?></span>
            <span class="col-md-4">操作员：<?php echo $warrant->operator; ?></span>
        </li>
        <li class="list-group-item clearfix">
            <span class="col-md-12">撤消申请备注：<?php echo $detail->remark; ?></span>
        </li>
    </ul>
</div>
<br />
<!-- 入库单信息 -->

<!-- 入库单详细信息 -->
<table class="table table-condensed table-bordered">
    <thead>
        <tr>
            <td>产品编号</td>
            <td>颜色</td>
            <td>仓位</td>
            <td>入库数量</td>
            <td>产品批次</td>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($details as $key => $product):
    $unit = ZOrderHelper::getUnitName($product->singleNumber); ?>
        <tr class="order-list-bd">
            <td><?php echo $product->singleNumber; ?></td>
            <td><?php echo $product->color; ?></td>
            <td><?php echo $product->positionName; ?></td>
            <td><?php echo $product->num . $unit; ?></td>
            <td><?php echo $product->batch; ?></td>
        </tr>
        <?php endforeach;?>
    </tbody>
</table>
<!-- 入库单详细信息 -->

<!-- 审核控制栏 -->
<?php if($detail->state == 0): ?>
    <br />
    <form action="" method="post">
    <input type="hidden" name="action" value="1">
    <div class="text-center">
    <button type="button" class="btn btn-danger" data-toggle="modal" data-target=".modal"><span class="glyphicon glyphicon-remove"></span> 拒绝通过</button>
    <button type="button" class="btn btn-success"><span class="glyphicon glyphicon-ok"></span> 同意撤消</button>
    </div>


    <div class="modal fade" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-md">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">请输入拒绝理由</h4>
          </div>
          <div class="modal-body">
            <textarea name="reasons" class="form-control" rows="4" required maxlength="50" ></textarea>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
            <button type="submit" class="btn btn-danger">确定拒绝</button>
          </div>
        </div>
      </div>
    </div>
    </form>
    <script>seajs.use('statics/app/warehouse/js/repeal.js');</script>
<?php endif; ?>
<!-- 审核控制栏 -->

<br/>


<div class="panel panel-default">
	<div class="panel-heading clearfix">
	  <span class="col-md-4">操作日志</span>
	</div>
	<ul class="list-group">
	<?php foreach ( $oplog as $item ){?>
		<li class="list-group-item clearfix">
			<span class="col-md-12">
				<?php echo $item->datetime;?>
				&nbsp;&nbsp;&nbsp;&nbsp;
				<?php echo $this->labels($item->action);?>
				&nbsp;&nbsp;&nbsp;&nbsp;
				<?php echo $item->operator;?>
			</span>
		</li>
	<?php }?>
	</ul>
</div>
<br>