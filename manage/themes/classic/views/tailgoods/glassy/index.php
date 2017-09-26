
 <div class="panel panel-default search-panel">
   <div class="panel-body">
    <form role="search" class="pull-left form-inline" action="<?php echo $this->createUrl('index');?>">
     <div class="form-group">
      <div class="inline-block category-select demo2 pull-left">
		<input type="text" name="singleNumber" value="<?php echo $singleNumber;?>" placeholder="请输入产品编号" class="form-control input-sm" />
        <select name="level" class="form-control input-sm">
			<option value="">请呆滞级别选择</option>
		<?php foreach ( $glevel as $val ):?>
          <option value="<?php echo $val['id']?>" <?php if( $val['id'] == $level ){ echo 'SELECTED';} ?> ><?php echo $val['title']?></option>
		<?php endforeach;?>
        </select>
		<select name="warehouseId" class="form-control input-sm">
			<option value="">请所属仓库</option>
		<?php foreach ( $warehouse as $k=>$val ):?>
          <option value="<?php echo $k;?>" <?php if( $k == $warehouseId ){ echo 'SELECTED';} ?> ><?php echo $val;?></option>
		<?php endforeach;?>
        </select>
		<select name="state" class="form-control input-sm">
			<option value="all">全部</option>
			<option value="0" <?php if( $state == '0' ){ echo 'SELECTED';} ?>>未转成尾货</option>
			<option value="1" <?php if( $state == '1' ){ echo 'SELECTED';} ?>>已转成尾货</option>
        </select>
      </div>
     </div>
     <button class="btn btn-sm btn-default">查找</button>
    </form>
   <div class="pull-right"> <span> 当前报表生成时间：<?php echo $dt;?></span>
    <a class="btn btn-sm btn-default" href="<?php echo $this->createUrl('index',array('flush'=>'glassy'));?>">刷新报表数据</a>
	</div>
  </div>
 </div>
<div id="list">
 <div class="clearfix well well-sm list-well">
   <div class="pull-left form-inline">
    <label class="checkbox-inline"> <input type="checkbox" class="checkedall"/> 全选 </label>
    <form class="hide" action="<?php echo $this->createUrl('changetail')?>" method="post"></form>
	<button type="button" class="btn btn-sm btn-default" v-on:click="submit">转成尾货销售<strong class="text-danger" v-text="select.length">0</strong></button>
   </div>
    <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>
  <table class="table table-striped table-condensed table-hover table-bordered">
   <colgroup><col width="20"><col><col width="20%"><col width="20%"><col width="20%"><col width="10%"><col width="10%"></colgroup>
   <thead>
    <tr>
	 <td></td>
	 <td>产品编号</td>
     <td>呆滞级别</td>
     <td>所属仓库</td>
	 <td>呆滞数量</td>
	 <td>已生成尾货销售</td>
     <td>操作</td>
    </tr>
   </thead>
   </table>

<?php foreach(  $list as $val  ):?><br>
   <table class="table table-condensed table-bordered">
   <colgroup><col width="20"><col><col width="20%"><col width="20%"><col width="20%"><col width="10%"><col width="10%"></colgroup>
   <tbody>
<?php
  $line = new MagicTableRow('id','singleNumber','level','warehouseId','num','isTail','option');
  $line->filterMerge('warehouseId');
  $line->filterMerge('num');

  $state = ($val['state']=='1')?'是':'否';
  $level = '';
  if( array_key_exists($val['level'],$glevel ) ){
	$level = $glevel[$val['level']]['title'];
	if ( !empty ( $glevel[$val['level']]['logo'] ) ){
		$level .= CHtml::image( $this->img(false).$glevel[$val['level']]['logo'],'',array('height'=>'20'));
	}
  }

  $box ='<input value="'.$val['singleNumber'].'" type="checkbox" v-model="select">';

 foreach ( $val['stock'] as $_stock ):
		$line->appendRow(
			$box,
			$val['singleNumber'],
			$level,
			$warehouse[$_stock['warehouseId']],
			Order::quantityFormat( $_stock['totalNum'] ),
			$state,
			CHtml::link('转成尾货', $this->createUrl('changetail',array('singleNumber' =>$val['singleNumber'])))
    	);
  endforeach;
   $line->show();
  ?>
 </tbody>
  </table>

<?php endforeach;?>

 <div class="clearfix well well-sm list-well">
  <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
</div>
</div>
<script>seajs.use('statics/app/tailgoods/js/glassy.js');</script>