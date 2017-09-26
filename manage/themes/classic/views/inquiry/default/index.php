<div class="panel panel-default search-panel">
  <div class="panel-body">
  <form class="pull-left form-inline">
	反馈时间：<input type="text" value="" name="f" class="form-control input-sm" />
	到<input type="text" value="" name="id" class="form-control input-sm" />
	<input type="text" value="" name="p" class="form-control input-sm" placeholder="产品编号"/>
	<button class="btn btn-sm btn-default">查找</button>
	</form>
  </div>
</div>

<div class="clearfix well well-sm list-well">
  <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
</div>
<table class="table table-condensed table-bordered">
  <thead>
    <tr>
    	<td width="70">&nbsp;</td>
      <td width="30%">产品信息</td>
	  <td>内容</td>
	  <td width="15%">操作</td>
    </tr>
  </thead>
</table>
<br/>

<?php foreach($dataList as $item):?>
<table class="table table-condensed table-bordered">
  <thead>
    <tr class="list-hd">
      <td colspan="4">
      <span class="pull-right text-right"><?php echo date('Y-m-d H:i:s', $item->lastTime)?></span>
		  <span class="first">客户：<?php echo $item->member->nickName;?></span>

	   </td>
    </tr>
  </thead>
	<tbody>
	<tr>
	  <td width="70"><?php echo CHtml::tag('img',['src'=>$this->showImage($item->product->mainPic,50)]); ?></td>
	  <td width="25%"><?php echo $item->product->title;?><br /><span class="text-muted"><?php echo $item->product->serialNumber;?></span></td>
	  <td><?php echo $item->lastMessage()->content;?></td>
	  <td width="15%">
	  <?php if($item->hasNew) echo CHtml::tag('div',['class'=>'text-danger'],"有新消息");?>
	  <?php echo CHtml::link('回复', $this->createUrl('view', array('id'=>$item->id)));?>
	  <?php echo CHtml::link('查看', $this->createUrl('view', array('id'=>$item->id)));?>
</td>
    </tr>
	</tbody>
  </table><br/>
  <?php endforeach;?>

<div class="clearfix well well-sm list-well">
  <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
</div>
<script>
  seajs.use('libs/emoji/1.0.0/emoji.js',function(){
    $('.content-wrap').emoji();
  });
</script>
