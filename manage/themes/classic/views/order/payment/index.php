 <div class="panel panel-default search-panel">
   <div class="panel-body">
     <a class="btn btn-sm btn-default" href="<?php echo $this->createUrl('addedit')?>">添加支付方式</a>
   </div>
 </div>
<br>
<table class="table table-condensed table-bordered">
   <colgroup><col width="40%"><col width="20%"><col width="20%"><col width="20%"></colgroup>
   <thead>
    <tr>
     <td>支付名称</td>
	 <td>所属类型</td>
	 <td>状态</td>
     <td width="200px">操作</td>
    </tr>
   </thead>
</table>
<br>
<table class="table table-condensed table-bordered table-hover">
<colgroup><col width="40%"><col width="20%"><col width="20%"><col width="20%"></colgroup>
   <tbody>
   <?php foreach(  $data as $val  ):?>
	<tr>
     <td><?php echo $val['paymentTitle'];?></td>
	 <td><?php echo isset($type[$val['type']])?$type[$val['type']]:'';?></td>
	 <td><?php if( $val['available'] == '1'){ ?> 已启用<?php }else{ ?> 已停用<?php } ?></td>
	 <td>
		<a href="<?php echo $this->createUrl('addedit',array('paymentId'=>$val['paymentId'],'from'=>urlencode( Yii::app()->request->url)))?>">编辑</a>
		<?php if( $val['paymentId'] > 12 ){ ?>
		<a href="#" class="del" data-toggle="modal" data-target=".del-confirm" data-id="<?php echo $val['paymentId'] ?>" data-rel="<?php echo $this->createUrl('deltype');?>">删除</a>
		<?php } ?>
	 </td>
    </tr>
   <?php endforeach;?>
   </tbody>
  </table>
 <?php $this->beginContent('//layouts/_del');$this->endContent();?>