<link rel="stylesheet" href="/themes/classic/statics/app/product/category/css/style.css">
<div class="clearfix well well-sm list-well">
  <div class="pull-left form-inline">
  <?php
  if( $this->checkAccess('/warehouse/warehouse/add') ){
  ?>
    <button class="btn btn-sm btn-default" data-templateid="cate1Item">添加仓库</button>
  <?php } ?>
  </div>
</div>
<ul class="list-unstyled form-inline category-list">
<?php foreach ( $list as $item ): ?>
  <li class="clearfix">
    <div class="clearfix">
      <div class="pull-left name">
        <form action="<?php echo $this->createUrl('edit');?>" method="post">
        <a href="javascript:" class="glyphicon glyphicon-plus"></a>
        <input type="text" name="form[title]" class="form-control input-sm" value="<?php echo $item['title'];?>">
        <input type="hidden" name="form[warehouseId]" value="<?php echo $item['warehouseId'];?>">
        <input type="hidden" name="form[parentId]" value="0">
        </form>
      </div>
      <div class="pull-left control">
		<!--  <a href="<?php //echo $this->createUrl('position',array('id'=>$item['warehouseId']) );?>">仓位管理</a>-->
        <a href="javascript:" class="del">删除</a>
      </div>
    </div>
  </li>
<?php endforeach;?>
</ul>
<script type="text/html" id="cate1Item">
  <li class="clearfix">
    <div class="clearfix">
      <div class="pull-left name">
        <form action="<?php echo $this->createUrl('add');?>" data-update="<?php echo $this->createUrl('edit');?>" method="post">
        <a href="javascript:" class="glyphicon glyphicon-minus"></a>
        <input type="text" name="form[title]" class="form-control input-sm">
        <input type="hidden" name="form[parentId]" value="0"/>
        <input type="hidden" name="form[warehouseId]" value="0"/>
        </form>
      </div>
      <div class="pull-left control">
        <a href="javascript:" class="del">删除</a>
      </div>
    </div>
    <ul class="list-unstyled">
      <li><button class="btn btn-sm btn-default" disabled data-templateid="cate2Item">添加区域</button></li>
    </ul>
  </li>
</script>
<script type="text/html" id="cate2Item">
  <li class="clearfix">
    <div class="clearfix">
      <div class="pull-left name">
        <form action="<?php echo $this->createUrl('add');?>" data-update="<?php echo $this->createUrl('edit');?>" method="post">
        <span class="glyphicon lever2 glyphicon-minus"></span>
        <input type="text" name="form[title]" class="form-control input-sm">
        <input type="hidden" name="form[parentId]" value="{{parentId}}"/>
        <input type="hidden" name="form[warehouseId]" value="0"/>
        </form>
      </div>
      <div class="pull-left control">
        <a href="javascript:" data-href="/warehouse/warehouse/position/id/warehouseId.html">仓位管理</a>
        <a href="javascript:" class="del">删除</a>
      </div>
    </div>
    <ul class="list-unstyled">
      
    </ul>
  </li>
</script>
<script type="text/html" id="cate3Item">
  <li class="clearfix">
    <div class="pull-left name">
      <form action="<?php echo $this->createUrl('add');?>" data-update="<?php echo $this->createUrl('edit');?>" method="post">
      <input type="text" name="form[title]" class="form-control input-sm">
      <input type="hidden" name="form[parentId]" value="{{parentId}}"/>
      <input type="hidden" name="form[warehouseId]" value="0"/>
      </form>
    </div>
    <div class="pull-left control">
      <a href="javascript:" class="del">删除</a>
    </div>
  </li>
</script>
<script type="text/html" id="cate2Items">
<ul class="list-unstyled">
  {{each list}}
  <li class="clearfix">
    <div class="clearfix">
      <div class="pull-left name">
        <form action="<?php echo $this->createUrl('edit');?>" method="post">
        <span class="glyphicon lever2 glyphicon-minus"></span>
        <input type="text" name="form[title]" class="form-control input-sm" value="{{$value.title}}">
        <input type="hidden" name="form[parentId]" value="{{parentId}}"/>
        <input type="hidden" name="form[warehouseId]" value="{{$value.warehouseId}}"/>
        </form>
      </div>
      <div class="pull-left control">
        <a href="/warehouse/warehouse/position/id/{{$value.warehouseId}}.html">仓位管理</a>
        {{$value.del}}
      </div>
    </div>
    {{$value.child}}
  </li>
  {{/each}}
  <li><button class="btn btn-sm btn-default" data-parentId="{{parentId}}" data-templateid="cate2Item">添加区域</button></li>
</ul>
</script>
<script type="text/html" id="cate3Items">
<ul class="list-unstyled {{hide}}">
  {{each list}}
  <li class="clearfix">
    <div class="pull-left name">
      <form action="<?php echo $this->createUrl('edit');?>" method="post">
      <input type="text" name="form[title]" class="form-control input-sm" value="{{$value.title}}">
      <input type="hidden" name="form[parentId]" value="{{parentId}}"/>
      <input type="hidden" name="form[warehouseId]" value="{{$value.warehouseId}}"/>
      </form>
    </div>
    <div class="pull-left control">
      <a href="javascript:" class="del">删除</a>
    </div>
  </li>
  {{/each}}
</ul>
</script>
<script>
  seajs.use('statics/app/warehouse/js/warehouse.js');
</script>