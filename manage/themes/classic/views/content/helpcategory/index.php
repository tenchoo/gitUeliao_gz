<link rel="stylesheet" href="/themes/classic/statics/app/product/category/css/style.css">
<div class="clearfix well well-sm list-well">
  <div class="pull-left form-inline">
  <?php
  if( $this->checkAccess('/content/helpcategory/add') ){
  ?>
    <button class="btn btn-sm btn-default" data-templateid="cate1Item">添加一级分类</button>
  <?php } ?>
  </div>
  <div class="text-muted title">
    <div class="pull-right  control">操作</div>
	<div class="pull-right sort">排序</div>
	<div class="pull-right  control"></div>
  </div>
</div>
<div  class="category-list">
<ul class="list-unstyled form-inline helpcategory-list">
<?php
foreach ( $categorys as $item ):
$class = isset($item['childrens'])? "glyphicon-plus" : "glyphicon-minus";
?>
  <li class="clearfix">
    <div class="clearfix">
      <div class="pull-left name">
        <form action="<?php echo $this->createUrl('edit');?>" method="post">
        <a href="javascript::" class="glyphicon <?php echo $class;?>"></a>
        <input type="text" name="form[title]" class="form-control input-sm" value="<?php echo $item['title'];?>">
        <input type="hidden" name="form[categoryId]" value="<?php echo $item['categoryId'];?>">
        <input type="hidden" name="form[parentId]" value="0">
        </form>
      </div>
      <div class="pull-right  control">
        <a href="<?php echo $this->createUrl('setcontent',array('categoryId'=>$item['categoryId']));?>">编辑</a>
        <a href="javascript::" class="del">删除</a>
      </div>
	  <div class="pull-right  sort">
        <a href="javascript::" class="glyphicon glyphicon-arrow-up"></a>
        <a href="javascript::" class="glyphicon glyphicon-arrow-down"></a>
      </div>
    </div>
    <?php if( !isset($item['childrens']) ):?>
    <ul class="list-unstyled">
      <li><button class="btn btn-sm btn-default" data-parentId="<?php echo $item['categoryId'];?>" data-templateid="cate2Item">添加二级分类</button></li>
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
        <input type="hidden" name="form[categoryId]" value="0"/>
        </form>
      </div>
      <div class="pull-right  control">
        <a href="javascript::" data-href="<?php echo $this->createUrl('setcontent',array('categoryId'=>'categoryId'));?>">编辑</a>
        <a href="javascript::" class="del">删除</a>
      </div>
	  <div class="pull-right  sort">
        <a href="javascript::" class="glyphicon glyphicon-arrow-up dis"></a>
        <a href="javascript::" class="glyphicon glyphicon-arrow-down dis"></a>
      </div>
    </div>
    <ul class="list-unstyled">
      <li><button class="btn btn-sm btn-default" disabled data-templateid="cate2Item">添加二级分类</button></li>
    </ul>
  </li>
</script>
<script type="text/html" id="cate2Item">
  <li class="clearfix">
    <form action="<?php echo $this->createUrl('add');?>" data-update="<?php echo $this->createUrl('edit');?>" method="post">
    <div class="pull-left name">
      <input type="text" name="form[title]" class="form-control input-sm">
      <input type="hidden" name="form[parentId]" value="{{parentId}}"/>
      <input type="hidden" name="form[categoryId]" value="0"/>
    </div>
	  <div class="pull-left  control">
	   <!--更改页面类型，post值为：type = 0/1,categoryId = 当前分类id　-->
	   <label class="radio-inline"><input type="radio" name="form[type]" value="0"/>列表</label>
	   <label class="radio-inline"><input type="radio" name="form[type]" value="1"/>单页</label>
	   </div>
    <div class="pull-right  control">
      <a href="javascript::" data-href="<?php echo $this->createUrl('setcontent',array('categoryId'=>'categoryId'));?>">编辑</a>
      <a href="javascript::" class="del">删除</a>
    </div>
	  <div class="pull-right  sort">
      <a href="javascript::" class="glyphicon glyphicon-arrow-up dis"></a>
      <a href="javascript::" class="glyphicon glyphicon-arrow-down dis"></a>
    </div>
  </form>
  </li>
</script>
<script type="text/html" id="cate2Items">
<ul class="list-unstyled">
  {{each list}}
  <li class="clearfix">
  <form action="<?php echo $this->createUrl('edit');?>" method="post">
    <div class="clearfix">
      <div class="pull-left name">
        <input type="text" name="form[title]" class="form-control input-sm" value="{{$value.title}}">
        <input type="hidden" name="form[parentId]" value="{{parentId}}"/>
        <input type="hidden" name="form[categoryId]" value="{{$value.categoryId}}"/>
      </div>
	  <div class="pull-left  control">
	   <label class="radio-inline"><input type="radio" name="form[type]" value="0" {{if $value.type === '0'}}checked{{/if}} />列表</label>
	   <label class="radio-inline"><input type="radio" name="form[type]" value="1" {{if $value.type === '1'}}checked{{/if}} />单页</label>
	   </div>
      <div class="pull-right  control">
        <a href="/content/helpcategory/setcontent/categoryId/{{$value.categoryId}}.html">编辑</a>
        <a href="javascript::" class="del">删除</a>
      </div>
	  <div class="pull-right  sort">
        <a href="javascript::" class="glyphicon glyphicon-arrow-up"></a>
        <a href="javascript::" class="glyphicon glyphicon-arrow-down"></a>
      </div>
    </div>
    {{$value.child}}
    </form>
  </li>
  {{/each}}
  <li><button class="btn btn-sm btn-default" data-parentId="{{parentId}}" data-templateid="cate2Item">添加二级分类</button></li>
</ul>
</script>
<script>
  seajs.use('statics/app/help/js/category.js');
</script>