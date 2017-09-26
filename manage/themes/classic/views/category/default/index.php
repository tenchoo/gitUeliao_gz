<link rel="stylesheet" href="/themes/classic/statics/app/product/category/css/style.css">
<div class="clearfix well well-sm list-well">
  <div class="pull-left form-inline">
  <?php 
  if( $this->checkAccess('/category/default/write') ){
  ?>
    <button class="btn btn-sm btn-default" data-templateid="cate1Item">添加一级分类</button>
  <?php } ?>
  </div>
  <div class="text-muted title">
    <div class="pull-left sort">排序</div>
    <div class="pull-left control">操作</div>
    <div class="pull-right"><a href="<?php echo $this->createUrl('/category/default/fixorder') ?>">清空类目缓存</a></div>
  </div>
</div>
<ul class="list-unstyled form-inline category-list">
<?php
foreach ( $categorys as $item ):
$class = isset($item['childrens'])? "glyphicon-plus" : "glyphicon-minus";
?>
  <li class="clearfix">
    <div class="clearfix">
      <div class="pull-left name">
        <form action="/category/default/update" method="post">
        <a href="javascript:" class="glyphicon <?php echo $class;?>"></a>
        <input type="text" name="form[title]" class="form-control input-sm" value="<?php echo $item['title'];?>" maxlength='10'>
        <input type="hidden" name="form[categoryId]" value="<?php echo $item['categoryId'];?>">
        <input type="hidden" name="form[parentId]" value="0">
        </form>
      </div>
      <div class="pull-left sort">
        <a href="javascript:" class="glyphicon glyphicon-arrow-up"></a>
        <a href="javascript:" class="glyphicon glyphicon-arrow-down"></a>
      </div>
      <div class="pull-left control">
        <a href="<?php echo $this->createUrl( '/category/attr/index',array( 'categoryId'=>$item['categoryId'] ) ); ?>">属性规格</a>
        <a href="<?php echo $this->createUrl( '/category/default/detail',array( 'categoryId'=>$item['categoryId'] ) ); ?>">编辑</a>
        <?php if( $item['rft']-$item['lft']==1 ):?>
        <a href="javascript:" class="del">删除</a>
        <?php endif;?>
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
<script type="text/html" id="cate1Item">
  <li class="clearfix">
    <div class="clearfix">
      <div class="pull-left name">
        <form action="/category/default/write" data-update="/category/default/update" method="post">
        <a href="javascript:" class="glyphicon glyphicon-minus"></a>
        <input type="text" name="form[title]" class="form-control input-sm" maxlength="10">
        <input type="hidden" name="form[parentId]" value="0"/>
        <input type="hidden" name="form[categoryId]" value="0"/>
        </form>
      </div>
      <div class="pull-left sort">
        <a href="javascript:" class="glyphicon glyphicon-arrow-up dis"></a>
        <a href="javascript:" class="glyphicon glyphicon-arrow-down dis"></a>
      </div>
      <div class="pull-left control">
        <a href="javascript:" data-href="/category/attr/index/categoryId/categoryId.html">属性规格</a>
        <a href="javascript:" data-href="/category/default/detail/categoryId/categoryId.html">编辑</a>
        <a href="javascript:" class="del">删除</a>
      </div>
    </div>
    <ul class="list-unstyled">
      <li><button class="btn btn-sm btn-default" disabled data-templateid="cate2Item">添加二级分类</button></li>
    </ul>
  </li>
