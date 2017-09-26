<link rel="stylesheet" href="/themes/classic/statics/app/order/css/style.css" />
<link rel="stylesheet" href="/themes/classic/statics/app/warehouse/css/style.css" />
<div class="panel panel-default">
  <div class="panel-heading clearfix">
		<span class="col-md-4"><?php echo $model->orderType;?>：<?php echo $model->orderId;?></span>
		<span class="col-md-4">下单日期：<?php echo $model->createTime;?></span>
		<span class="col-md-4">业务员：<?php echo $salesman;?></span>
	</div>
  <ul class="list-group">
	  <li class="list-group-item clearfix">
	    <span class="col-md-4">联系人：<?php echo $model->name;?>（<?php echo $model->tel;?>）</span>
			<span class="col-md-4">收货地址：<?php echo $model->address;?></span>
			<span class="col-md-4">提货方式：<?php echo $model->deliveryMethod;?></span>
		</li>
	  <li class="list-group-item clearfix">
			<span class="col-md-12">备注：<?php echo $model->memo;?></span>
		</li>
  </ul>
</div>
<form  method="post" action="">
<div class="panel panel-default">
  <br>
	<div class="clearfix distribution-item">
		<span class="name"><span class="text-danger">*</span>选择发货仓: </span>
		<span><?php echo CHtml::dropDownList('warehouseId','',$warehouse,array('class'=>'form-control input-sm'))?></span>
  </div>
  <br>
</div>
<br>
	<table class="table table-condensed table-bordered">
	 <colgroup><col width="20%"><col width="20%"><col width="20%"><col width="20%"><col></colgroup>
   <thead>
    <tr>
	 	<td>仓库</td>
		<td>仓位</td>
   		<td>产品批次</td>
   		<td>总库存</td>
   		<td>分配数量</td>
	  </tr>
	  </thead>
	</table>
	<br>
	 <?php foreach( $model->products as $pval) :?>
	 <table class="table table-condensed table-bordered distribution-list">
	 <colgroup><col width="20%"><col width="20%"><col width="20%"><col width="20%"><col></colgroup>
	 <tbody id="b_<?php echo $pval['singleNumber'];?>" data-pid="<?php echo $pval['orderProductId'];?>">
	 <tr class="list-hd">
		<td colspan="5">
		<span class="first">产品编号:<?php echo $pval['singleNumber'];?></span>
		<span>颜色：<?php echo $pval['color'];?></span>
		<span>购买数量：<span class="total"><?php echo Order::quantityFormat($pval['num']);?></span><?php echo $productUnits[$pval->productId]['unit']?></span>
		<?php if( array_key_exists( $pval['orderProductId'],$refunds ) ){ ?>
		<span class="text-danger">退货数量：<span class="total" ><?php echo Order::quantityFormat($refunds[$pval['orderProductId']]['num']);?></span><?php echo $productUnits[$pval->productId]['unit']?></span>
		<span>购买数量 - 退货数量 = <span class="total"><?php echo Order::quantityFormat( $refunds[$pval['orderProductId']]['residualNum'] );?></span><?php echo $productUnits[$pval->productId]['unit']?></span>
		<?php }?>

		<?php if(!empty($productUnits[$pval->productId]['auxiliaryUnit'])){ ?>
		<span class="num">
		<input class="form-control input-sm num-float-only" type="text" value="<?php echo $unitRate[$pval['orderProductId']];?>" name="unitRate[<?php echo $pval['orderProductId'];?>]"/>
		<?php echo $productUnits[$pval->productId]['unit']?>/<?php echo $productUnits[$pval->productId]['auxiliaryUnit']?>
		(总共：<span class="integer">
			<?php echo  ($unitRate[$pval['orderProductId']]>0)?floor($pval['num']/$unitRate[$pval['orderProductId']]):'0';?>
			</span>
			<?php echo $productUnits[$pval->productId]['auxiliaryUnit']?>
			<span class="remainder">
			<?php echo Order::unitMod( $pval['num'], $unitRate[$pval['orderProductId']] );?>
			</span>
			<?php echo $productUnits[$pval->productId]['unit']?>)
		</span>
		<?php } ?>
		</td>
	</tr>
	<?php if(isset($dataArr[$pval['orderProductId']])){
		foreach ( $dataArr[$pval['orderProductId']] as  $key=>$vval){
		$name = 'data['.$pval['orderProductId'].']['.$key.']';
	?>
	<tr id="s_<?php echo $pval['singleNumber'];?>_<?php echo $vval['warehouseId'];?>_<?php echo $key;?>">
	 <td><?php echo $warehouse[$vval['warehouseId']];?>
	 <input type="hidden" name="<?php echo $name;?>[warehouseId]" value="<?php echo $vval['warehouseId'];?>"/>
	 </td>
     <td><?php echo $vval['productBatch'];?>
	 <input type="hidden" name="<?php echo $name;?>[productBatch]" value="<?php echo $vval['productBatch'];?>"/></td>
	  <td><?php echo $vval['inventory'];?>
	 <input type="hidden" name="<?php echo $name;?>[inventory]" value="<?php echo $vval['inventory'];?>"/></td>
     <td>
	 <span><input type="text" name="<?php echo $name;?>[distributionNum]" value="<?php echo $vval['distributionNum'];?>" class="form-control input-sm"/></span>
	 <a href="javascript:" class="del">删除</a>
	 </td>
	</tr>
	<?php } ?>
  <tr class="empty hide" style="display:none">
    <td colspan="3" align="center">暂时没有数据，请编辑</td>
  </tr>
  <?php }else{ ?>
	<tr class="empty" style="display:none">
		<td colspan="3" align="center">暂时没有数据，请编辑</td>
	</tr>
	<?php }?>

	</tbody>
	<tfoot>
	<?php if( !$isClose ){ ?>
		<tr>
      <td colspan="6" align="center"><a href="javascript:" data-serial="<?php echo $pval['singleNumber'];?>">添加分拣仓库</a></td>
    </tr>
	<?php }?>
