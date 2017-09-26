 <div class="panel panel-default search-panel">
   <div class="panel-body">
    <form role="search" class="pull-left form-inline" action="<?php echo $this->createUrl('index');?>">
     <div class="form-group">
      <div class="inline-block category-select demo2 pull-left">
		<input type="text" name="singleNumber" value="<?php echo $singleNumber;?>" placeholder="请输入产品编号" class="form-control input-sm" />
      </div>
     </div>
     <button class="btn btn-sm btn-default">查找</button>
    </form>
	<div class="pull-right">
	<?php if( $isAdd ){ ?>
	<a class="btn btn-sm btn-primary" href="<?php echo $this->createUrl('addtail');?>">添加尾货</a>
	<?php }?>
	</div>
  </div>
 </div>

 <div class="clearfix well well-sm list-well">
    <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>
  <table class="table table-striped table-condensed table-hover table-bordered">
   <colgroup><col><col width="20%"><col width="20%"><col width="20%"><col width="10%"><col width="10%"></colgroup>
   <thead>
    <tr>
	 <td>产品编号</td>
     <td>所属仓库</td>
	 <td>仓存量</td>
	 <td>销售方式</td>
	 <td>价格</td>
     <td>操作</td>
    </tr>
   </thead>
   </table>
<?php
 foreach(  $list as $val  ):
	$buttons['edit'] = CHtml::link('编辑', $this->createUrl('edit',array('id' =>$val['tailId'])));
	$buttons['offshelf'] = '<a href="#" class="shelf"  data-toggle="modal" data-target=".shelf-confirm" data-id="'.$val['tailId'].'" data-rel="'.$this->createUrl('offshelf').'" data-whatever="下架">下架</a>';
	$buttons['onshelf'] = '<a href="#" class="shelf"  data-toggle="modal" data-target=".shelf-confirm" data-id="'.$val['tailId'].'" data-rel="'.$this->createUrl('onshelf').'" data-whatever="上架">上架</a>';
	$buttons['del'] = '<a href="#" class="del" data-toggle="modal" data-target=".del-confirm" data-id="'.$val['tailId'].'" data-rel="'.$this->createUrl('del').'">删除</a>';
	$buttons['recycledel'] = '<a href="#" class="del" data-toggle="modal" data-target=".del-confirm" data-id="'.$val['tailId'].'" data-rel="'.$this->createUrl('recycledel').'">彻底删除</a>';



	$buttonHtml = '';
	foreach ( $val['buttons'] as $_b ){
		if( array_key_exists( $_b,$buttons ) ){
			$buttonHtml .= $buttons[$_b].'&nbsp;&nbsp;';
		}
	}
   ?><br>
	<table class="table table-condensed table-bordered">
   <colgroup><col><col width="20%"><col width="20%"><col width="20%"><col width="10%"><col width="10%"></colgroup>
   <tbody>
    <?php
  $line = new MagicTableRow('singleNumber','warehouseId','num','saleType','price','option');
  $line->filterMerge('warehouseId');
  $line->filterMerge('num');

  foreach( $val['single'] as $_single ):
	if(empty($_single['stock'])){
		$line->appendRow(
    		$_single['singleNumber'],
    		'无',
    		'无',
			$val['saleType'],
			Order::priceFormat( $val['price'] ),
    		$buttonHtml
    		);
	}else{
		foreach ( $_single['stock'] as $_stock ){
			$line->appendRow(
				$_single['singleNumber'],
				$warehouse[$_stock['warehouseId']],
				Order::quantityFormat( $_stock['num'] ),
				$val['saleType'],
				Order::priceFormat( $val['price'] ),
				$buttonHtml
    		);

		}
	}
    endforeach;
    $line->show();
  ?>
    </tbody>
  </table>
   <?php endforeach;?>
   <div class="clearfix well well-sm list-well">
    <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>
   <?php $this->beginContent('//layouts/_del');$this->endContent();?>
 <?php $this->beginContent('//layouts/_shelf');$this->endContent();?>