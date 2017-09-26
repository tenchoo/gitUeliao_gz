    <div class="panel panel-default search-panel">
   <div class="panel-body">
    <form role="search" class="pull-left form-inline" action="<?php echo $this->createUrl(Yii::app()->getController()->getAction()->id);?>">
     <div class="form-group">
      <div class="inline-block category-select demo2 pull-left">
        <select name="" class="form-control input-sm cate1">
          <option value="default">请选择</option>
        </select>
        <select name="" class="form-control input-sm cate2">
          <option value="default">请先选择一级类目</option>
        </select>
        <select name="" class="form-control input-sm cate3">
          <option value="default">请先选择二级类目</option>
        </select>
        <br>
        <input type="hidden" name="category" class="form-control input-sm" />
      </div>
      <div class="inline-block pull-left"><input type="text" name="serialNumber" value="<?php echo $serialNumber;?>" placeholder="请输入产品编号" class="form-control input-sm" /></div>
     </div>
     <button class="btn btn-sm btn-default">查找</button>
    </form>
   </div>
  </div>

  <div class="clearfix well well-sm list-well">
   <div class="pull-left form-inline">
    <label class="checkbox-inline"> <input type="checkbox" class="checkedall"/> 全选 </label>
    <!-- <button class="btn btn-sm btn-default btn-export">批量导出</button> -->
   </div>
    <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>
  <table class="table table-striped table-condensed table-hover">
   <colgroup><col width="85"><col width="8%"><col><col width="6%"><col width="6%"><col width="8%"><col width="8%"><col width="10%"><col width="10%"></colgroup>
   <thead>
    <tr>
    <td width="85" rowspan="2"></td>
	 <td>产品编号</td>
     <td>产品标题</td>
     <td>散剪价</td>
	  <td>大货价</td>
     <td>总库存</td>
	 <td>总销量</td>
     <td>更新时间</td>
     <td>操作</td>
    </tr>
   </thead>
   </table>
   <br>
   <table class="table table-condensed table-bordered table-bordered table-hover">
   <colgroup><col width="20"><col width="50"><col width="8%"><col><col width="6%"><col width="6%"><col width="8%"><col width="8%"><col width="10%"><col width="10%"></colgroup>
   <tbody>
   <?php foreach(  $list as $val  ):?>
    <tr>
     <td><input type="checkbox" value="<?php echo $val['productId'];?>"/></td>
	  <td><img src="<?php echo $this->img().$val['mainPic'];?>_50" width="50" height="50"/></td>
	 <td><?php echo $val['serialNumber'];?></td>
     <td><?php echo $val['title'];?></td>
     <td><?php echo Order::priceFormat($val['price']);?></td>
	  <td><?php echo Order::priceFormat($val['tradePrice']);?></td>
     <td><?php echo Order::quantityFormat($val['total']);?></td>
	 <td><?php echo Order::quantityFormat($val['dealCount']);?></td>
     <td><?php echo $val['updateTime'];?></td>
     <td class="h3">
	<?php if( $op == 'setprice' ){ ?>
		<a href="<?php echo $this->createUrl('/product/publish/list',array('id' => $val['productId']));?>" title="编辑产品"><span class="glyphicon glyphicon-edit"></span></a>
<?php if( $this->checkAccess( '/product/publish/saleinfo' ) ){ ?>
	<a href="<?php echo $this->createUrl('/product/publish/saleinfo',array('step'=>'craft','id' => $val['productId']));?>"  title="价格设置"><span class="glyphicon glyphicon-cog"></span></a>
<?php }?>
<?php if( $this->checkAccess( '/product/publish/packarea' ) ){ ?>
	<a href="<?php echo $this->createUrl('/product/publish/packarea',array('id' => $val['productId']));?>" title="设置默认分拣区域"><span class="glyphicon glyphicon-log-in"></span></a>
<?php }?>
	<?php if( $val['type']=='0' && $this->checkAccess( '/product/publish/index' ) ){ ?>
	<a href="<?php echo $this->createUrl('/product/publish/index',array('step'=>'craft','id' => $val['productId']));?>" title="添加工艺产品"><span class="glyphicon glyphicon-plus-sign"></span></a>
<?php }?>
	<?php }else{ ?>
	<?php if($val['state'] =='0'){ ?>
		<a href="#" class="shelf" data-toggle="modal" data-target=".shelf-confirm" data-id="<?php echo $val['productId'] ?>" data-rel="<?php echo $this->createUrl('offshelf');?>" data-whatever="下架"><span class="glyphicon glyphicon-arrow-down"></span>下架</a>
	<?php }else if($val['state'] =='1'){ ?>
		<?php if( $val['price']>0 ){?>
		<a href="#" class="shelf" data-toggle="modal" data-target=".shelf-confirm" data-id="<?php echo $val['productId'] ?>" data-rel="<?php echo $this->createUrl('onshelf');?>" data-whatever="上架" title="上架"><span class="glyphicon glyphicon-arrow-up"></span>上架</a>
		<?php }?>
		<a href="#" class="del" data-toggle="modal" data-target=".del-confirm" data-id="<?php echo $val['productId'] ?>" data-rel="<?php echo $this->createUrl('del');?>" title="删除"><span class="glyphicon glyphicon-trash"></span></a>
	<?php }else{ ?>
		<a href="<?php echo $this->createUrl('/product/default/offshelf',array('id' =>$val['productId']));?>"><span class="glyphicon glyphicon-repeat"></span>回仓库</a>
		<!--a href="#" class="del" data-toggle="modal" data-target=".del-confirm" data-id="<?php //echo $val['productId'] ?>" data-rel="<?php //echo $this->createUrl('recycledel');?>">彻底删除</a-->
		<?php }?>
	<?php }?>
	</td>
    </tr>
   <?php endforeach;?>
   </tbody>
  </table>
  <div class="clearfix well well-sm list-well">
   <div class="pull-left form-inline">
    <label class="checkbox-inline"> <input type="checkbox" class="checkedall"/> 全选 </label>
    <!-- <button class="btn btn-sm btn-default btn-export">批量导出</button> -->
   </div>
   <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>
 <?php $this->beginContent('//layouts/_del');$this->endContent();?>
 <?php $this->beginContent('//layouts/_shelf');$this->endContent();?>
 <script>
seajs.use('statics/app/product/default/js/index.js');
</script>