 <div class="panel panel-default search-panel">
   <div class="panel-body">
	<form action="<?php echo $this->createUrl('index');?>" class="pull-left form-inline">
	<input type="text" value="<?php echo $keyword;?>" name="keyword" class="form-control input-sm"/>
	<button class="btn btn-sm btn-default">查找</button>
	</form>
  <div class="pull-right">
     <a class="btn btn-sm btn-default" href="<?php echo $this->createUrl('addedit')?>">添加物流</a></div>
   </div>
 </div>
 <div class="clearfix well well-sm list-well"><?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?></div>
  <table class="table table-condensed table-bordered">
   <colgroup><col width="25%"><col width="25%"><col width="25%"><col width="25%"></colgroup>
   <thead>
    <tr>
     <td>物流名称</td>
	 <td>物流标识</td>
	 <td>支付货到付款</td>
     <td width="200px">操作</td>
    </tr>
   </thead>
  </table>
  <br>
  <table class="table table-condensed table-bordered table-hover">
   <colgroup><col width="25%"><col width="25%"><col width="25%"><col width="25%"></colgroup>
   <tbody>
   <?php foreach(  $list as $val  ):?>
	<tr>
     <td><?php echo $val['title'];?></td>
	 <td><?php echo $val['mark'];?></td>
	 <td><?php if( $val['isCOD'] == '1'){ ?> 是<?php }else{ ?> 否<?php } ?></td>
	 <td>
		<a href="<?php echo $this->createUrl('addedit',array('logisticsId'=>$val['logisticsId'],'from'=>urlencode( Yii::app()->request->url)))?>">编辑</a>
		<a href="#" class="del" data-toggle="modal" data-target=".del-confirm" data-id="<?php echo $val['logisticsId'] ?>" data-rel="<?php echo $this->createUrl('del');?>">删除</a>
	 </td>
    </tr>
   <?php endforeach;?>
   </tbody>
  </table>
  <div class="clearfix well well-sm list-well">
   <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?></div>
  <?php $this->beginContent('//layouts/_del');$this->endContent();?>