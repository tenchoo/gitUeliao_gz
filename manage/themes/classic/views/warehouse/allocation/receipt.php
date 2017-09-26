<link rel="stylesheet" href="/themes/classic/statics/modules/warehouse/css/style.css" />
<div class="panel panel-default">
  <div class="panel-heading clearfix">
    <span class="col-md-4">调拨单号：<?php echo $data['allocationId']?> <?php if( $data['type'] == tbAllocation::TYPE_CALLBACK ){ ?>(回调产品)<?php }?></span>
	<span class="col-md-4">订单编号：<?php echo ($data['orderId'])?$data['orderId']:'0000'?></span>
	<span class="col-md-4">下单时间：<?php echo $data['orderTime'];?></span>
  </div>
	<ul class="list-group">
		<li class="list-group-item clearfix">
			<span class="col-md-4">原仓库：<?php echo $data['warehouse']?></span>
			<span class="col-md-4">调拨人：<?php echo $data['userName']?></span>
			<span class="col-md-4">调拨时间：<?php echo $data['createTime']?></span>
		</li>
		<li class="list-group-item clearfix">
			<span class="col-md-4">目标仓库：<?php echo $data['targetWarehouse']?></span>

		</li>
	</ul>
</div>
<br>
<table class="table table-condensed table-bordered">
  <thead>
    <tr>
      <td>产品编号</td>
      <td>颜色</td>
      <td>产品批次</td>
      <td>调拨数量</td>
    </tr>
  </thead>
  <tbody>
<?php foreach( $data['detail'] as $dval) :?>
	<tr>
      <td><?php echo $dval['singleNumber']?></td>
      <td><?php echo $dval['color']?></td>
      <td><?php echo $dval['productBatch']?></td>
      <td><?php echo Order::quantityFormat( $dval['num'] ) ;?><?php echo $dval['unit']?></td>
    </tr>
<?php endforeach;?>
	</tbody>
</table>
<br />
<form class="form-horizontal" method="post" action="">
  <input type="hidden" name="warehouseid" value="<?php echo $data['targetWarehouseId'];?>">
  <table class="table table-condensed table-bordered receipt">
    <thead>
      <tr>
        <td colspan="6">调拨入库</td>
      </tr>
      <tr>
        <td>产品编号</td>
        <td>颜色</td>
        <td>产品批次</td>
        <td width="15%">入库数量</td>
        <td width="15%">入库仓位</td>
        <td width="15%">操作 <a href="javascript:" class="batch-position">批量选择仓位</a></td>
      </tr>
    </thead>
    <tbody>
<?php foreach( $saveData as $key=>$pval) :?>
      <tr class="list-bd">
        <td><?php echo $pval['singleNumber'];?>
		<input type="hidden" name="data[<?php echo $key;?>][singleNumber]" value="<?php echo $pval['singleNumber'];?>" /> <input type="hidden" name="data[<?php echo $key;?>][productId]" value="<?php echo $pval['productId'];?>" /></td>
        <td><?php echo $pval['color'];?>
		<input type="hidden" name="data[<?php echo $key;?>][color]" value="<?php echo $pval['color'];?>" /></td>
        <td>
		<?php echo $pval['productBatch'];?>
		<input type="hidden" name="data[<?php echo $key;?>][productBatch]" value="<?php echo $pval['productBatch'];?>"/>
	 </td>
     <td>
	 <div class="input-group title-group">
		<input type="text" class="form-control input-sm num-float-only" name="data[<?php echo $key;?>][num]" value="<?php echo Order::quantityFormat( $pval['num'] );?>"/>
		<input type="hidden" name="data[<?php echo $key;?>][unit]" value="<?php echo $pval['unit'];?>"/>
		<div class="input-group-addon">
		<?php echo $pval['unit']?>
		</div>
		</div>
	</td>
	 <td class="positioninfo">
	 <span><?php echo !empty($pval['positionId'])?$pval['positionTitle']:'未选择'; ?></span>
	<input type="hidden" name="data[<?php echo $key;?>][positionId]" value="<?php echo isset($pval['positionId'])?$pval['positionId']:'';?>" class="positionId"/>
	<input type="hidden" name="data[<?php echo $key;?>][positionTitle]" value="<?php echo isset($pval['positionId'])?$pval['positionTitle']:'';?>" class="positionTitle"/>
        </td>
        <td>
			<a href="#" class="choose-position">选择仓位</a>
		<?php if($key> count($data['detail'])){ ?>
			<a class="del" href="/javascript:">删除</a>
		<?php } ?>
		</td>
      </tr>
