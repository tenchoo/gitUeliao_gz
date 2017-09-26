<div>当前在线员工:<?php echo $userTotal;?>人，在线会员：<?php echo $memberTotal;?>人</div>
<div class="panel panel-default search-panel">
  <div class="panel-body">
   <div class="pull-right">
   <?php
   $htmlOption=array('class'=>'btn btn-sm btn-default');

   //在线员工
   $options = $htmlOption;
   $url = $this->createUrl('/system/online',['type'=>'user']);
   if(Yii::app()->request->getQuery('type','user') == 'user') {
   	$options['class'] .= ' active';
   }
   echo CHtml::link('在线员工', $url, $options);
   echo "\n";

   //在线员工
   $options = $htmlOption;
   $url = $this->createUrl('/system/online',['type'=>'member']);
   if(Yii::app()->request->getQuery('type') == 'member') {
   	$options['class'] .= ' active';
   }
   echo CHtml::link('在线会员', $url, $options);
   ?>
	</div>
  </div>
</div>

<table class="table table-condensed table-bordered table-hover">
	<thead>
		<tr>
			<td width="20%">用户编号</td>
			<td width="20%">用户名称</td>
			<td width="20%">用户类型</td>
			<td width="20%">超级管理员</td>
			<td>操作</td>
		</tr>
	</thead>
	<tbody>
   <?php
	foreach ( $Datalist as $val ) :
				?>
	<tr>
			<td><?php echo $val['uid'];?></td>
			<td><?php echo $val ['username'];?></td>
			<td><?php echo ($val['type']=='user')? '员工' : '会员';?></td>
			<td><?php echo ($val['isAdmin']==1)? '是' : '否';?></td>
			<td><a href="<?php echo $this->createUrl('kick', ['id'=>$val['key'], 'type'=>Yii::app()->request->getQuery('type','user')]);?>">踢除</a></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
