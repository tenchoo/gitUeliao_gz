<!doctype html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="renderer" content="webkit|ie-stand|ie-comp">
    <title>送货员：<?php echo $memberName ;?></title>
    <meta name="keywords" content=""/>
    <meta name="description" content=""/>
    <meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=no">
    <link rel="stylesheet" href="/themes/classic/statics/libs/bootstrap/3.3.5/css/bootstrap.min.css"/>
    <script src="/themes/classic/statics/libs/seajs/2.3.0/sea.js"></script>
    <script src="/themes/classic/statics/libs/seajs/2.3.0/seajs-css.js"></script>
    <script src="/themes/classic/statics/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="/themes/classic/statics/libs/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script src="/themes/classic/statics/common/init.js"></script>
</head>
<body>
<div class="container-fluid">
  <form role="search"  action="<?php echo $url;?>">
   <div class="row">
    <div class="col-xs-6">
    <input type="text" name="keyword" value="<?php echo $keyword;?>" placeholder="搜索内容" class="form-control" />
    </div>
    <div class="col-xs-6">
    <?php echo CHtml::dropDownList('type',$type,$types,array('class'=>'form-control','empty'=>'按啥查'))?>
     </div>
    </div>

	<div class="row" style="padding-top:2px;">
    <div class="col-xs-6">
   <?php echo CHtml::dropDownList('areaId',$areaId,$areas,array('class'=>'form-control','empty'=>'片区'))?>
    </div>
    <div class="col-xs-6">
	<?php echo CHtml::dropDownList('state',$state,$states,array('class'=>'form-control','empty'=>'配送'))?>
     </div>
	 </div>
	 <div class="row" style="padding-top:15px;">
		<div class="col-xs-6">
		总件数：<span class="text-danger">( <?php echo $totalNum;?> )</span><br>
		总订单数：<span class="text-danger">( <?php echo $pages->itemCount;;?> )</span>
		 </div>
		 <div class="col-xs-6">
	  <button class="btn btn-success">查找</button>
	  </div>
    </div>
    </form>
    </div>
	<br>

   <?php
   $varPage = $pages->currentPage*$pages->pageSize;

   $classAttr = array('0'=>'alert-danger','1'=>'alert-success','2'=>'alert-info','3'=>'alert-warning');
   foreach(  $list as  $index=>$val  ):
	 $class = isset($classAttr[$val['state']])?$classAttr[$val['state']]:'';
   ?>
   <div class="alert <?php echo $class;?> ">
    <div class="row">
	<div class="col-xs-6"><strong>序号： <?php echo $index+1+$varPage;?></div>
	</div>
   <div class="row">
	<div class="col-xs-6"><strong>订单号：</strong><?php echo $val['orderId'];?>
  </div>
	<div class="col-xs-6"><strong>片区：</strong><?php echo $val['areaTitle'];?></div>
	</div>
   <div class="row">
  <div class="col-xs-12"><strong>商品标题：</strong><?php echo $val['title'];?>
  </div>
  </div>
  <div class="row">
  <div class="col-xs-6"><strong>预约时间:</strong><?php echo $val['appointment'];?></div>
  <div class="col-xs-6"><strong>发货数量：</strong>  <?php if( $val['num']>1 ){ ?>
   <span class="text-danger" style=" font-size:2em; "><strong><?php echo $val['num'];?></strong></span>
   <?php }else{ ?>
    <?php echo $val['num'];?>
  <?php }?></div>
  </div>
  <div class="row">
  <div class="col-xs-12"><strong>收货人：</strong><?php echo $val['name'];?>(<a href="tel:<?php echo $val['phone'];?>"><?php echo $val['phone'];?></a>)</div>
  </div>
   <?php if( !empty( $val['remark'] ) ){ ?>
	<div class="row">
  <div class="col-xs-12" style="color:#460046"><span class="glyphicon glyphicon-user"></span><strong>留言：</strong><?php echo $val['remark'];?></div>
	</div>
   <?php  } ?>
    <?php if( !empty( $val['shopRemark'] ) ){ ?>
	<div class="row">
	<div class="col-xs-12" style="color:#000"><strong>商家备注：</strong><?php echo $val['shopRemark'];?></div>
	</div>
	 <?php  } ?>
	<div class="row">
	<div class="col-xs-12"><strong>订单地址：</strong><?php echo $val['orderAddress'];?></div>
	</div>
	<form action="<?php echo $url;?>" method="post">
	<div class="row">
	<div class="col-xs-6"><strong>送货地址：</strong></div>
	<div class="col-xs-6"><button class="btn btn-success" type="submit">保存送货地址</button></div>
	<div class="col-xs-12">
		<input type="hidden" name="editId" value="<?php echo $val['id'];?>"/>
		<input type="text" class="form-control " name="deliveryAddress"  name="remark" value="<?php echo $val['deliveryAddress'];?>" maxlength="100"  placeholder="送货地址">
	</div>
	</div>
	</form>
	<form action="<?php echo $url;?>" method="post">
		<input type="hidden" name="editId" value="<?php echo $val['id'];?>"/>
	<div class="row" style="padding-top:2px;">
		<div class="col-xs-6">
			<?php echo CHtml::dropDownList('state',$val['state'],$states,array('class'=>'form-control'))?>
		</div>
      <div class="col-xs-6"> <button class="btn btn-success" type="submit">保存</button></div>
	</div>
	<div class="row" style="padding-top:2px;">
	<div class="col-xs-12" ><input type="text" class="form-control"  name="remark" value="" maxlength="50"  placeholder="送货备注"></div>
	</div>
	</form>
	 <?php if( !empty( $val['ops'] ) ){ ?>
	 <div class="row">
		<div class="col-xs-12 ops" ><strong>送货备注：</strong><?php echo $val['ops'][0]['opTime'].'&nbsp;'.$val['ops'][0]['remark'];?></div>
	</div>
	<div class="ops_all hide">
		<?php
		unset($val['ops'][0]);
		foreach(  $val['ops'] as  $_op  ) {?>
		<div class="row">
		<div class="col-xs-12">&nbsp;&nbsp;<?php echo $_op['opTime'].'&nbsp;'.$_op['remark'];?></div>
		</div>
	 <?php  }?>
	</div>

	  <?php  } ?>
   </div>
   <?php endforeach;?>
    <div class="clearfix well well-sm list-well">
   <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>
</body>
</html>
<script>seajs.use('statics/app/gou/js/d.js');</script>