<?php $this->beginContent('//layouts/_error');$this->endContent();?>
<br>
<table class="table table-condensed table-bordered">
  <thead>
    <tr class="list-hd">
      <td colspan="2">
        <span class="pull-right text-right"><?php echo date('Y-m-d H:i:s', $room->lastTime)?></span>
		<span class="first">客户：<?php echo $room->member->nickName;?></span>

	  </td>
    </tr>
  </thead>
  <tbody>
  <tr>
  	<td width="70"><?php echo CHtml::tag('img',['src'=>$this->showImage($room->product->mainPic,50)]); ?></td>
  	<td colspan="3"><?php echo $room->product->title;?><br /><span class="text-muted"><?php echo $room->product->serialNumber;?></span></td>
  </tr>
  </tbody>
</table>
<br />

<?php foreach($dataList as $item):?>
<table class="table table-condensed table-bordered">
	<tbody>
	<tr>
	  <td width="70"><?php if($item->mark!=='member'){echo '<span class="text-success">回复</span>';}else{echo $item->userIcon();}?></td>
	  <td>
	  	<div><?php echo date('Y-m-d H:i:s', $item->createTime);?></div>
	  	<div><?php
	  	switch($item->mime) {
	  		case "voice":
	  			echo $item->showVoice();
	  			break;

	  		case "image":
	  			echo $item->showImage();
	  			break;

	  		default:
	  			 echo  urldecode( $item->content ) ;
	  	}
	  	?></div>
	  </td>
	  <td width="100"><?php if($item->mark!=='member') echo CHtml::link("编辑", $this->createUrl('editor',['id'=>$room->id,'cid'=>$item->id]),['class'=>'edit']);?> <?php //echo CHtml::link('删除',$this->createUrl('delete', ['id'=>$item->id])); ?></td>
    </tr>
	</tbody>
  </table><br/>
  <?php endforeach;?>
<div class="panel panel-default">
  <form method="post" action="<?php echo $this->createUrl('reply');?>" class="form-horizontal">
      <input type="hidden" name="form[id]" value="<?php echo $room->inquiryId;?>">
  	<div class="form-group">
      <label class="control-label col-md-2" for="">回复信息：</label>
      <div class="col-md-4">
        <textarea name="form[content]" class="form-control"></textarea>
      </div>
  	</div>
    <div class="form-group">
      <div class="col-md-offset-2 col-md-10">
        <button class="btn btn-success">提交信息</button>
      </div>
    </div>
  </form>
</div>
<script>
  seajs.use(['statics/app/inquiry/js/detail.js','libs/emoji/1.0.0/emoji.js'],function(){
    $('.content-wrap').emoji();
  });
</script>