<?php endforeach;?>
</tbody>
<tfoot>
		<tr>
      <td colspan="6" align="center"><a href="#" data-toggle="modal" data-target=".add-product">添加产品</a></td>
    </tr>
</tfoot>
  </table>
  <br />
<?php $this->beginContent('//layouts/_error');$this->endContent();?><br />
  <div align="center">
    <button class="btn btn-success">确定收货</button>
  </div>
</form>
<div class="modal fade add-product" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="exampleModalLabel">添加产品</h4>
      </div>
      <div class="modal-body">
        <br>
        <table class="table table-condensed table-bordered">
          <thead>
            <tr>
              <td width="2%"><input type="checkbox" class="checkedall"></td>
              <td width="30%">产品编号</td>
              <td width="30%">颜色</td>
              <td>产品批次</td>
            </tr>
          </thead>
          <tbody>
          <?php foreach( $data['detail'] as $key=>$dval) :?>
	          <tr>
              <td><input type="checkbox" name="" value="<?php echo $key;?>" /></td>
              <td><?php echo $dval['singleNumber']?></td>
              <td><?php echo $dval['color']?></td>
              <td><?php echo $dval['productBatch']?></td>
            </tr>
          <?php endforeach;?>
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
        <button type="button" class="btn btn-success">确定添加</button>
      </div>
    </div>
  </div>
</div>

 <script type="text/html" id="receiptlist">
    <tr class="list-bd">
		<td>{{singleNumber}}
            <input type="hidden" name="data[{{id}}][productId]" value="{{productId}}" />
            <input type="hidden" name="data[{{id}}][singleNumber]" value="{{singleNumber}}" />
        </td>
		<td>{{color}}
			<input type="hidden" name="data[{{id}}][color]" value="{{color}}" />
		</td>
		<td>{{productBatch}}
			<input type="hidden" name="data[{{id}}][productBatch]" value="{{productBatch}}" />
		</td>
		<td>
			<div class="input-group title-group">
			<input name="data[{{id}}][num]" class="form-control input-sm num-float-only" />
			<input type="hidden" name="data[{{id}}][unit]" value="{{unit}}"/>
			<div class="input-group-addon">{{unit}}</div>
			</div>
		</td>
		<td class="positioninfo"><span>未选择</span>
		<input type="hidden" name="data[{{id}}][positionId]" class="positionId"/>
		<input type="hidden" name="data[{{id}}][positionTitle]" class="positionTitle"/>
		</td>
		<td><a href="" class="choose-position">选择仓位</a> <a href="" class="del">删除</a></td>
	</tr>
</script>

<script>
var productData = <?php echo json_encode($data['detail']);?>;
</script>


 <div class="modal fade add-position" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="exampleModalLabel">选择仓位</h4>
      </div>
      <div class="modal-body">
        <div class="form-inline">
			    <div class="form-group">
			      <div class="inline-block category-select">
			        <select name="" class="form-control input-sm cate1" size="6">
			        </select>
			        <select name="" class="form-control input-sm cate2" size="6">
			          <option value="default">请选择</option>
			        </select>
			        <br>
			      </div>
			    </div>
  			</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
        <button type="button" class="btn btn-success save">保存</button>
      </div>
    </div>
  </div>
</div>

<script type="text/html" id="area">
  {{each}}<option value="{{$value.positionId}}">{{$value.title}}</option>{{/each}}
</script>

<script>seajs.use('statics/app/warehouse/js/receipt.js');</script>

