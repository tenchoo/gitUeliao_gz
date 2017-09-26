<form action="" class="form-horizontal" method="post" id="changetail" v-cloak>
<?php $this->beginContent('//layouts/_error');$this->endContent();?>
 <h3 class="h4">价格设置</h3>
  <br>
  <div class="form-group">
	<label class="control-label col-md-2"><span class="text-danger">*</span>促销类型：</label>
    <div class="col-md-4 ">
	 <label class="radio-inline"><input type="radio" name="tail[saleType]" v-model="saleType" value="retail" <?php if( $tail['saleType'] != 'whole' ) { ?>checked <?php } ?>>降价促销</label>
      <label class="radio-inline"><input type="radio" name="tail[saleType]" value="whole" v-model="saleType" <?php if( $tail['saleType'] == 'whole' ) { ?>checked <?php } ?>>整批销售</label>
    </div>
  </div>
  <template v-if="saleType == 'whole'">
  <div class="form-group">
    <label class="control-label col-md-2"><span class="text-danger">*</span>单价：</label>
    <div class="clearfix col-md-4">
      <div class="input-group">
        <input type="text" class="pull-left form-control input-sm price-only" name="tail[price]" v-model="oneprice | currencyDisplay" value="{{oneprice || '<?php echo $tail['price'];?>'}}">
        <div class="input-group-addon">元</div>
      </div>
    </div>
  </div>
  </template>
  <template v-if="saleType == 'retail'">
  <div class="form-group">
    <label class="control-label col-md-2"><span class="text-danger">*</span>零售价：</label>
    <div class="clearfix col-md-4">
      <div class="input-group">
        <input type="text" class="pull-left form-control input-sm price-only" name="tail[price]" v-model="price | currencyDisplay" value="{{price || '<?php echo $tail['price'];?>'}}">
        <div class="input-group-addon">元</div>
      </div>
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2"><span class="text-danger">*</span>大货价：</label>
    <div class="col-md-4">
      <div class="input-group">
        <input type="text" class="form-control input-sm price-only" name="tail[tradePrice]" v-model="trade | currencyDisplay" value="{{trade || '<?php echo $tail['tradePrice'];?>'}}">
        <div class="input-group-addon">元</div>
      </div>
    </div>
  </div>
  </template>
  <br/>
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
   <table class="table table-condensed table-bordered">
   <colgroup><col><col width="20%"><col width="20%"><col width="20%"><col width="10%"></colgroup>
   <tbody>
<?php
	$c = count($list);
	foreach(  $list as $val  ):
		$n = count($val['stock']);
		foreach ( $val['stock'] as $key=>$_stock ):
?>
    <tr class="tr<?php echo $val['singleNumber'];?>">
	<?php if($key=='0'){ ?>
	 <td rowspan="<?php echo $n;?>" >
		<input type="hidden" name="singleNumber[]" value="<?php echo $val['singleNumber'];?>" />
		<?php echo $val['singleNumber'];?>
	</td>
     <td rowspan="<?php echo $n;?>" >
	 <?php if( array_key_exists($val['level'],$glevel ) ){ ?>
	 <?php echo $glevel[$val['level']]['title'];?>
		<?php if ( !empty ( $glevel[$val['level']]['logo'] ) ){?>
			<img src="<?php echo $this->img().$glevel[$val['level']]['logo'];?>" alt="" height="20">
		<?php }?>
	 <?php }?>
	 </td>
	<?php }?>
	 <td><?php echo $warehouse[$_stock['warehouseId']];?></td>
	 <td><?php echo $_stock['totalNum'];?></td>
	<?php if($key=='0'){ ?>
     <td rowspan="<?php echo $n;?>" >
	 <?php if($c >1){?>
		<a href="#" v-on:click.stop.prevent="del">删除</a>
	 <?php }?>
	</td>
	<?php }?>
    </tr>
   <?php endforeach;?>
    <?php endforeach;?>
   </tbody>
  </table>
  <br>
  <div class="form-group">
    <div class="col-md-offset-2">
      <button class="btn btn-success" type="submit">转成尾货</button>
    </div>
  </div>
 </form>
 <script>seajs.use('statics/app/tailgoods/js/changetail.js');</script>