</tfoot>
	 </table><br/>
	<?php endforeach;?>
	<br/>
  <template v-if="hasData">
  <table  class="table table-condensed table-bordered">
    <tbody>
      <tr class="list-hd">
        <td>选择分拣员</td>
      </tr>
      <tr v-for="(id,item) of warehouses">
        <td class="form-inline">
          <span class="text-danger">*</span>{{item.w}}：
          <select class="form-control input-sm" style="width:180px" name="packinger[{{id}}]" @change="doSelect(id,$event)">
            <template v-if="sorters[id]">
            <template v-if="sorters[id].length > 0">
              <option value="">请选择</option>
              <option v-for="i in sorters[id]" :selected="i.userId == selected[id]" value="{{i.userId}}">{{i.username}}</option>
            </template>
            <option v-else="sorters[id].length == 0" value="">此仓库暂无分拣员，请先去添加分拣员</option>
            </template>
            <option v-else value="">加载中……</option>
          </select>
        </td>
      </tr>
    </tbody>
  </table>
  </template>
<br>
	 <?php $this->beginContent('//layouts/_error');$this->endContent();?><br/>
	<div class="text-center">
		<?php if( $isClose ){ ?>
		<button class="btn btn-success" >关闭分配并确认退货入库</button>
		<?php }else{ ?>
		<button class="btn btn-success loading" <?php if( $applycolse ){?> disabled <?php }?> >保存</button>
		<?php if( $applycolse ){?>
			<span class="text-danger">（客户申请取消，待业务员审核）</span>
		<?php } }?>
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

<script type="text/html" id="storage">
<div id="s_{{serial}}">
  <select class="pull-left form-control input-sm house" data-serial="{{serial}}" size="6">
    {{each data}}<option data-title="{{$value.title}}" value="{{$value.warehouseId}}">{{$value.title}}:{{$value.total}}{{$value.unit}}</option>{{/each}}
  </select>
  <select class="pull-left form-control input-sm area" data-serial="{{serial}}" size="6">
  </select>
  <select class="pull-left form-control input-sm position" data-serial="{{serial}}" size="6">
  </select>
  <div class="pull-left cate2-wrap">
  </div>
</div>
</script>
<script type="text/html" id="area">
  {{each data}}<option value="{{$value.areaId}}">{{$value.areaTitle}}:{{$value.num}}{{$value.unit}}</option>{{/each}}
</script>
<script type="text/html" id="position">
  {{each data}}<option value="{{$value.positionId}}" data-title="{{$value.positionTitle}}">{{$value.positionTitle}}:{{$value.num}}{{$value.unit}}</option>{{/each}}
</script>
<script type="text/html" id="batch">
<ul class="form-control list-unstyled" id="b_{{id}}">
  {{each data}}
    <li>
    <label>
      <input id="c_{{serial}}_{{storageId}}_{{$value.productBatch}}" data-storage="{{storage}}" data-position="{{position}}" data-pid="{{positionId}}" data-sid="{{storageId}}" data-batch="{{$value.productBatch}}" data-num="{{$value.total}}" data-unit="{{$value.unit}}" type="checkbox"/>{{$value.productBatch}}:{{$value.total}}{{$value.unit}}
    </label>
    </li>
  {{/each}}
</ul>
</script>
<script type="text/html" id="list">
  {{each data}}
  <tr>
    <td class="storage">{{$value.storage}}
      <input type="hidden" name="data[{{pid}}][{{t}}_{{$value.batch}}_{{$value.position}}][warehouseId]" value="{{$value.sid}}"/>
      </td>
	<td>{{$value.position}}
      <input type="hidden" name="data[{{pid}}][{{t}}_{{$value.batch}}_{{$value.position}}][positionId]" value="{{$value.pid}}"/>
	  <input type="hidden" name="data[{{pid}}][{{t}}_{{$value.batch}}_{{$value.position}}][positionTitle]" value="{{$value.position}}"/>
      </td>
       <td>{{$value.batch}}
      <input type="hidden" name="data[{{pid}}][{{t}}_{{$value.batch}}_{{$value.position}}][productBatch]" value="{{$value.batch}}"/></td>
      <td>
      {{$value.num}}{{$value.unit}}
      <input type="hidden" name="data[{{pid}}][{{t}}_{{$value.batch}}_{{$value.position}}][inventory]" value="{{$value.num}}"/>
      </td>
      <td>
      <span><input type="text" name="data[{{pid}}][{{t}}_{{$value.batch}}_{{$value.position}}][distributionNum]" value="" class="form-control input-sm"/></span>
      <a href="javascript:" class="del">删除</a>
    </td>
  </tr>
  {{/each}}
</script>
<script>seajs.use('statics/app/warehouse/js/distribution.js');</script>