<link rel="stylesheet" href="/themes/classic/statics/app/order/css/style.css" />
<link rel="stylesheet" href="/themes/classic/statics/app/warehouse/css/style.css" />
<div class="panel panel-default">
    <div class="panel-heading clearfix">
	<span class="col-md-4">订单编号：<?php echo $data['orderId'];?>
	<?php if($data['orderState'] == '7'){?>
			<span class="text-danger">（订单已取消）</span>
	<?php }?>
	</span>
	<span class="col-md-4">下单日期：<?php echo $data['orderTime'];?></span>
	<span class="col-md-4">提货方式：<?php echo $data['deliveryMethod'];?></span>
	</div>
    <ul class="list-group">
		<li class="list-group-item clearfix">
			<span class="col-md-4">分拣仓库：<?php echo $data['warehouse'];?></span>
			<span class="col-md-4">调拨发货仓库：<?php echo $data['deliveryWarehouse'];?></span>
		</li>
		<li class="list-group-item clearfix">
			<span class="col-md-4">分配分拣员：<?php echo $data['packinger'];?></span>
			<span class="col-md-4">分配人：<?php echo $data['distributioner'];?></span>
			<span class="col-md-4">分配时间：<?php echo $data['distributionTime'];?></span>
		</li>
    </ul>
  </div>
  <br/>
  <table class="table table-condensed table-bordered">
   <thead>
    <tr>
	 <td>产品编号</td>
	 <td>颜色</td>
	 <td>仓位</td>
     <td>产品批次</td>
	 <td>分配数量</td>
	</tr>
	  </thead>
	  <tbody>
	   <?php
		foreach( $data['distribution'] as $dval) :
			$count = count($dval['detail']);
			foreach( $dval['detail'] as $key=>$detail) :
		?>
	   <tr>
	   <?php if($key=='0'):?>
	   <td rowspan="<?php echo $count;?>"><?php echo $dval['singleNumber'];?></td>
	   <td rowspan="<?php echo $count;?>"><?php echo $dval['color'];?></td>
	   <?php endif;?>
	   <td><?php echo $detail['positionTitle'];?></td>
	   <td><?php echo $detail['productBatch'];?></td>
	   <td><?php echo $detail['distributionNum'];?></td>
	   </tr>
	  <?php endforeach;?>
	<?php endforeach;?>
	 </table><br/> <br/>
