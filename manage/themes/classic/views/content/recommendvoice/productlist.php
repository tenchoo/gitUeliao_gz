    <div class="panel panel-default search-panel">
   <div class="panel-body">
    <form role="search" class="pull-left form-inline" action="<?php echo $this->createUrl(Yii::app()->getController()->getAction()->id);?>">
	<input type="hidden" name="recommendId" class="form-control input-sm" value="<?php echo $recommendId;?>"/>
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
    <a class="pull-right btn btn-sm btn-primary" href="javascript:void(history.back())"><span class="glyphicon glyphicon-share-alt" style="-moz-transform:scaleX(-1);
    -webkit-transform:scaleX(-1);
    -o-transform:scaleX(-1);
    transform:scaleX(-1);
    filter:FlipH;"></span> 返回</a>
   </div>
  </div>

  <div class="clearfix well well-sm list-well">
   <!-- <div class="pull-left form-inline">
    <label class="checkbox-inline"> <input type="checkbox" class="checkedall"/> 全选 </label>
    <button class="btn btn-sm btn-default btn-export">批量导出</button>
   </div> -->
    <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>
  <table class="table table-condensed table-hover table-bordered">
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
   <table class="table table-condensed table-bordered">
   <colgroup><col width="33"><col width="50"><col width="8%"><col><col width="6%"><col width="6%"><col width="8%"><col width="8%"><col width="10%"><col width="10%"></colgroup>
   <tbody>
   <?php foreach(  $list as $val  ):?>
    <tr>
     <!-- <td><input type="checkbox" value="<?php echo $val['productId'];?>"/></td> -->
	  <td colspan="2"><img src="<?php echo $this->img().$val['mainPic'];?>_50" width="50" height="50"/></td>
	 <td><?php echo $val['serialNumber'];?></td>
     <td><?php echo $val['title'],$val['productId'];?></td>
     <td><?php echo Order::priceFormat($val['price']);?></td>
	  <td><?php echo Order::priceFormat($val['tradePrice']);?></td>
     <td><?php echo Order::quantityFormat($val['total']);?></td>
	 <td><?php echo Order::quantityFormat($val['dealCount']);?></td>
     <td><?php echo $val['updateTime'];?></td>
     <td>
	<?php if( in_array ($val['productId'],$recommendProductIds ) ){ ?>
		已推荐<br/>
		<a href="#" class="shelf" data-toggle="modal" data-target=".shelf-confirm" data-id="<?php echo $val['productId'] ?>" data-rel="<?php echo $this->createUrl('delproduct',array('recommendId'=>$recommendId));?>" data-whatever="取消推荐">取消推荐</a>
	<?php }else { ?>
		<a href="#" class="recommend" data-id="<?php echo $val['productId'] ?>" data-rel="<?php echo $this->createUrl('addproduct',array('recommendId'=>$recommendId));?>" data-whatever="推荐">推荐</a>
	<?php }?>
	</td>
    </tr>
   <?php endforeach;?>
   </tbody>
  </table>
  <div class="clearfix well well-sm list-well">
   <!-- <div class="pull-left form-inline">
    <label class="checkbox-inline"> <input type="checkbox" class="checkedall"/> 全选 </label>
    <button class="btn btn-sm btn-default btn-export">批量导出</button>
   </div> -->
   <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>
 <?php $this->beginContent('//layouts/_shelf');$this->endContent();?>
 <script>
seajs.use('statics/app/content/js/productrecom.js');
</script>