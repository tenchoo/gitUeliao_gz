

<div class="container-fluid">
  <form role="search"  action="<?php echo $url;?>">
   <div class="row">
    <div class="col-xs-6">
    <input type="text" name="keyword" value="<?php echo $keyword;?>" placeholder="搜索内容" class="form-control" />
    </div>
    <div class="col-xs-6">
    <?php echo CHtml::dropDownList('type',$type,$types,array('class'=>'form-control',' empty'=>'按啥查'))?>
     </div>
    </div>

	<div class="row" style="padding-top:2px;">
    <div class="col-xs-6">
   <?php echo CHtml::dropDownList('areaId',$areaId,$areas,array('class'=>'form-control','empty'=>'片区'))?>
    </div>
    <div class="col-xs-6">
	<?php echo CHtml::dropDownList('state',$state,$states,array('class'=>'form-control','empty'=>'配送'))?>
     </div>
	 </div>
	 <div class="row" style="padding-top:2px;">
    <div class="col-xs-6">
   <?php echo CHtml::dropDownList('deliverymanId',$deliverymanId,$mems,array('class'=>'form-control','empty'=>'送货员'))?>
    </div>
	 <div class="col-xs-6">  <button class="btn btn-success">查找</button>
	 <div class="pull-right">
	 <a href="<?php echo $this->createUrl('area',array('c'=>$this->cc ));?>">片区管理</a>
	  </div>
	  </div>
	 </div>
	 <div class="row" style="padding-top:15px;">
		<div class="col-xs-6">
		总件数：<span class="text-danger">( <?php echo $totalNum;?> )</span>
		 </div>
		 <div class="col-xs-6">
	总订单数：<span class="text-danger">( <?php echo $pages->itemCount;;?> )</span>
	  </div>
    </div>
    </form>
    </div>
	<br>
 <?php 
 //去除不需要的选择项
 unset( $mems['-1'],$areas['-1'] );
 ?>
<div class="clearfix well well-sm list-well">
   <div class="pull-left form-inline">
    <label class="checkbox-inline"> <input type="checkbox" class="checkedall"/> 全选 </label>
    <button class="btn btn-sm btn-default"  data-toggle="modal" data-target=".setarea-confirm" >批量设置片区</button>
	<button class="btn btn-sm btn-default"  data-toggle="modal" data-target=".setdeliveryman-confirm" >批量设置送货员</button>
   </div>
</div>

 <?php
   $varPage = $pages->currentPage*$pages->pageSize;

   $classAttr = array('0'=>'alert-danger','1'=>'alert-success','2'=>'alert-info','3'=>'alert-warning');
   foreach(  $list as  $index=>$val  ):
	 $class = isset($classAttr[$val['state']])?$classAttr[$val['state']]:'';
  if( !empty ( $val['orderId'] ) ){
    $val['orderId'] = '*'.substr($val['orderId'], -4);
  }
     $val['title'] = mb_substr($val['title'], 0,8).'*';
   ?>
  <table class="table table-condensed table-bordered <?php echo $class;?>">
   <colgroup><col width="50%"><col width="50%"></colgroup>
  <tbody>
    <tr>
      <td> <strong>序号：  <?php echo $index+1+$varPage;?> &nbsp;&nbsp;<input type="checkbox" value="<?php echo $val['id'];?>" name="memberId[]"/>
	  </td><td><strong>订单号：</strong><?php echo $val['orderId'];?></td>
	</tr>
	 <tr>
	 <form action="<?php echo $this->createUrl('edit',array('id' =>$val['id'],'c'=>$this->cc ));?>" method="post">
	<td colspan="2">
	 <div class="row">
	  <div class="col-xs-6">
	  <strong>片区：</strong>
	<?php echo CHtml::dropDownList('data[areaId]',$val['areaId'],$areas,array('class'=>'form-control do-save'))?>
	  </div>
	   <div class="col-xs-6">
	 <strong>送货员：</strong><?php echo CHtml::dropDownList('data[deliverymanId]',$val['deliverymanId'],$mems,array('class'=>'form-control do-save'))?>
	  </div>
   </div>
   </td>
   </form>
	</tr>
	 <tr>
	  <td  colspan="2">
	    <div class="row">
  <div class="col-xs-12"><strong>商品标题：</strong><?php echo $val['title'];?>
  </div>
  </div>
  <div class="row">
  <div class="col-xs-6"><strong>预约时间:</strong><?php echo $val['appointment'];?></div>
  <div class="col-xs-6"><strong>发货数量：</strong>  <?php if( $val['num']>1 ){ ?>
   <span class="text-danger" style=" font-size:2em; "><strong><?php echo $val['num'];?></strong></span>
   <?php }else{ ?>
    <?php echo $val['num'];?>
  <?php }?></div>
  </div>
  <div class="row">
  <div class="col-xs-12"><strong>收货人：</strong><?php echo $val['name'];?>(<a href="tel:<?php echo $val['phone'];?>"><?php echo $val['phone'];?></a>)</div>
  </div>
