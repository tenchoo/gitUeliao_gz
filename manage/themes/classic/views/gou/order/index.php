    <div class="panel panel-default search-panel">
   <div class="panel-body">
    <form role="search" class="pull-left form-inline" action="<?php echo $this->createUrl(Yii::app()->getController()->getAction()->id);?>">
     <div class="form-group">
      <div class="inline-block pull-left">
		<input type="text" name="keyword" value="<?php echo $keyword;?>" placeholder="搜索内容" class="form-control input-sm" />
		<?php echo CHtml::dropDownList('type',$type,$types,array('class'=>'form-control input-sm','empty'=>'按啥查'))?>
		<?php echo CHtml::dropDownList('areaId',$areaId,$areas,array('class'=>'form-control input-sm','empty'=>'片区'))?>
		<?php echo CHtml::dropDownList('state',$state,$states,array('class'=>'form-control input-sm','empty'=>'配送'))?>
		<?php echo CHtml::dropDownList('deliverymanId',$deliverymanId,$mems,array('class'=>'form-control input-sm','empty'=>'送货员'))?>
		<?php echo CHtml::dropDownList('isDel',$isDel,array('0'=>'未关闭的','1'=>'已关闭的'),array('class'=>'form-control input-sm','empty'=>'全部'))?>

	 </div>
     </div>
     <button class="btn btn-sm btn-default">查找</button>
    </form>
	<div class="pull-right">
      <a class="btn btn-default" href="<?php echo $this->createUrl( 'add',array('from'=> urlencode($from) ) );?>">新增</a>
	  <a class="btn btn-default" href="<?php echo $this->createUrl( 'import' );?>">excel导入</a>
	  <a class="btn btn-default" href="<?php echo $this->createUrl( '/gou/dorder/index/c/95a44df75c30050fca4c17532dbf9980' );?>" target="_blank">手机端界面</a>

    </div>
   </div>
  </div>
 <?php 
 //去除不需要的选择项
 unset( $mems['-1'],$areas['-1'] );
 ?>
    <div class="clearfix well well-sm list-well">
   <div class="pull-left form-inline">
    <label class="checkbox-inline"> <input type="checkbox" class="checkedall"/> 全选 </label>
    <button class="btn btn-sm btn-default"  data-toggle="modal" data-target=".setarea-confirm" title="批量设置片区" >设置片区</button>
	<button class="btn btn-sm btn-default"  data-toggle="modal" data-target=".setdeliveryman-confirm" title="批量设置送货员" >设置送货员</button>
	<button class="btn btn-sm btn-default setBatch" title="批量设置已送" data-url="<?php echo $this->createUrl('setsend')?>" >批量已送</button>
	<button class="btn btn-sm btn-default setBatch" title="批量关闭" data-url="<?php echo $this->createUrl('setdel')?>"  >批量关闭</button>

	<a class="btn btn-sm btn-default" href="<?php echo $excelUrl ;?>" title="excel导出当前列表">excel导出</a>
	&nbsp;&nbsp;总件数: <span class="text-danger"> <strong><?php echo $totalNum;?></strong> </span>
	&nbsp;&nbsp;总订单数: <span class="text-danger"> <strong><?php echo $pages->itemCount;?></strong> </span>

   </div>
   <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>
  <table class="table table-condensed table-bordered">
   <colgroup><col width="50px"><col  width="100px"><col width="8%"><col width="10%"><col width="13%"><col width="8%"><col width="50px"><col width="8%"><col width="8%"><col><col width="20px"></colgroup>
   <thead>
    <tr>
	 <td>序号</td>
   <td>订单号</td>
	 <td>收货人</td>
	 <td>片区</td>
	 <td>收货人地址</td>
	 <td>手机号码</td>
	 <td>数量</td>
	 <td>配送</td>
	 <td>送货员</td>
	 <td>商品标题</td>
   <td></td>
    </tr>
   </thead>
  <tbody>
   <?php
   $varPage = $pages->currentPage*$pages->pageSize;
   $classAttr = array('0'=>'alert-danger','1'=>'alert-success','2'=>'alert-info','3'=>'alert-warning');
   foreach(  $list as  $index=>$val  ):
	 $class = isset($classAttr[$val['state']])?$classAttr[$val['state']]:'';
   ?>
    <tr class="<?php echo $class;?>">
	<form action="<?php echo $this->createUrl('edit',array('id' =>$val['id'] ));?>" method="post">
      <td><input type="checkbox" value="<?php echo $val['id'];?>" name="memberId[]"/> <?php echo $index+1+$varPage;?></td>
	 <td style="word-break:break-all; " title="<?php echo $val['orderId'];?>">
   <?php echo $val['orderId'];?>
   </td>
	 <td><a href="<?php echo $this->createUrl('edit',array('id' =>$val['id'],'from'=> urlencode($from) ));?>" title="编辑" target="_blank"><?php echo $val['name'];?></a></td>
	 <td><?php echo CHtml::dropDownList('data[areaId]',$val['areaId'],$areas,array('class'=>'form-control input-sm do-save'))?></td>
	 <td>
		<span title="订单地址：<?php echo $val['orderAddress'];?>"><?php echo $val['deliveryAddress'];?></span>
	 </td>
	 <td><?php echo $val['phone'];?></td>
	  <td><?php if( $val['num']>1 ){ ?><strong><?php echo $val['num'];?></strong><?php }else{ echo $val['num']; }?>	</td>
	 <td><?php echo CHtml::dropDownList('data[state]',$val['state'],$states,array('class'=>'form-control input-sm  do-save'))?></td>
	 <td><?php echo CHtml::dropDownList('data[deliverymanId]',$val['deliverymanId'],$mems,array('class'=>'form-control input-sm do-save'))?></td>
	 <td><?php echo $val['title'];?><br>
   <?php if( !empty( $val['appointment'] ) ){ ?>
     <strong>预约时间：</strong><?php echo $val['appointment'];?><br>
   <?php }?>
	 <?php if( !empty( $val['remark'] ) ){ ?>
	   <div style="color:#460046"><span class="glyphicon glyphicon-user"></span><strong>留言：</strong><?php echo $val['remark'];?></div>
  </div>
  <?php }?>
	 <?php if( !empty( $val['shopRemark'] ) ){ ?>
		<div style="color:#000">
   <span class="glyphicon glyphicon-star"></span><strong>商家备注：</strong><?php echo $val['shopRemark'];?></div>
	 <?php }?>
	  <?php if( !empty( $val['ops'] ) ){ ?>
		<strong>送货备注：</strong></a><br>
		<?php foreach(  $val['ops'] as  $_op  ) {
			echo $_op['opTime'].'&nbsp;'.$_op['remark'].'<br>';
	  }}
	  ?>
	 </td>
     <td>
	 <?php if( $val['isDel'] == '0' ){ ?>
	 <a href="#" class="shelf" data-toggle="modal" data-target=".shelf-confirm" data-id="<?php echo $val['id'] ?>" data-rel="<?php echo $this->createUrl('del');?>" data-whatever="关闭" title="关闭"><span class="glyphicon glyphicon-remove"></span></a>
	 <?php }else{?>
    <a href="#" class="shelf" data-toggle="modal" data-target=".shelf-confirm" data-id="<?php echo $val['id'] ?>" data-rel="<?php echo $this->createUrl('undel');?>" data-whatever="恢复" title="恢复"><span class="glyphicon glyphicon-repeat"></span></a>
	<?php }?>
	</td>
	</form>
    </tr>
   <?php endforeach;?>
   </tbody>
  </table>
  <div class="clearfix well well-sm list-well">
   <div class="pull-left form-inline">
    <label class="checkbox-inline"> <input type="checkbox" class="checkedall"/> 全选 </label>
    <button class="btn btn-sm btn-default"  data-toggle="modal" data-target=".setarea-confirm" title="批量设置片区" >设置片区</button>
	<button class="btn btn-sm btn-default"  data-toggle="modal" data-target=".setdeliveryman-confirm" title="批量设置送货员" >设置送货员</button>
	<button class="btn btn-sm btn-default setBatch" title="批量设置已送" data-url="<?php echo $this->createUrl('setsend')?>" >批量已送</button>
	<button class="btn btn-sm btn-default setBatch" title="批量关闭" data-url="<?php echo $this->createUrl('setdel')?>"  >批量关闭</button>

	<a class="btn btn-sm btn-default" href="<?php echo $excelUrl ;?>" title="excel导出当前列表">excel导出</a>
   </div>
   <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>

<?php $this->beginContent('//layouts/_shelf');$this->endContent();?>

<div class="modal fade setarea-confirm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="exampleModalLabel">批量设置片区</h4>
      </div>
      <div class="modal-body">
        <form action="<?php echo $this->createUrl('setarea')?>" method="post">
			<input type="hidden" value="" name="ids"/>
			<?php echo CHtml::dropDownList('areaId',$areaId,$areas,array('class'=>'form-control input-sm'))?>
		</form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
        <button type="button" class="btn btn-success">确定</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade setdeliveryman-confirm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="exampleModalLabel">批量设置送货员</h4>
      </div>
      <div class="modal-body">
        <form action="<?php echo $this->createUrl('setdeliveryman')?>" method="post">
			<input type="hidden" value="" name="ids"/>
			<?php echo CHtml::dropDownList('deliverymanId',$deliverymanId,$mems,array('class'=>'form-control input-sm'))?>
		</form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
        <button type="button" class="btn btn-success">确定</button>
      </div>
    </div>
  </div>
</div>
<script>seajs.use('statics/app/gou/js/order.js');</script>