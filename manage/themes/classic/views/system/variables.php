<form class="form-horizontal" action="" method="post">
<?php foreach( $variables as $item ){?>
  <div class="form-group">
    <label class="control-label col-md-2" for=""><?php echo $item->comment;?>：</label>
    <div class="col-md-4">
      <?php switch ($item->valueType){
      	case 'bool':?>
				<label class="radio-inline">
				  <input type="radio" name="config[<?php echo $item->key?>]" value="1" <?php echo ($item->value =='1')?'checked':''?>/>是
				</label>
				<label class="radio-inline">
				   <input type="radio" name="config[<?php echo $item->key?>]" value="0" <?php echo ($item->value =='0')?'checked':''?>/>否</label>
		<?php
      		break;

      	default:?>
		<div class="input-group">
      		<input type="text" class="form-control input-sm" name="config[<?php echo $item->key?>]" value="<?php echo CHtml::encode($item->value);?>">
		<?php if($item->unit){ ?>
        <span class="input-group-addon"><?php echo $item->unit;?></span>
		<?php }?>
		 </div>
		<?php
      		break;
      } ?>
    </div>
  </div>
<?php }?>
<?php $this->beginContent('//layouts/_error');$this->endContent();?>
  <div class="form-group">
    <div class="col-md-offset-2 col-md-10">
      <button class="btn btn-success" type="submit">保存</button>
    </div>
  </div>
</form>