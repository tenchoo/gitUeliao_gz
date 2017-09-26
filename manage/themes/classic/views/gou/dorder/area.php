<div class="panel panel-default search-panel">
   <div class="panel-body">
	<a href="<?php echo $this->createUrl('area',array('state'=>'all','c'=>$this->cc));?>"  <?php if( is_null($state ) ){ ?>class="text-danger"<?php } ?>>全部</a>
	<?php foreach ( $states as $key=>$val){
	?>
		/ <a href="<?php echo $this->createUrl('area',array('state'=>$key,'c'=>$this->cc));?>" <?php if( $state === $key ){ ?>class="text-danger"<?php } ?> ><?php echo $val;?></a>
	<?php }?>
	<div class="pull-right">
	 <a href="<?php echo $this->createUrl('addarea',array('c'=>$this->cc ));?>" class="btn btn-default">新增</a>
	  <a href="<?php echo $this->createUrl('index',array('c'=>$this->cc ));?>" class="btn btn-default">订单管理</a>
	  </div>
   </div>
  </div>
  <table class="table table-condensed">
   <colgroup><col width="28%"><col width="20%"><col></colgroup>
   <thead>
    <tr>
     <td>片区</td>
	 <td>订单数</td>
	 <td>商品件数</td>
    </tr>
   </thead>
     <tbody>
   <?php foreach(  $tongji as $val  ):?>
    <tr class="<?php echo $val['class']?>">
	 <td><?php echo $val['title'];?></td>
	 <td><?php echo $val['c'];?></td>
	 <td><?php echo $val['total'];?></td>
    </tr>
   <?php endforeach;?>
   </tbody>
  </table>
   <table class="table table-condensed">
    <colgroup><col width="28%"><col width="20%"><col></colgroup>
     <tbody>
   <?php foreach(  $memCounts as $val  ):?>
    <tr class="<?php echo $val['class']?>">
	 <td><?php echo $val['deliveryman'];?></td>
	 <td><?php echo $val['c'];?></td>
	 <td><?php echo $val['total'];?></td>
     <td></td> <td></td>
    </tr>
   <?php endforeach;?>
   </tbody>
  </table>

  <table class="table table-condensed table-bordered">
  <colgroup><col width="28%"><col width="20%"><col></colgroup>
  <tbody>
   <?php foreach(  $list as $val  ):?>
   <tr class="<?php echo $val['class']?>">
   <form action="<?php echo $this->createUrl('areadeliveryman',array('areaId' =>$val['areaId'],'c'=>$this->cc ));?>" method="post">
	 <td><?php echo $val['title'];?></td>
	 <td><?php echo $val['c'];?></td>
	 <td><?php echo $val['total'];?>
	  <?php if( $state == '0'){ ?>
	<div class="pull-right" style="width:100px;">
	 <?php echo CHtml::dropDownList('deliverymanId',$val['deliverymanId'],$mems,array('class'=>'form-control input-sm do-save','empty'=> $val['title'].'送货员'))?>
	 </div>
	 <?php }?>
	 </td>
	</form>
    </tr>
   <?php endforeach;?>
   </tbody>
  </table>
  <script>seajs.use('statics/app/gou/js/order.js');</script>