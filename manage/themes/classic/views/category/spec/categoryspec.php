<link rel="stylesheet" href="/themes/classic/statics/app/product/category/css/style.css">
<ul class="nav nav-tabs">
  <li><a href="<?php echo $this->createUrl('/category/attr/index',array('categoryId'=>$categoryId))?>">属性设置</a></li>
  <li class="active"><a href="javascript:">规格设置</a></li>
</ul>
<br>

<div class="panel panel-default search-panel">
  <div class="panel-body">

    <div class="pull-left form-inline">
      <form class="add" method="post" action="/category/spec/addcategoryspec" data-templateid="specItem">
      <input type="hidden" id="categoryId" name="categoryId" value="<?php echo $categoryId;?>">
      <select class="form-control input-sm" name="specId">
        <?php if( is_array( $speclist ) ){
            foreach( $speclist as $val ){
        ?>
        <option value="<?php echo $val['specId']; ?>"><?php echo $val['specName']; ?></option>
        <?php }}?>
      </select>
      <button class="btn btn-sm btn-default" type="submit" <?php echo (count($data)>=2)?'disabled':''?>>添加规格</button>
      </form>
    </div>
  </div>
</div>
<div class="clearfix well well-sm list-well">
  <div class="pull-left form-inline">
    <label class="checkbox-inline">
      <input class="checkedall" type="checkbox"> 全选
    </label>

    <button class="btn btn-sm btn-default save">继承到所有子类</button>
  </div>
</div>

<table class="table table-striped table-condensed table-hover table-bordered">
  <thead>
    <tr>
      <td>规格名称</td>
      <td width="20%">操作</td>
    </tr>
  </thead>
  <tbody>
  <?php if( is_array( $data ) ) {
  foreach($data as $val) {
  $specId = $val['specId'];
  if( isset ($speclist[$specId]) ){
  ?>
    <tr>
      <td>
        <input type="checkbox" name="extendids[]" value="<?php echo $specId;?>"/>
         <?php echo $speclist[$specId]['specName'];?>
      </td>
      <td>
        <?php echo CHtml::link('编辑',array('valuelist','specvalueId'=>$val['specId'])); ?>
      	<a href="<?php echo $this->createUrl('delcategoryspec',array('specId'=>$val['specId'],'categoryId'=>$val['categoryId']) );?>" class="del">删除</a>
      </td>
    </tr>
  <?php } }} ?>
  </tbody>
</table>

<script>
  seajs.use('statics/app/product/category/js/categoryspec.js')
</script>
说明：规格至多只能添加2个
<script id="specItem" type="text/html">
<tr>
  <td>
    <input type="checkbox" name="extendids[]" value="{{id}}"/>
     {{name}}
  </td>
  <td>
    <a href="/category/spec/setvalue/specvalueId/{{id}}.html">编辑</a>
    <a href="/category/spec/delcategoryspec/specId/{{id}}/categoryId/5.html" class="del">删除</a>
  </td>
</tr>
</script>

<?php $this->beginContent('//layouts/_error');$this->endContent();?>