<link rel="stylesheet" href="/themes/classic/statics/app/product/category/css/style.css">
<div class="clearfix well well-sm list-well">
  <div class="pull-left form-inline">
  <?php
  if( $this->checkAccess('/content/advertisement/add') ){
  ?>
    <button class="btn btn-sm btn-default" data-templateid="cate1Item">添加页面</button>
  <?php } ?>
  </div>
  <div class="text-muted title">
    <div class="pull-right  control">操作</div>
	<div class="pull-right control">已上传广告数/总数</div>
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
        <input type="hidden" name="form[adPositionId]" value="<?php echo $item['adPositionId'];?>">
        <input type="hidden" name="form[parentId]" value="0">
        </form>
      </div>
      <div class="pull-right  control">
        <a href="javascript::" class="del">删除</a>
      </div>
    </div>
    <?php if( !isset($item['childrens']) ):?>
    <ul class="list-unstyled">
      <li><button class="btn btn-sm btn-default" data-parentId="<?php echo $item['adPositionId'];?>" data-templateid="cate2Item">新建广告位</button></li>
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
        <input type="hidden" name="form[adPositionId]" value="0"/>
        </form>
      </div>
      <div class="pull-right  control">
        <a href="javascript::" data-href="<?php echo $this->createUrl('editposition',array('adPositionId'=>'adPositionId'));?>">编辑</a>
        <a href="javascript::" class="del">删除</a>
      </div>
    </div>
    <ul class="list-unstyled">
      <li><button class="btn btn-sm btn-default" disabled data-templateid="cate2Item">新建广告位</button></li>
    </ul>
  </li>
</script>
<script type="text/html" id="cate2Item">
  <li class="clearfix">
    <div class="pull-left name">
		<form action="<?php echo $this->createUrl('add');?>" data-update="<?php echo $this->createUrl('edit');?>" method="post">
        <input type="text" name="form[title]" class="form-control input-sm">
        <input type="hidden" name="form[parentId]" value="{{parentId}}"/>
        <input type="hidden" name="form[adPositionId]" value="0"/>
		 </form>
      </div>
	  <div class="pull-right  control">
        <a href="javascript::" data-href="<?php echo $this->createUrl('addad',array('adPositionId'=>'adPositionId'));?>">上传广告</a>
        <a href="javascript::" data-href="<?php echo $this->createUrl('editposition',array('adPositionId'=>'adPositionId'));?>">编辑</a>
		<a href="###" data-toggle="modal" data-target=".add-confirm">生成JS</a>
		<a href="javascript::" class="del">删除</a>
      </div>
	  <div class="pull-right  control">
          <a href="javascript::" data-href="<?php echo $this->createUrl('adlist',array('adPositionId'=>'adPositionId'));?>">已上传广告(0/0)</a>
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
        <input type="hidden" name="form[adPositionId]" value="{{$value.adPositionId}}"/>
        </form>
      </div>
      <div class="pull-right  control">
        <a href="/content/advertisement/addad/adPositionId/{{$value.adPositionId}}.html">上传广告</a>
        <a href="/content/advertisement/editposition/adPositionId/{{$value.adPositionId}}.html">编辑</a>
		<a href="javascript::" data-toggle="modal" data-target=".add-confirm" data-mark="{{$value.mark}}" data-id="{{$value.adPositionId}}" class="createjs">生成JS</a>
		<a href="javascript::" class="del">删除</a>
      </div>
	  <div class="pull-right  control">
         <a href="/content/advertisement/adlist/adPositionId/{{$value.adPositionId}}.html">已上传广告({{$value.num}}/{{$value.maxNum}})</a>
      </div>
    </div>
    {{$value.child}}
  </li>
  {{/each}}
  <li><button class="btn btn-sm btn-default" data-parentId="{{parentId}}" data-templateid="cate2Item">新建广告位</button></li>
</ul>
</script>

<div class="modal fade add-confirm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="exampleModalLabel">广告引用</h4>
      </div>
      <div class="modal-body">
        <div class="form-inline">
			    <div class="form-group">
			      <div class="inline-block category-select" id='jsdemo'>
			       JS引用。。。
			      </div>
			    </div>
			  </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">确定</button>
      </div>
    </div>
  </div>
</div>

<script>
  seajs.use('statics/app/ad/js/index.js');
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