</script>
<script type="text/html" id="cate2Item">
  <li class="clearfix">
    <div class="clearfix">
      <div class="pull-left name">
        <form action="/category/default/write" data-update="/category/default/update" method="post">
        <a href="javascript:" class="glyphicon glyphicon-minus"></a>
        <input type="text" name="form[title]" class="form-control input-sm" maxlength="10">
        <input type="hidden" name="form[parentId]" value="{{parentId}}"/>
        <input type="hidden" name="form[categoryId]" value="0"/>
        </form>
      </div>
      <div class="pull-left sort">
        <a href="javascript:" class="glyphicon glyphicon-arrow-up dis"></a>
        <a href="javascript:" class="glyphicon glyphicon-arrow-down dis"></a>
      </div>
      <div class="pull-left control">
        <a href="javascript:" data-href="/category/attr/index/categoryId/categoryId.html">属性规格</a>
        <a href="javascript:" data-href="/category/default/detail/categoryId/categoryId.html">编辑</a>
        <a href="javascript:" class="del">删除</a>
      </div>
    </div>
    <ul class="list-unstyled">
      <li><a href="javascript:" class="text-disabled" data-parentId="{{parentId}}" data-templateid="cate3Item">添加三级分类</a></li>
    </ul>
  </li>
</script>
<script type="text/html" id="cate3Item">
  <li class="clearfix">
    <div class="pull-left name">
      <form action="/category/default/write" data-update="/category/default/update" method="post">
      <input type="text" name="form[title]" class="form-control input-sm" maxlength="10">
      <input type="hidden" name="form[parentId]" value="{{parentId}}"/>
      <input type="hidden" name="form[categoryId]" value="0"/>
      </form>
    </div>
    <div class="pull-left sort">
      <a href="javascript:" class="glyphicon glyphicon-arrow-up dis"></a>
      <a href="javascript:" class="glyphicon glyphicon-arrow-down dis"></a>
    </div>
    <div class="pull-left control">
      <a href="javascript:" data-href="/category/attr/index/categoryId/categoryId.html">属性规格</a>
      <a href="javascript:" data-href="/category/default/detail/categoryId/categoryId.html">编辑</a>
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
        <form action="/category/default/update" method="post">
        <a href="javascript:" class="glyphicon lever2 {{$value.class}}"></a>
        <input type="text" name="form[title]" class="form-control input-sm" value="{{$value.title}}" maxlength="10">
        <input type="hidden" name="form[parentId]" value="{{parentId}}"/>
        <input type="hidden" name="form[categoryId]" value="{{$value.categoryId}}"/>
        </form>
      </div>
      <div class="pull-left sort">
        <a href="javascript:" class="glyphicon glyphicon-arrow-up"></a>
        <a href="javascript:" class="glyphicon glyphicon-arrow-down"></a>
      </div>
      <div class="pull-left control">
        <a href="/category/attr/index/categoryId/{{$value.categoryId}}.html">属性规格</a>
        <a href="/category/default/detail/categoryId/{{$value.categoryId}}.html">编辑</a>
        {{$value.del}}
      </div>
    </div>
    {{$value.child}}
  </li>
  {{/each}}
  <li><button class="btn btn-sm btn-default" data-parentId="{{parentId}}" data-templateid="cate2Item">添加二级分类</button></li>
</ul>
</script>
<script type="text/html" id="cate3Items">
<ul class="list-unstyled {{hide}}">
  {{each list}}
  <li class="clearfix">
    <div class="pull-left name">
      <form action="/category/default/update" method="post">
      <input type="text" name="form[title]" class="form-control input-sm" value="{{$value.title}}" maxlength="10">
      <input type="hidden" name="form[parentId]" value="{{parentId}}"/>
      <input type="hidden" name="form[categoryId]" value="{{$value.categoryId}}"/>
      </form>
    </div>
    <div class="pull-left sort">
      <a href="javascript:" class="glyphicon glyphicon-arrow-up"></a>
      <a href="javascript:" class="glyphicon glyphicon-arrow-down"></a>
    </div>
    <div class="pull-left control">
      <a href="/category/attr/index/categoryId/{{$value.categoryId}}.html">属性规格</a>
      <a href="/category/default/detail/categoryId/{{$value.categoryId}}.html">编辑</a>
      <a href="javascript:" class="del">删除</a>
    </div>
  </li>
  {{/each}}
  <li><a href="javascript:" data-parentId="{{parentId}}" data-templateid="cate3Item">添加三级分类</a></li>
</ul>
</script>
<script>
  seajs.use('statics/app/product/category/js/category.js');
</script>