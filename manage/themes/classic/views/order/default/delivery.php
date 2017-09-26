<link rel="stylesheet" href="/themes/classic/statics/app/order/css/style.css" />
<?php $this->beginContent('_orderinfo',array('model' => $model ,'member'=>$member,'payments' => $payments));$this->endContent();?>
<form  method="post" action="">
	<table class="table table-condensed table-bordered order">
   <thead>
    <tr>
     <td>产品信息</td>
	 <td>产品编号</td>
	 <td>颜色</td>
     <td>购买数量</td>
	 <td>分拣数量</td>
	 <?php if( $packingState=='2'){?>
	 <td>已发数量</td>
	 <?php }?>
	 <td>发货数量</td>
	  </tr>
	  </thead>
	  <tbody>
	 <?php foreach( $model->products as $pval) :?>
	  <tr>
     <tr class="order-list-bd">
     <td>
     <div class="c-img pull-left"><a href="javascript:"><img src="<?php echo $this->img(false).$pval['mainPic'];?>" alt="" width="100" height="100"/></a></div>
	 <div class="product-title"><?php echo $pval['title'];?></div>
	 </td>
	 <td><?php echo $pval['serialNumber'];?></td>
	 <td><?php $spec = explode(':',$pval['specifiaction']); echo $spec['1'];?></td>
     <td><?php echo $pval['num'];?> 码</td>
     <td><?php echo $packDetail[$pval['stockId']]?> 码</td>
	 <?php if( $packingState=='2'){?>
	 <td><?php echo $pval['deliveryNum'];?> 码</td>
	 <?php } ?>
	 <td>
	 <?php if( $packDetail[$pval['stockId']]>$pval['deliveryNum'] ){ ?>
	 <input type="text" name="pack[<?php echo $pval['stockId']?>]"/> 码
	 <? }?>


	 </td>
	  </tr>
	<?php endforeach;?>
	 </table><br/>
	 <div class="panel panel-default">
		<ul class="list-group">
	    <li class="list-group-item clearfix">
			<span class="col-md-12">收货地址：<input type="text" name="address" value="<?php echo $model->address;?> ( <?php echo $model->name;?>  收 ) <?php echo $model->tel;?>" size="100"/></span>
		</li>
	    <li class="list-group-item clearfix">
			<span class="col-md-12">选择物流：
				<select name="logistics">
					<option value="">请选择物流公司</option>
					<?php foreach( $logisticsList as $val ){ ?>
					<option value="<?php echo $val['logisticsId']?>"><?php echo $val['title']?></option>
					<?php } ?>
				</select>
			</span>
		</li>
		<li class="list-group-item clearfix">
			<span class="col-md-12">物流编号：<input type="text" name="logisticsNo" value="" size="50"/></span>
		</li>
    </ul>
  </div>
	 <br/>
	 <?php $this->beginContent('//layouts/_error');$this->endContent();?><br/>
	<div align="center">
		<button class="btn btn-success">确认发货</button>
	</div>
 </form>