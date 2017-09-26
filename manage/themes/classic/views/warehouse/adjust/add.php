<link rel="stylesheet" href="/themes/classic/statics/app/order/css/style.css" />
<link rel="stylesheet" href="/themes/classic/statics/app/warehouse/css/style.css" />
 <div class="panel panel-default search-panel">
   <div class="panel-body">
    <form class="pull-left form-inline" action="<?php echo $this->createUrl('add');?>">
	  <input type="text" class="form-control input-sm" name="singleNumber"  data-suggestion="singleNumber" data-search="serial=%s" data-api="/api/search_product_serial" autocomplete="off" placeholder="请输入单品编号" value="<?php echo $singleNumber;?>" />
     <button class="btn btn-sm btn-default" disabled id="btn-add" >查询</button>
    </form>
   </div>
  </div>
 <?php if( !empty( $adjustInfo ) ){ ?>
  <div class="panel panel-default">
	<div class="panel-heading clearfix">
	  <span class="col-md-4">单品编号：<?php echo $singleNumber;?></span>
		<span class="col-md-4">计算时间：<?php echo $adjustInfo['time'];?></span>
		<span class="col-md-4">上次调整时间：<?php echo $adjustInfo['lastAdjustTime'];?></span>
	</div>
	<ul class="list-group">
		<li class="list-group-item clearfix">
			<span class="col-md-4">可调整的成交总数：<?php echo $adjustInfo['dealNum'];?></span>
			<span class="col-md-4">调整比例：<?php echo $adjustInfo['adjustRatio'];?>‰</span>
			<span class="col-md-4">可调整数量：<?php echo $adjustInfo['adjustNum'];?></span>
		</li>
	</ul>
</div>
<?php if( $adjustInfo['adjustNum']>0 ){?>
<br>
<div class="ajax_form">
<form  method="post" action="">
	<table class="table table-condensed table-bordered">
	 <colgroup><col width="16%"><col width="16%"><col width="16%"><col width="16%"><col width="16%"><col></colgroup>
   <thead>
    <tr>
	 	<td>仓库</td>
		<td>仓位</td>
   		<td>产品批次</td>
   		<td>当前库存</td>
		<td>调整批次名称</td>
   		<td>调整数量</td>
	  </tr>
	  </thead>
	   <tbody id="b_<?php echo $singleNumber;?>" data-pid="">
	   </tbody>
	  <tfoot>
		<tr>
		<td colspan="6" align="center"><a href="javascript:" data-serial="<?php echo $singleNumber;?>">添加调整仓位</a></td>
		</tr>
	</tfoot>
</table>
<br>
<div class="panel panel-default">
	<ul class="list-group">
		<li class="list-group-item clearfix">
			<span class="col-md-12">备注：<input type="text" name="remark" class="form-control input-sm" maxlength="100" style="width:80%"/></span>
		</li>
	</ul>
</div>
<br>
 <?php $this->beginContent('//layouts/_error');$this->endContent();?><br/>
	<div class="text-center">
		<button class="btn btn-success"  id="btn-save">确定调整</button>
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
			      <div class="inline-block category-select"></div>
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
  <tr id="{{$value.position}}_{{$value.batch}}">
    <td class="storage">{{$value.storage}}</td>
	<td>{{$value.position}}
      <input type="hidden" name="data[{{$value.position}}_{{$value.batch}}][positionId]" value="{{$value.pid}}"/>
	  <input type="hidden" name="data[{{$value.position}}_{{$value.batch}}][positionTitle]" value="{{$value.position}}"/>
      </td>
       <td>{{$value.batch}}
      <input type="hidden" name="data[{{$value.position}}_{{$value.batch}}][oldbatch]" value="{{$value.batch}}"/></td>
      <td>{{$value.num}}{{$value.unit}}</td>
	  <td><input type="text" name="data[{{$value.position}}_{{$value.batch}}][batch]" class="form-control input-sm" maxlength="10" value="{{$value.batch}}"/></td>
      <td>
      <span><input type="text" name="data[{{$value.position}}_{{$value.batch}}][num]" value="" class="form-control input-sm" maxlength="6"/></span>
      <a href="javascript:" class="del">删除</a>
    </td>
  </tr>
  {{/each}}
</script>
 </div>
<br>
 <?php }}else{ ?>
	<?php $this->beginContent('//layouts/_error');$this->endContent();?> 
 <?php } ?>
 <script>
seajs.use('statics/app/warehouse/js/adjust.js');
</script>