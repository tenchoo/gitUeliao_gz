<link rel="stylesheet" href="/themes/classic/statics/app/product/category/css/style.css">
<div class="clearfix well well-sm list-well">
  <div class="pull-left form-inline">
  <?php
  if( $this->checkAccess('/content/recommend/add') ){
  ?>
    <button class="btn btn-sm btn-default" data-templateid="cate1Item">添加页面</button>
  <?php } ?>
  </div>
  <div class="text-muted title">
    <div class="pull-right  control">操作</div>
	<div class="pull-right control">已推荐产品数/总数</div>
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
        <input type="hidden" name="form[recommendId]" value="<?php echo $item['recommendId'];?>">
        <input type="hidden" name="form[parentId]" value="0">
        </form>
      </div>
      <div class="pull-right  control">
        <a href="javascript::" class="del">删除</a>
      </div>
    </div>
    <?php if( !isset($item['childrens']) ):?>
    <ul class="list-unstyled">
      <li><button class="btn btn-sm btn-default" data-parentId="<?php echo $item['recommendId'];?>" data-templateid="cate2Item">新建推荐位</button></li>
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
        <input type="hidden" name="form[recommendId]" value="0"/>
        </form>
      </div>
      <div class="pull-right control">
		<!-- <a href="javascript::" data-href="<?php echo $this->createUrl('productlist',array('recommendId'=>'recommendId'));?>">添加推荐</a>
        <a href="javascript::" data-href="<?php echo $this->createUrl('editposition',array('recommendId'=>'recommendId'));?>">编辑</a> -->
        <a href="javascript::" class="del">删除</a>
      </div>
    </div>
    <ul class="list-unstyled">
      <li><button class="btn btn-sm btn-default" disabled data-templateid="cate2Item">新建推荐位</button></li>
    </ul>
  </li>
</script>
<script type="text/html" id="cate2Item">
  <li class="clearfix cate2">
    <div class="pull-left name">
		<form action="<?php echo $this->createUrl('add');?>" data-update="<?php echo $this->createUrl('edit');?>" method="post">
        <input type="text" name="form[title]" class="form-control input-sm">
        <input type="hidden" name="form[parentId]" value="{{parentId}}"/>
        <input type="hidden" name="form[recommendId]" value="0"/>
		 </form>
      </div>
	  <div class="pull-right  control">
		<a href="javascript::" data-href="<?php echo $this->createUrl('productlist',array('recommendId'=>'recommendId'));?>">添加推荐</a>
        <a href="javascript::" data-href="<?php echo $this->createUrl('editposition',array('recommendId'=>'recommendId'));?>">编辑</a>
		<a href="javascript::" class="del">删除</a>
      </div>
	  <div class="pull-right  control">
          <a href="javascript::" data-href="<?php echo $this->createUrl('list',array('recommendId'=>'recommendId'));?>">已推荐产品(0/0)</a>
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
        <input type="hidden" name="form[recommendId]" value="{{$value.recommendId}}"/>
        </form>
      </div>
      <div class="pull-right  control">
		 <a href="/content/recommend/productlist/recommendId/{{$value.recommendId}}.html">添加推荐</a>
        <a href="/content/recommend/editposition/recommendId/{{$value.recommendId}}.html">编辑</a>
		<a href="javascript::" class="del">删除</a>
      </div>
	  <div class="pull-right  control">
         <a href="/content/recommend/list/recommendId/{{$value.recommendId}}.html">已推荐产品({{$value.num}}/{{$value.maxNum}})</a>
      </div>
    </div>
    {{$value.child}}
  </li>
  {{/each}}
  <li><button class="btn btn-sm btn-default" data-parentId="{{parentId}}" data-templateid="cate2Item">新建推荐位</button></li>
</ul>
</script>



<script>
  seajs.use('statics/app/recommend/index.js');
</script>

<script type="text/html" id="jscode">
<H2>js引用 json格式:</H2>
<pre>
<code class="language-html" data-lang="html">
&lt;script src=&quot;{{url1}}&quot; data-no=&quot;op&quot;&gt; &lt;/script&gt;
</code>
</pre>
<p>或者用标识读取:</p>
<pre>
<code class="language-html" data-lang="html">
&lt;script  src=&quot;{{url2}}&quot; data-no=&quot;op&quot;&gt; &lt;/script&gt;
</code>
</pre>
<H2>js引用 document.write:</H2>
<pre>
<code class="language-html" data-lang="html">
&lt;script src=&quot;{{url3}}&quot; data-no=&quot;op&quot;&gt; &lt;/script&gt;
</code>
</pre>
<p>或者用标识读取:</p>
<pre>
<code class="language-html" data-lang="html">
&lt;script src=&quot;{{url4}}&quot; data-no=&quot;op&quot;&gt; &lt;/script&gt;
</code>
</pre>
<H2>PHP引用</H2>
<pre>
<code class="language-html" data-lang="html">
&lt;?php $this-&gt;widget('widgets.AdWidget', array('id' =&gt; {{id}}));?&gt;
</code>
</pre>
<p>或者用标识读取:</p>
<pre>
<code class="language-html" data-lang="html">
&lt;?php $this-&gt;widget('widgets.AdWidget', array('mark' =&gt;'{{mark}}'));?&gt;
</code>
</pre>
</script>