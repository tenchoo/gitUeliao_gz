<link rel="stylesheet" href="/themes/classic/statics/app/product/category/css/style.css">
<div class="clearfix well well-sm list-well">
  <div class="pull-left form-inline">
  <?php
  if( $this->checkAccess('/content/piece/add') ){
  ?>
    <button class="btn btn-sm btn-default" data-templateid="cate1Item">添加页面</button>
  <?php } ?>
  </div>
  <div class="text-muted title">
    <div class="pull-right  control">操作</div>
  </div>
</div>
<div  class="category-list">
<ul class="list-unstyled form-inline tree-list">
<?php
foreach ( $list as $item ):
$class = isset($item['childrens'])? "glyphicon-plus" : "glyphicon-minus";
?>
  <li class="clearfix">
    <div class="clearfix">
      <div class="pull-left name">
        <form action="<?php echo $this->createUrl('edit');?>" method="post">
        <a href="javascript::" class="glyphicon <?php echo $class;?>"></a>
        <input type="text" name="form[title]" class="form-control input-sm" value="<?php echo $item['title'];?>">
        <input type="hidden" name="form[pieceId]" value="<?php echo $item['pieceId'];?>">
        <input type="hidden" name="form[parentId]" value="0">
        </form>
      </div>
      <div class="pull-right  control">
        <a href="javascript::" class="del">删除</a>
      </div>
    </div>
    <?php if( !isset($item['childrens']) ):?>
    <ul class="list-unstyled">
      <li><button class="btn btn-sm btn-default" data-parentId="<?php echo $item['pieceId'];?>" data-templateid="cate2Item">添加碎片</button></li>
    </ul>
    <?php endif;?>
  </li>
	<?php endforeach;?>
</ul>
</div>
<script type="text/html" id="cate1Item">
  <li class="clearfix">
    <div class="clearfix">
      <div class="pull-left name">
        <form action="<?php echo $this->createUrl('add');?>" data-update="<?php echo $this->createUrl('edit');?>" method="post">
        <a href="javascript::" class="glyphicon glyphicon-minus"></a>
        <input type="text" name="form[title]" class="form-control input-sm">
        <input type="hidden" name="form[parentId]" value="0"/>
        <input type="hidden" name="form[pieceId]" value="0"/>
        </form>
      </div>
      <div class="pull-right  control">
        <a href="javascript::" data-href="<?php echo $this->createUrl('setcontent',array('pieceId'=>'pieceId'));?>">编辑</a>
        <a href="javascript::" class="del">删除</a>
      </div>
    </div>
    <ul class="list-unstyled">
      <li><button class="btn btn-sm btn-default" disabled data-templateid="cate2Item">添加碎片</button></li>
    </ul>
  </li>
</script>
<script type="text/html" id="cate2Item">
  <li class="clearfix">
    <div class="pull-left name">
		<form action="<?php echo $this->createUrl('add');?>" data-update="<?php echo $this->createUrl('edit');?>" method="post">
        <input type="text" name="form[title]" class="form-control input-sm">
        <input type="hidden" name="form[parentId]" value="{{parentId}}"/>
        <input type="hidden" name="form[pieceId]" value="0"/>
		 </form>
      </div>
	  <div class="pull-right  control">    
        <a href="javascript::" data-href="<?php echo $this->createUrl('setcontent',array('pieceId'=>'pieceId'));?>">编辑</a>
		<a href="javascript::" class="del">删除</a>
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
        <input type="text" name="form[title]" class="form-control input-sm" value="{{$value.title}}">
        <input type="hidden" name="form[parentId]" value="{{parentId}}"/>
        <input type="hidden" name="form[pieceId]" value="{{$value.pieceId}}"/>
        </form>
      </div>
      <div class="pull-right  control">
        <a href="/content/piece/setcontent/pieceId/{{$value.pieceId}}.html">编辑</a>
		<a href="javascript::" class="del">删除</a>
      </div>
    </div>
    {{$value.child}}
  </li>
  {{/each}}
  <li><button class="btn btn-sm btn-default" data-parentId="{{parentId}}" data-templateid="cate2Item">添加碎片</button></li>
</ul>
</script>
<script>
  seajs.use('statics/app/piece/js/index.js');
</script>