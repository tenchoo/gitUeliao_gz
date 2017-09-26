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

	 </div>
     </div>
     <button class="btn btn-sm btn-default">查找</button>
    </form>
   </div>
  </div>
    <div class="clearfix well well-sm list-well">
   <div class="pull-left form-inline">
	<a class="btn btn-default" href="<?php echo $excelUrl ;?>">excel导出当前列表</a>
	&nbsp;&nbsp;总件数: <span class="text-danger"> <strong><?php echo $totalNum;?></strong> </span>
	&nbsp;&nbsp;总订单数: <span class="text-danger"> <strong><?php echo $pages->itemCount;?></strong> </span>

   </div>
   <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>
  <table class="table table-condensed table-bordered">
   <colgroup><col width="50px"><col width="50px"><col width="8%"><col width="8%"><col width="10%"><col><col width="8%"><col width="8%"><col width="8%"><col width="8%"><col width="8%"><col><col width="50px"></colgroup>
   <thead>
    <tr>
	 <td>序号</td>
	 <td>ID</td>
     <td>订单号</td>
	 <td>收货人</td>
	 <td>片区</td>
	 <td>收货人地址</td>
	 <td>手机号码</td>
	 <td>数量</td>
	 <td>配送</td>
	 <td>送货员</td>
	 <td>预约时间</td>
	 <td>商品标题</td>
	 <td>操作</td>
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
      <td> <?php echo $index+1+$varPage;?></td>
	  <td> <?php echo  $val['id'];?></td>
	 <td><?php echo $val['orderId'];?></td>
	 <td><?php echo $val['name'];?></td>
	 <td><?php echo $val['areaTitle'];?></td>
	 <td>
		<span title="订单地址：<?php echo $val['orderAddress'];?>"><?php echo $val['deliveryAddress'];?></span>
	 </td>
	 <td><?php echo $val['phone'];?></td>
	 <td><?php if( $val['num']>1 ){ ?><strong><?php echo $val['num'];?></strong><?php }else{ echo $val['num']; }?>	</td>
	 <td><?php echo $states[$val['state']]?></td>
	 <td><?php echo $val['deliverymanTitle'];?></td>
	 <td><?php echo $val['appointment'];?></td>
	 <td><?php echo $val['title'];?><br>
	 <?php if( !empty( $val['remark'] ) ){ ?>
		<strong>客户留言：</strong><?php echo $val['remark'];?><br>
	 <?php }?>
	 <?php if( !empty( $val['shopRemark'] ) ){ ?>
		 <strong>商家备注：</strong><?php echo $val['shopRemark'];?><br>
	 <?php }?>
	  <?php if( !empty( $val['ops'] ) ){ ?>
		<strong>送货备注：</strong></a><br>
		<?php foreach(  $val['ops'] as  $_op  ) {
			echo $_op['opTime'].'&nbsp;'.$_op['remark'].'<br>';
	  }}
	  ?>
	 </td>
	  <td>
		<a href="#" class="del" data-toggle="modal" data-target=".del-confirm" data-id="<?php echo $val['id'] ?>" data-rel="<?php echo $this->createUrl('undel');?>">恢复</a>
	</td>

	</form>
    </tr>
   <?php endforeach;?>
   </tbody>
  </table>
  <div class="clearfix well well-sm list-well">
   <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>
  
  
  <div class="modal fade del-confirm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="exampleModalLabel">确认恢复</h4>
      </div>
      <div class="modal-body">
        <p>您确定要恢复吗？</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
        <button type="button" class="btn btn-success">确定</button>
      </div>
    </div>
  </div>
</div>
<script>seajs.use('statics/common/common.js');</script>
<script>seajs.use('statics/app/gou/js/order.js');</script>

