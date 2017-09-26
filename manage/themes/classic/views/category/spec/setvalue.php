<form class="form-horizontal"  method="post">
	<?php if( $data['isColor'] =='1' ){ ?>
	<div class="form-group">
          <label class="control-label col-md-2" for=""> *所属色系：</label>
          <div class="col-md-4">
		<select class="form-control" name="specForm[colorSeriesId]">
			<option value="0">请选择色系</option>
		<?php if(is_array($data['colorseries'])){
			foreach($data['colorseries'] as $key =>$val ){ ?>
				<option value="<?php echo $key;?>" <?php if( isset( $data['colorSeriesId']) && $key ==$data['colorSeriesId'] ){ echo 'selected="selected"';}?>>
					<?php echo $val;?>
				</option>
			<?php }} ?>
		</select>
    </div>
  </div>
	<?php } ?>
  <div class="form-group">
    <label class="control-label col-md-2" for=""><span class="text-danger">*</span> 名称：</label>
    <div class="col-md-4">
      <input type="text" class="form-control input-sm"  name="specForm[title]" value="<?php echo isset( $data['title'])? $data['title']:'';?>" maxlength="10">
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2" for=""><span class="text-danger">*</span> 值</label>
    <div class="col-md-4">
    <input type="text" class="form-control input-sm"  name="specForm[code]" value="<?php echo isset( $data['code'])? $data['code']:'';?>" maxlength="10">
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2" for=""><?php if( $data['isColor'] =='1' ){ ?><span class="text-danger">*</span><?php } ?>编号：</label>
    <div class="col-md-4">
    <input type="text" class="form-control input-sm"  name="specForm[serialNumber]" value="<?php echo isset( $data['serialNumber']) ? $data['serialNumber']:'';?>" maxlength="10">
    </div>
  </div>

  <?php $this->beginContent('//layouts/_error');$this->endContent();?>
  <br/>
  <div class="col-md-offset-2 col-md-10">
    <button class="btn btn-success" type="submit">保存</button>
    <a href="/category/spec" class="btn btn-default">取消</a>
  </div>
</form>
