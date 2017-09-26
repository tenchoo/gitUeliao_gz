<link rel="stylesheet" href="/themes/classic/statics/app/product/category/css/style.css">
<div class="clearfix well well-sm list-well">
  <div class="pull-left form-inline">
    <button class="btn btn-sm btn-default" data-templateid="specItem">添加规格</button>
  </div>
</div>

<ul class="list-unstyled form-inline category-list spec-list">
  <?php
    if( is_array( $list ) ) {
      foreach( $list as $val ){
  ?>
  <li>
    <div class="clearfix">
      <form action="/category/spec/setspec" method="post">
      <input type="hidden" name="form[specId]" value="<?php echo $val['specId']; ?>">
      <div class="pull-left name">
      <input class="form-control input-sm" type="text" name="form[specName]" value="<?php echo $val['specName'] ?>" maxlength="10"/>
      </div>
      </form>
      <div class="pull-left control">
	  <a href="<?php echo $this->createUrl('setvalue',array('specId'=>$val['specId']) );?>">添加规格值</a>
	  <a href="<?php echo $this->createUrl('valuelist',array('specId'=>$val['specId']) );?>">管理</a>
	  <?php if($val['specId']>1){ ?>
	  <a href="#" class="del" data-toggle="modal" data-target=".del-confirm" data-id="<?php echo $val['specId'] ?>" data-rel="<?php echo $this->createUrl('del');?>">删除</a>
	  <?php } ?>
      </div>
    </div>
  </li>
  <?php }}?>
</ul>
<script type="text/html" id="specItem">
<li>
  <div class="clearfix">
    <form action="/category/spec/setspec" data-update="true" method="post">
    <input type="hidden" name="form[specId]"/>
    <div class="pull-left name">
    <input class="form-control input-sm" type="text" name="form[specName]" value="" maxlength="10"/>
    </div>
    </form>
    <div class="pull-left control">
    <a href="" data-href="<?php echo $this->createUrl('setvalue',array('specId'=>'specId') );?>">添加规格值</a>
    <a href="" data-href="<?php echo $this->createUrl('valuelist',array('specId'=>'specId') );?>">管理</a>
    <a href="#" data-toggle="modal" data-target=".del-confirm" data-href="<?php echo $this->createUrl('del',array('specId'=>'specId') );?>" class="del" data-rel="<?php echo $this->createUrl('del');?>">删除</a>
    </div>
  </div>
</li>
</script>
<script>
  seajs.use('statics/app/product/category/js/spec.js')
</script>
<?php $this->beginContent('//layouts/_del');$this->endContent();?>