    <div class="panel panel-default search-panel">
   <div class="panel-body">
	<a href="<?php echo $this->createUrl('index',array('state'=>'all'));?>"  <?php if( is_null($state ) ){ ?>class="text-danger"<?php } ?>>全部</a>
	<?php foreach ( $states as $key=>$val){
	?>
		/ <a href="<?php echo $this->createUrl('index',array('state'=>$key));?>" <?php if( $state === $key ){ ?>class="text-danger"<?php } ?> ><?php echo $val;?></a>
	<?php }?>

	<div class="pull-right">
      <a class="btn btn-default" href="<?php echo $this->createUrl( 'add' );?>">新增</a>
    </div>
   </div>
  </div>

   <table class="table table-condensed">
    <colgroup><col width="30%"><col width="30%"><col></colgroup>
   <thead>
    <tr>
     <td>片区</td>
	 <td>订单数</td>
	 <td>商品件数</td>
	  <td></td>
	  <td></td>
    </tr>
   </thead>
     <tbody>
   <?php foreach(  $tongji as $val  ):?>
    <tr class="<?php echo $val['class']?>">
	 <td><?php echo $val['title'];?></td>
	 <td><?php echo $val['c'];?></td>
	 <td><?php echo $val['total'];?></td>
     <td></td> <td></td>
    </tr>
   <?php endforeach;?>
   </tbody>
  </table>
   <br>
   
   <table class="table table-condensed">
   <colgroup><col width="30%"><col width="30%"><col></colgroup>
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
   <br>
  <table class="table table-condensed table-bordered table-hover">
   <colgroup><col width="30%"><col width="30%"><col width="20%"><col></colgroup>
  <tbody>
   <?php foreach(  $list as $val  ):?>
    <tr class="<?php echo $val['class']?>">
	<form action="<?php echo $this->createUrl('setdeliveryman',array('areaId' =>$val['areaId'] ));?>" method="post">
	 <td><?php echo $val['title'];?></td>
	 <td><?php echo $val['c'];?></td>
	 <td><?php echo $val['total'];?></td>
	 <td>
	 <?php if( $state == '0'){ ?>
	<div class="col-xs-8">
	 <?php echo CHtml::dropDownList('deliverymanId',$val['deliverymanId'],$mems,array('class'=>'form-control input-sm do-save','empty'=>'送货员'))?>
	 </div>
	 <?php }?>
	 <div class="pull-right">
		<a href="<?php echo $this->createUrl('edit',array('id' =>$val['areaId']));?>">编辑</a>
		
		<a href="#" class="del" data-toggle="modal" data-target=".del-confirm" data-id="<?php echo $val['areaId'] ?>" data-rel="<?php echo $this->createUrl('del');?>">删除</a>
	 </div>
	</td>
	</form>
    </tr>
   <?php endforeach;?>
   </tbody>
  </table>
 <?php $this->beginContent('//layouts/_del');$this->endContent();?>
 <script>seajs.use('statics/app/gou/js/order.js');</script>