<?php if($data['orderState'] == '7'){?>
<form  method="post" action="">
	<div align="center">
		<button class="btn btn-default">关闭分拣单</button>
	</div>
 </form>
<?php }else{ ?>
<table class="table table-condensed table-bordered">
   <colgroup><col width="33%"><col width="33%"><col></colgroup>
   <thead>
    <tr>
	 <td>仓位号</td>
	 <td>产品批次</td>
     <td>分拣数量</td>
	  </tr>
	  </thead>
</table><br/>
<form  method="post" action="">
<?php foreach( $data['distribution'] as $dval) :?>
<table class="table table-condensed table-bordered distribution-list">
  <colgroup><col width="33%"><col width="33%"><col></colgroup>
	<tbody id="b_<?php echo $dval['singleNumber'];?>" data-pid="<?php echo $dval['orderProductId'];?>">
	<tr class="list-hd">
	 <td colspan="4">
		<span class="first">产品编号:<?php echo $dval['singleNumber'];?></span>
		<span>颜色:<?php echo $dval['color'];?></span>
		<span>分配数量：<span class="total"><?php echo Order::quantityFormat( $dval['total'] );?> </span><?php echo $dval['unit'];?></span>

		<?php if(!empty($dval['auxiliaryUnit'])){ ?>
		<span class="num">
		<input class="form-control input-sm int-only" type="text" value="<?php echo $dval['unitRate'];?>" name="unitRate[<?php echo $dval['orderProductId'];?>]"/>
		<?php echo $dval['unit'];?>/<?php echo $dval['auxiliaryUnit'];?>
		(总共：<span class="integer">
			<?php echo  ($dval['unitRate']>0)?floor($dval['total']/$dval['unitRate']):'';?>
			</span>
			<?php echo $dval['auxiliaryUnit'];?>
			<span class="remainder">
			<?php echo Order::unitMod( $dval['total'], $dval['unitRate'] );?>
			</span>
			<?php echo $dval['unit']?>)
		</span>
		<?php } ?>
	</td>
</tr>
<?php foreach ( $data['distribution'][$dval['orderProductId']]['detail'] as $key=>$_info ): ?>
<tr>
    <td><?php echo $_info['positionTitle'];?>
      <input name="pack[<?php echo $dval['orderProductId'];?>][<?php echo $key;?>][positionId]" value="<?php echo $_info['positionId'];?>" type="hidden">
      <input name="pack[<?php echo $dval['orderProductId'];?>][<?php echo $key;?>][positionTitle]" value="<?php echo $_info['positionTitle'];?>" type="hidden">
      </td>
       <td><?php echo $_info['productBatch'];?>
      <input name="pack[<?php echo $dval['orderProductId'];?>][<?php echo $key;?>][productBatch]" value="<?php echo $_info['productBatch'];?>" type="hidden"></td>
       <td>
      <span><input name="pack[<?php echo $dval['orderProductId'];?>][<?php echo $key;?>][packingNum]" value="<?php echo $_info['distributionNum'];?>" class="form-control input-sm num-float-only" type="text"></span>
      <a href="https://manage.leather.comjavascript:" class="del">删除</a>
    </td>
  </tr>

<?php endforeach;?>




<?php if( is_array($dataArr) && array_key_exists ( $dval['orderProductId'],$dataArr ) ){
	foreach ( $dataArr[$dval['orderProductId']] as $key=>$_info ){ ?>
<tr id="s_<?php echo $dval['singleNumber'];?>_<?php echo $key;?>"><?php echo $dval['orderProductId'];?>
    <td><?php echo $_info['positionTitle'];?>
      <input name="pack[<?php echo $dval['orderProductId'];?>][<?php echo $key;?>][positionId]" value="<?php echo $_info['positionId'];?>" type="hidden">
      <input name="pack[<?php echo $dval['orderProductId'];?>][<?php echo $key;?>][positionTitle]" value="<?php echo $_info['positionTitle'];?>" type="hidden">
      </td>
       <td><?php echo $_info['productBatch'];?>
      <input name="pack[<?php echo $dval['orderProductId'];?>][<?php echo $key;?>][productBatch]" value="<?php echo $_info['productBatch'];?>" type="hidden"></td>
       <td>
      <span><input name="pack[<?php echo $dval['orderProductId'];?>][<?php echo $key;?>][packingNum]" value="<?php echo $_info['packingNum'];?>" class="form-control input-sm num-float-only" type="text"></span>
      <a href="https://manage.leather.comjavascript:" class="del">删除</a>
    </td>
  </tr>
<?php }} ?>
</tbody>
<tfoot>
 <tr>
    <td colspan="6" align="center"><a href="#" data-serial="<?php echo $dval['singleNumber'];?>" data-opid ="<?php echo $dval['orderProductId'];?>" data-wid="<?php echo $data['warehouseId'];?>">添加分拣信息</a></td>
    </tr>
</tfoot>
</table><br/>
<?php endforeach;?>
<br/>
<?php if( is_array($data['applyInfo']) ){?>
 <div class="panel panel-default">
    <div class="panel-heading clearfix">
	<span class="text-danger">
	<?php echo $data['applyInfo']['title']?><br>
	<?php echo $data['applyInfo']['content']?></span>
	</div>
  </div>
  <br/>
<?php }?>
	 <?php $this->beginContent('//layouts/_error');$this->endContent();?><br/>
	<div align="center">
	<?php if($data['applyInfo']['opstate'] == tbWarehouseMessage::OP_CLOSE ){
		Yii::app()->session->add('packing_cancle',$data['orderId']);
	?>
		<button class="btn btn-default">关闭分拣单</button>
	<?php }else if($data['applyInfo']['opstate'] == tbWarehouseMessage::OP_HOLDON ){?>
		<button class="btn btn-success" disabled >确认分拣</button>
	<?php }else{ ?>
		<button class="btn btn-success">确认分拣</button>
	<?php }?>
	</div>
 </form>

  <div class="modal fade add-confirm" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="exampleModalLabel">编辑</h4>
      </div>
      <div class="modal-body">
        <div class="form-inline">
			    <div class="form-group">
			      <div class="inline-block category-select">

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
<div id="s_{{serial}}">
  <select class="pull-left form-control input-sm cate1-wrap" data-serial="{{serial}}" size="6">
    {{each data}}<option data-title="{{$value.areaTitle}}" value="{{$value.areaId}}">{{$value.areaTitle}}:{{$value.num}}{{$value.unit}}</option>{{/each}}
  </select>
  <select class="pull-left form-control input-sm cate2-wrap" data-serial="{{serial}}" size="6">
  </select>
  <div class="pull-left cate3-wrap">
  </div>
</div>
</script>
<script type="text/html" id="position">
  {{each data}}<option value="{{$value.positionId}}" data-positiontitle="{{$value.positionTitle}}">{{$value.positionTitle}}:{{$value.num}}{{$value.unit}}</option>{{/each}}
</script>
<script type="text/html" id="batch">
<ul class="form-control list-unstyled" id="b_{{id}}">
  {{each data}}
    <li>
    <label>
      <input id="c_{{serial}}_{{positionId}}_{{$value.productBatch}}" data-positiontitle="{{positiontitle}}" data-posid="{{positionId}}" data-batch="{{$value.productBatch}}" type="checkbox"/>{{$value.productBatch}}:{{$value.total}}{{$value.unit}}
    </label>
    </li>
  {{/each}}
</ul>
</script>
<script type="text/html" id="list">
  {{each data}}
  <tr>
    <td>{{$value.positiontitle}}
      <input type="hidden" name="pack[{{pid}}][{{t}}_{{$value.batch}}_{{$value.posid}}][positionId]" value="{{$value.posid}}"/>
      <input type="hidden" name="pack[{{pid}}][{{t}}_{{$value.batch}}_{{$value.posid}}][positionTitle]" value="{{$value.positiontitle}}"/>
      </td>
       <td>{{$value.batch}}
      <input type="hidden" name="pack[{{pid}}][{{t}}_{{$value.batch}}_{{$value.posid}}][productBatch]" value="{{$value.batch}}"/></td>
       <td>
      <span><input type="text" name="pack[{{pid}}][{{t}}_{{$value.batch}}_{{$value.posid}}][packingNum]" value="" class="form-control input-sm num-float-only"/></span>
      <a href="javascript:" class="del">删除</a>
    </td>
  </tr>
  {{/each}}
</script>
<script>
  var orderid = '<?php echo $data['orderId'];?>';
seajs.use('statics/app/warehouse/js/packing.js');</script>
<?php }?>