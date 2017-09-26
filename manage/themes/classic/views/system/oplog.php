
<div class="panel panel-default search-panel">
    <div class="panel-body">
    <form role="search" class="pull-left form-inline" action="<?php echo $this->createUrl('oplog');?>">
     <div class="form-group">
      <div class="inline-block category-select demo2 pull-left">
        <select name="c" class="form-control input-sm cate1">
          <option value="">请选择项目</option>
		  <?php foreach ( $collection as $val ):?>
		   <option value="<?php echo $val;?>" <?php if($c == $val){ echo 'selected';}?>><?php echo $val;?></option>
		  <?php endforeach;?>
        </select>
        <br>
      </div>
     </div>
     <button class="btn btn-sm btn-default">查看日志</button>
    </form>
   </div>
</div>

<table class="table table-condensed table-bordered table-hover">
  <thead>
    <tr>
    <td width="20%">访问时间</td>
	<td>访问路径</td>
	<td width="20%">访问方法</td>
	<td width="20%">用户ID</td>
	<td width="10%">ip</td>
    </tr>
  </thead>
  <tbody>
   <?php foreach( $list as $val ):?>
	<tr>
	 <td><?php echo $val->viewTime;?></td>
      <td><?php echo $val->route;?></td>
	  <td><?php echo $val->method;?></td>
	  <td><?php echo $val->memberId;?></td>
	  <td><?php echo $val->ip;?></td>
    </tr>
	<?php endforeach; ?>
	</tbody>
  </table><br/>
   <div class="clearfix well well-sm list-well">
   <div class="pull-right">
	<?php
     $this->widget('widgetNextpager', array(
        'nextId' => $nextId,
        'preId' => $preId, //当前页的class
        'htmlOptions' => array('class' => 'pagination pagination-sm'),
		'firstPageLabel' => '首页',
        'nextPageLabel' => '下一页',
        'prevPageLabel' => '上一页',
		'hiddenPageCssClass' => 'disabled', //禁用页的class
        )
    );
    ?>
	</div>
  </div>
