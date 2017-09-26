<link rel="stylesheet" href="/themes/classic/statics/app/order/css/style.css" />
<div class="panel panel-default search-panel">
   <div class="panel-body">
    <form role="search" class="pull-left form-inline" action="<?php echo $this->createUrl(Yii::app()->getController()->getAction()->id);?>">
     <div class="form-group">
      <input type="text" name="orderId" value="<?php echo $orderId;?>" placeholder="订单编号" class="form-control input-sm" />
	    <input type="text" name="singleNumber" value="<?php echo $singleNumber;?>" placeholder="产品编号" class="form-control input-sm">
     </div>
     <button class="btn btn-sm btn-default">查找</button>
    </form>
   </div>
  </div>
    <div class="clearfix well well-sm list-well">
	 <!--div class="pull-left form-inline col-md-8">
	  <div class="col-md-8">分拣次数:12/50次，分拣码数：100/500码</div>
	 <div class="col-md-4">
	  <div class="progress">
		<div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%;">60%</div>
	</div>
	</div>
 </div-->
   <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>
  <table class="table table-condensed table-bordered ">
  <colgroup><col width="20%"><col width="20%"><col><col></colgroup>
   <thead>
    <tr>
	<td>产品</td>
	<td>订单编号</td>
	<td>下单时间</td>
	<td>配送方式</td>
    <td>分拣区域</td>
	<td>需分拣数量</td>
	<td>辅助单位</td>
	<td>整卷</td>
	<td>零码</td>
	<td>操作</td>
    </tr>
   </thead>
    <tbody>
	 <?php foreach(  $list as $val  ){ ?>
	 <tr>
	 <td><?php echo $val['singleNumber'];?>&nbsp;<?php echo $val['color'];?></td>
	 <td><?php echo $val['orderId'];?></td>
	 <td><?php echo $val['orderTime'];?></td>
	 <td><?php echo $val['deliveryMethod'];?></td>
	 <td><?php echo $val['areaTitle'];?></td>
	 <td><?php echo $val['num'];?></td>
     <td><?php echo $val['unitConversion'];?></td>
	 <td><?php echo $val['whole'];?></td>
	 <td><?php echo $val['piece'];?></td>
	 <td>
	 <?php if( !empty( $val['areaTitle'] ) ) {?>
	 <a href="#" data-toggle="modal" data-target=".pack-confirm" data-id="<?php echo $val['orderProductId'];?>" data-product="<?php echo $val['singleNumber'];?>&nbsp;<?php echo $val['color'];?>">分拣</a>
	 <?php }else{ ?>
	  <span class="text-danger">待分配分区</span>
	 <?php }?>
	 </td>
    </tr>
	 <?php }?>
	</tbody>
   </table>
   <br>
	<div class="clearfix well well-sm list-well">
   <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>

<div class="modal fade pack-confirm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header alert-success">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="exampleModalLabel">分拣</h4>
      </div>
      <div class="modal-body">
	<form action="" method="post"  class="form-inline" >
	  <div class="panel panel-default" ></div>
	<div align="center">
      <button class="btn btn-success btn-lg" type="submit">确定分拣</button>
	</div>
	</form>
    </div>
    </div>
  </div>
</div>

<script>seajs.use('statics/app/warehouse/js/waitpacking.js');</script>
<script type="text/html" id="piece-input">
 <div class="row packForm">
		<div class = "pull-right ">
		<input type="text" name="pieces[]" value="" class="form-control input-sm num-float-only">
		<span class="glyphicon glyphicon-minus-sign"></span>
		</div>
		</div>
</script>

<script type="text/html" id="pack-info">
<ul class="list-group">
	<input type="hidden" name="orderProductId" value="{{orderProductId}}"/>
	 <li class="list-group-item disabled clearfix">
		<span class="col-md-6">分拣员：<?php echo Yii::app()->getUser()->getstate('username');?></span>
		<span class="col-md-6">发货仓库：{{Dwarehouse}}</span>
	 </li>
	 <li class="list-group-item disabled clearfix">
		<span class="col-md-6">客户名称：{{companyname}}</span>
		<span class="col-md-6">订单号：{{orderId}}</span>
	 </li>
	 <li class="list-group-item disabled clearfix">
		<span class="col-md-6">提货方式：{{deliveryMethod}}</span>
		<span class="col-md-6">下单时间：{{orderTime}}</span>
	 </li>
	 <li class="list-group-item disabled clearfix">
		<span class="col-md-6">订单数量：<span class="text-danger">{{num}}</span>米</span>
		<span class="col-md-6">辅助数量：{{unitConversion}}{{unit}}/{{auxiliaryUnit}}</span>
	 </li>
	 <li class="list-group-item disabled clearfix">
		<span class="col-md-12">特殊要求：<span class="text-danger">{{memo}}</span></span>
	 </li>

	 <li class="list-group-item clearfix">
		<span class="col-md-6">整料仓位：<select name="wholePosition" class="form-control input-sm">
		 {{each positions}}<option value="{{$value}}">{{$value}}</option>{{/each}}
		</select></span>
		<span class="col-md-6"><span class="clear-top">整料数量({{auxiliaryUnit}})：</span>
		<div class=" pull-right packForm new-packForm ">
        <input type="text" name="wholeNum" value="{{whole}}" class="form-control input-sm int-only">
        </div>
		</span>
	 </li>
	 <li class="list-group-item clearfix">
		<span class="col-md-6">零码仓位：
		<select name="piecePosition" class="form-control input-sm">
		 {{each positions}}<option value="{{$value}}">{{$value}}</option>{{/each}}
		</select></span>
		<span class="col-md-6 ">
		<span class="clear-top">零码数量({{unit}})：</span>
		<div class = "pull-right pieces-list">
		<div class="row packForm">
		<input type="text" name="pieces[]" value="{{piece}}" class="form-control input-sm num-float-only">
		<span class="alert-success"><span class="glyphicon glyphicon-plus-sign"></span></span>
		</div>
		</div>
		</span>
	 </li>
	<li class="list-group-item list-group-item-warning clearfix">
		<span class="col-md-6">标签数量：<span class="tags">{{tags}}</span>张</span>
		<span class="col-md-6">分拣数量：<span class="text-danger packing-num">{{num}}</span>米</span>
	 </li>
	</ul>
</script>