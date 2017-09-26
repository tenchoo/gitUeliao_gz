<link rel="stylesheet" href="/themes/classic/statics/app/purchase/default/css/style.css" />
<?php $this->beginContent('//layouts/_error');$this->endContent();?>
<form action="" class="form-horizontal" method="post">

   <br>
   <table class="table table-striped table-condensed table-hover table-bordered">
   <colgroup><col><col width="20%"><col width="20%"><col width="20%"><col width="10%"></colgroup>
   <thead>
    <tr>
	 <td>产品编号</td>
     <td>呆滞级别</td>
     <td>所属仓库</td>
	 <td>呆滞数量</td>
     <td>操作</td>
    </tr>
   </thead>
   </table>
<br>  
   <?php foreach(  $list as $val  ):?>
    <table class="table table-condensed table-bordered">
   <colgroup><col><col width="20%"><col width="20%"><col width="20%"><col width="10%"></colgroup>
   <tbody>
   <?php 
			$n = count($val['nums']);
		foreach ( $val['nums'] as $key=>$_nums ):
   ?>
    <tr>
	<?php if($key=='0'){ ?>
	 <td rowspan="<?php echo $n;?>" ><?php echo $val['singleNumber'];?></td>
     <td rowspan="<?php echo $n;?>" ><?php echo $val['level'];?></td>
	<?php }?>
	 <td><?php echo $warehouse[$_nums['warehouseId']];?></td>
	 <td><?php echo $_nums['totalNum'];?></td>
	<?php if($key=='0'){ ?>
     <td rowspan="<?php echo $n;?>" >
		<a href="###<?php echo $this->createUrl('add',array('singleNumber' =>$val['singleNumber']));?>">删除</a>
	</td>
	<?php }?>
    </tr>
   <?php endforeach;?>
   </tbody>
  </table><br/>
    <?php endforeach;?>
 </form>  