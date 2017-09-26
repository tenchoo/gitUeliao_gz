 <div class="panel panel-default search-panel">
   <div class="panel-body">
   <a class="btn btn-sm btn-default" href="<?php echo $this->createUrl('edittype')?>">添加支付类型</a>
   </div>
  </div>
  <br>
   <table class="table table-condensed table-bordered">
   <colgroup><col><col width="20%"></colgroup>
   <thead>
    <tr>
     <td>支付类型</td>
     <td>操作</td>
    </tr>
   </thead>
   </table>
   <br>
   <table class="table table-condensed table-bordered table-hover">
   <colgroup><col><col width="20%"></colgroup>
   <tbody>
   <?php foreach(  $data as $val  ):?>
	<tr>
     <td><?php echo $val['paymentTitle'];?></td>
	 <td>
		<a href="<?php echo $this->createUrl('edittype',array('paymentId'=>$val['paymentId']))?>">编辑</a>

		<?php if( $val['paymentId'] > 12 ){ ?>
		<a href="#" class="del" data-toggle="modal" data-target=".del-confirm" data-id="<?php echo $val['paymentId'] ?>" data-rel="<?php echo $this->createUrl('deltype');?>">删除</a>
		<?php } ?>
	 </td>
    </tr>
   <?php endforeach;?>
   </tbody>
  </table>
 <?php $this->beginContent('//layouts/_del');$this->endContent();?>