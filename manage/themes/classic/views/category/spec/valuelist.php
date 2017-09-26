<div class="panel panel-default search-panel">
  <div class="panel-body">
    <form class="pull-left form-inline" role="search" action="<?php echo $this->createUrl('valuelist',array('specId'=>$specData['specId']));?>">
      <input class="form-control input-sm" type="text" name="keyword" value="<?php echo $keyword;?>"/>
      <button class="btn btn-sm btn-default" type="submit">查找</button>
    </form>
    <div class="pull-right">
      <a class="btn btn-default" href="<?php echo $this->createUrl('setvalue',array('specId'=>$specData['specId']));?>">新增</a>
    </div>
  </div>
</div>
<form method="post">
<div class="clearfix well well-sm list-well">
  <div class="pull-left form-inline">
    <label class="checkbox-inline">
      <input class="checkedall" type="checkbox"> 全选
    </label>
  </div>
    <?php $this->beginContent('//layouts/_page',array('pages'=>$data['pages']));$this->endContent();?>
</div>
<table class="table table-condensed table-bordered">
  <colgroup><col width="24"><col width="20%"><col width="20%"><col width="20%"><col width="20%"><col></colgroup>
  <thead>
    <tr>
    <td></td>
    <td>名称</td>
    <td>值</td>
    <td>编号</td>
    <?php if( $specData['isColor'] =='1' ){ ?>
    <td>所属色系</td>
    	<?php } ?>
    <td>操作 </td>
    </tr>
  </thead>
</table>
<br>
<table class="table table-condensed table-bordered table-hover">
  <colgroup><col width="24"><col width="20%"><col width="20%"><col width="20%"><col width="20%"><col></colgroup>
  <tbody>
  <?php
  	if( is_array( $data['list'] ) ) {
  		foreach( $data['list'] as $val ){
  ?>
   <tr>
    <td><input type="checkbox" name="" value="<?php echo $val['specvalueId']?>"/></td>
  	<td><?php echo $val['title']?></td>
  	<td><?php echo $val['code']?></td>
  	<td><?php echo $val['serialNumber']?></td>
  	<?php if( $specData['isColor'] =='1' ){
  		$colorSeriesId = $val['colorSeriesId'];
  	?>
  	<td><?php echo isset($specData['colorseries'][$colorSeriesId])?$specData['colorseries'][$colorSeriesId]:''?></td>
  		<?php } ?>
  	<td>

  		<?php echo CHtml::link('编辑',array('setvalue','specvalueId'=>$val['specvalueId'],'from'=>urlencode( Yii::app()->request->url))); ?>
		<?php if($val['hasProduct'] == '0' ){ ?>
		<a href="javascript:" class="del">删除</a>
		<?php } ?>
  	 </td>
    </tr>
  <?php }}?>
  </tbody>
</table>
<div class="clearfix well well-sm list-well">
  <div class="pull-left form-inline">
    <label class="checkbox-inline">
      <input class="checkedall" type="checkbox"> 全选
    </label>
  </div>
  <?php $this->beginContent('//layouts/_page',array('pages'=>$data['pages']));$this->endContent();?>
</div>
</form>
<script>
  seajs.use('statics/app/product/category/js/speclist.js')
</script>
<?php $this->beginContent('//layouts/_del');$this->endContent();?>