<?php if( !empty( $val['remark'] ) ){ ?>
	<div class="row">
	<div class="col-xs-12" style="color:#460046"><span class="glyphicon glyphicon-user"></span><strong>留言：</strong><?php echo $val['remark'];?></div>
	</div>
    <?php  } ?>
    <?php if( !empty( $val['shopRemark'] ) ){ ?>
	<div class="row">
	<div class="col-xs-12" style="color:#000"><span class="glyphicon glyphicon-star"></span><strong>商家备注：</strong><?php echo $val['shopRemark'];?></div>
	</div>
	 <?php  } ?>
	<div class="row">
	<div class="col-xs-12"><strong>订单地址：</strong><?php echo $val['orderAddress'];?></div>
	</div>
	<form action="<?php echo $url;?>" method="post">
	<div class="row">
	<div class="col-xs-6"><strong>送货地址：</strong></div>
	<div class="col-xs-6"><button class="btn btn-success" type="submit">保存送货地址</button></div>
	<div class="col-xs-12">
		<input type="hidden" name="editId" value="<?php echo $val['id'];?>"/>
		<input type="text" class="form-control " name="deliveryAddress"  name="remark" value="<?php echo $val['deliveryAddress'];?>" maxlength="100"  placeholder="送货地址">
	</div>
	</div>
	</form>
	<form action="<?php echo $url;?>" method="post">
		<input type="hidden" name="editId" value="<?php echo $val['id'];?>"/>
	<div class="row" style="padding-top:2px;">
		<div class="col-xs-6">
			<?php echo CHtml::dropDownList('state',$val['state'],$states,array('class'=>'form-control'))?>
		</div>
      <div class="col-xs-6"> <button class="btn btn-success" type="submit">保存</button></div>
	</div>
	<div class="row" style="padding-top:2px;">
	<div class="col-xs-12" ><input type="text" class="form-control"  name="remark" value="" maxlength="50"  placeholder="送货备注"></div>
	</div>
	</form>
	 <?php if( !empty( $val['ops'] ) ){ ?>
	 <div class="row">
		<div class="col-xs-12 ops" ><strong>送货备注：</strong><?php echo $val['ops'][0]['opTime'].'&nbsp;'.$val['ops'][0]['remark'];?></div>
	</div>
	<div class="ops_all hide">
		<?php
		unset($val['ops'][0]);
		foreach(  $val['ops'] as  $_op  ) {?>
		<div class="row">
		<div class="col-xs-12">&nbsp;&nbsp;<?php echo $_op['opTime'].'&nbsp;'.$_op['remark'];?></div>
		</div>
	 <?php  }?>
	</div>
	  <?php  } ?>
	  </td>
	 </tr>
    </tbody>
  </table>
   <?php endforeach;?>

    <div class="clearfix well well-sm list-well">
   <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>
</body>
</html>
<div class="modal fade setarea-confirm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="exampleModalLabel">批量设置片区</h4>
      </div>
      <div class="modal-body">
        <form action="<?php echo $this->createUrl('setarea',array('c'=>$this->cc))?>" method="post">
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
        <form action="<?php echo $this->createUrl('setdeliveryman',array('c'=>$this->cc))?>" method="post">
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
<div class="modal fade del-confirm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="exampleModalLabel">确认关闭</h4>
      </div>
      <div class="modal-body">
        <p>您确定要关闭吗？</p>
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
<script>seajs.use('statics/app/gou/js/d.js');</script>