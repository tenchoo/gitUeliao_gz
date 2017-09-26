<link rel="stylesheet" href="/themes/classic/statics/app/order/css/style.css" />
<div class="panel panel-default search-panel">
  <div class="panel-body">
  <form action="<?php echo $this->createUrl('index');?>" class="pull-left form-inline">
	<input type="text" value="<?php echo $account;?>" name="account" class="form-control input-sm" placeholder="手机号"/>
	<button class="btn btn-sm btn-default">查找</button>
	</form>
 </div>
</div>
<table class="table table-condensed table-bordered">
  <colgroup><col width="20%"><col><col width="10%"></colgroup>
  <thead>
    <tr>
      <td width="20%">手机号</td>
	  <td >内容</td>
	   <td width="10%">发送时间</td>
    </tr>
    </thead>
</table>
<br>
<table class="table table-condensed table-bordered table-hover">
<colgroup><col width="20%"><col><col width="10%"></colgroup>
<tbody>
   <?php foreach( $list as $val ):?>
	<tr>
      <td><?php echo $val['account'];?></td>
	  <td><?php echo $val['content'];?></td>
	   <td><?php echo $val['createTime'];?></td>
    </tr>
	<?php endforeach; ?>
	</tbody>
  </table><br/>
<?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>

 <?php $this->beginContent('//layouts/_del');$this->endContent();?>