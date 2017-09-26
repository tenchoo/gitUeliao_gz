 <div class="panel panel-default search-panel">
   <div class="panel-body">
    <form role="search" class="pull-left form-inline" action="<?php echo $this->createUrl('addtail');?>">
     <div class="form-group">
      <div class="inline-block category-select demo2 pull-left">
		<input type="text" name="singleNumber" value="<?php echo $singleNumber;?>" placeholder="请输入产品编号" class="form-control input-sm" />
      </div>
     </div>
     <button class="btn btn-sm btn-default">查找</button>
    </form>
  </div>
 </div>
<div id="list">
 <div class="clearfix well well-sm list-well">
   <div class="pull-left form-inline">
    <label class="checkbox-inline"> <input type="checkbox" class="checkedall"/> 全选 </label>
    <form class="hide" action="<?php echo $this->createUrl('changetail')?>" method="post"></form>
	<button type="button" class="btn btn-sm btn-default" v-on:click="submit" >转成尾货销售<strong class="text-danger" v-text="select.length">0</strong></button>
   </div>
    <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>
  <table class="table table-striped table-condensed table-hover table-bordered">
   <colgroup><col width="20"><col><col width="20%"><col width="20%"><col width="10%"></colgroup>
   <thead>
    <tr>
	 <td></td>
	 <td>产品编号</td>
     <td>所属仓库</td>
	 <td>仓存量</td>
     <td>操作</td>
    </tr>
   </thead>
   </table>
 <br>
<table class="table table-condensed table-bordered">
  <colgroup><col width="20"><col><col width="20%"><col width="20%"><col width="10%"></colgroup>
   <tbody>
<?php
  $line = new MagicTableRow('id','singleNumber','warehouseId','num','option');
  $line->filterMerge('warehouseId');
  $line->filterMerge('num');


 foreach(  $list as $val  ):
  $box ='<input value="'.$val['singleNumber'].'" type="checkbox" v-model="select">';
		$line->appendRow(
			$box,
			$val['singleNumber'],
			$warehouse[$val['warehouseId']],
			Order::quantityFormat( $val['num'] ),
			CHtml::link('转成尾货', $this->createUrl('changetail',array('singleNumber' =>$val['singleNumber'])))
    	);
  endforeach;
    $line->show();
  ?>
    </tbody>
  </table>
   <div class="clearfix well well-sm list-well">
    <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>
  </div>
<script>seajs.use('statics/app/tailgoods/js/glassy.js');</script>