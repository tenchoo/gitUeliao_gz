<link rel="stylesheet" href="/themes/classic/statics/app/product/category/css/style.css">
<ul class="nav nav-tabs">
  <li class="active"><a href="javascript:">属性设置</a></li>
  <li><a href="<?php echo $this->createUrl('/category/spec/categoryspec',array('categoryId'=>$categoryId))?>">规格设置</a></li>
</ul>
<br>
<div class="panel panel-default search-panel">
  <div class="panel-body">

    <div class="pull-left">
      <button class="btn btn-sm btn-default" data-templateid="attrItem">新增属性</button>
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
<table class="table table-striped table-condensed table-hover table-bordered form-inline">
  <thead>
    <tr>
      <td width="20%">属性名称 </td>
		  <td>属性值</td>
		  <td width="8%">支持搜索 </td>
		  <td>控件类型</td>
		  <td>属性组</td>
		  <td>排序 </td>
		  <td width="8%">操作 </td>
    </tr>
  </thead>
  <tbody>
<?php $count = count($list);
	foreach( $list as $key => $val ){
?>
	<tr>
    <form action="/category/attr/setattr" method="post">
    <input type="hidden" name="form[categoryId]" value="<?php echo $categoryId;?>">
	  <td class="title">
	  	<label class="checkbox-inline">
		  <input type="checkbox" name="extendids[]" value="<?php echo $val['attributeId'];?>"/></label>
			<input type="hidden" name="form[attributeId]" value="<?php echo $val['attributeId']; ?>"/>
			<input class="form-control input-sm" type="text" name="form[title]" value="<?php echo $val['title'] ?>" maxlength="10"/>
		 </td>
		  <td>
			<input class="form-control input-sm" type="text" name="form[attrValue]" value="<?php echo $val['attrValue'] ?>"/>
			<label class="checkbox-inline">
			<input type="checkbox" name="form[isOther]" value="1" <?php if( $val['isOther'] ) { echo 'checked';} ?>/> 其他</label>
		</td>
		<td class="search">
			<label class="checkbox-inline"><input type="checkbox" name="form[isSearch]" value="1"  <?php if( $val['isSearch'] ) { echo 'checked';} ?> /></label>
		</td>
	  <td>
	  	<select name="form[type]">
				<option value="1">单选框</option>
				<option value="2" <?php if( $val['type'] == '2' ) { echo 'selected';} ?>>复选框</option>
				<option value="3" <?php if( $val['type'] == '3' ) { echo 'selected';} ?>>下拉框</option>
				<option value="4" <?php if( $val['type'] == '4' ) { echo 'selected';} ?>>文本框</option>
				<option value="5" <?php if( $val['type'] == '5' ) { echo 'selected';} ?>  >文本域</option>
			</select>
	  </td>
	  <td>
	   	<select name="form[setGroupId]">
				<option value="0">不分组</option>
				<?php foreach ( $setgroups as $key2 =>$setval ){ ?>
				<option value="<?php echo $key2;?>" <?php if( $key2 == $val['setGroupId'] ) { echo 'selected';} ?>  ><?php echo $setval;?></option>
				<?php }?>
			</select>
	  </td>
	  <td>
		  <a href="javascript:" class="glyphicon glyphicon-arrow-up<?php if(  $key == '0' ) { ?> dis <?php } ?>"></a>
			<a href="javascript:" class="glyphicon glyphicon-arrow-down<?php if( $key == $count -1 ) { ?> dis <?php } ?>"></a>
	  </td>
	  <td>
	  	<a class="del" href="<?php echo $this->createUrl( 'delattributes',array( 'attributeId'=>$val['attributeId'] ) ); ?>">删除</a>
	  </td>
    </form>
	</tr>
<?php } ?>
	<!--新增样例,form 的id需自增-->

	<!--新增样例end-->
  </tbody>
</table>
<p class="text-muted">说明：请用英文逗号分隔多个值,不要能逗号开头和结尾，两个逗号之间值不要为空</p>
<input type="hidden" id="categoryId" value="<?php echo $categoryId;?>">
<script type="text/html" id="attrItem">
<tr>
 <form action="/category/attr/setattr" method="post" data-update="true">
 <input type="hidden" name="form[categoryId]" value="<?php echo $categoryId;?>">
 <td class="title">
 	<label class="checkbox-inline">
		<input type="checkbox" name="extendids[]" disabled value=""/>
	</label>
	<input class="form-control input-sm" type="text" name="form[title]" value="">
 </td>
  <td>
		<input class="form-control input-sm" type="text" name="form[attrValue]" value="">
		<label class="checkbox-inline">
		<input type="checkbox" name="form[isOther]" value="1"> 其他</label>
	</td>
	<td class="search">
		<label class="checkbox-inline"><input type="checkbox" name="form[isSearch]" value="1"></label>
	</td>
  <td>
  	<select name="form[type]">
			<option value="1">单选框</option>
			<option value="2">复选框</option>
			<option value="3" selected="">下拉框</option>
			<option value="4">文本框</option>
			<option value="5">文本域</option>
		</select>
  </td>
   <td>
   	<select name="form[setGroupId]">
			<option value="0">不分组</option>
				<?php foreach ( $setgroups as $key =>$val ){ ?>
				<option value="<?php echo $key;?>"><?php echo $val;?></option>
				<?php }?>
			</select>
  </td>
  <td>
	  <a href="javascript:" class="glyphicon glyphicon-arrow-up"></a>
		<a href="javascript:" class="glyphicon glyphicon-arrow-down"></a>
  </td>
  <td>
    <a class="save" href="javascript:">保存</a>
  	<a class="del" href="#">删除</a>
  </td>
  </form>
</tr>
</script>

<script>
  seajs.use('statics/app/product/category/js/attr.js');
</script>
