<?php if ( is_array ( $attrlist ) ) {
  foreach ( $attrlist as $key=>$attr ) { ?>
  <div class="form-group">
  <label class="control-label col-md-2">
  <?php echo array_key_exists($key,$attrgroups)? $attrgroups[$key]:'产品属性';?>：
  </label>
  <div class="control-div col-md-10 col-lg-8">
    <div class="well attr-div">
      <?php
      foreach ( $attr as $attrval ) {
          $attrId = $attrval['attrId'];
          $name = 'attrs['.$attrId.']';
           $chooseValue =  array_key_exists( $attrId,$attrs )? $attrs[$attrId]:array();

      if( $attrval['type'] != '2' && is_array( $chooseValue ) ){
           $chooseValue = implode(',', $chooseValue);
      }
    ?>
      <div class="form-group">
        <label class="control-label col-md-2"><?php echo $attrval['title']; ?>：</label>
        <div class="col-md-<?php if($attrval['type'] > 2){?>4<?php }else{?>8<?php }?>">

        <?php switch( $attrval['type'] ) {
          case '1': ?>
          <?php foreach( $attrval['attrValue'] as $vvar) { ?>
        <label class="radio-inline">
          <input type="radio" name="<?php echo $name;?>" value="<?php echo $vvar;?>" <?php if( $vvar==$chooseValue ){ echo 'checked';}?> />
          <?php echo $vvar;?>
        </label>
          <?php } ?>
        <?php break;
          case '2': ?>
          <?php foreach( $attrval['attrValue'] as $vvar) { ?>
         <label class="checkbox-inline">
          <input type="checkbox" name="<?php echo $name;?>[]" value="<?php echo $vvar;?>" <?php if( in_array($vvar,$chooseValue) ){ echo 'checked';}?> />
          <?php echo $vvar;?>
        </label>
          <?php } ?>
        <?php break;
          case '3': ?>
          <select name="<?php echo $name;?>" class="form-control input-sm input-short">
          <?php   foreach( $attrval['attrValue'] as $vvar){ ?>
          <option value="<?php echo $vvar;?>" <?php if( $vvar == $chooseValue ){ echo 'SELECTED';}?>>
            <?php echo $vvar;?>
          </option>
          <?php  }  ?>
            </select>

        <?php break;
          case '4': ?>
          <input type="text" name="<?php echo $name;?>" class="form-control input-sm input-short" value="<?php echo $chooseValue;?>" />
        <?php break;
          case '5': ?>
           <textarea name="<?php echo $name;?>" class="form-control"><?php echo $chooseValue;?></textarea>
        <?php break;
        }
        ?>
        </div>
      </div>
        <?php } ?>
    </div>
  </div>
</div>
<?php }} ?>