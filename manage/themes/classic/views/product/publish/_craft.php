  <div class="form-group">
    <label class="control-label col-md-2"><span class="text-danger">*</span>工艺产品编号：</label>
    <div class="col-md-2">
      <input type="text" name="p[serialNumber]" value="<?php echo $data['serialNumber'] ?>" class="form-control input-sm" data-val="<?php echo $data['baseSerialNumber'] ?>" <?php if( $data['productId'] ){ echo 'disabled';}?> readonly="readonly"/>
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2"><span class="text-danger">*</span>特殊工艺：</label>
    <div class="control-div col-md-10 col-lg-8">
      <div class="well attr-div craft-wrap">
    	 <?php foreach ( $craft as $key=>$val ) { ?>
        <div class="form-group">
			<label class="control-label col-md-2"> <?php echo $val['title'];?>：</label>
			<div class="col-md-8">
            <label class="checkbox-inline">
          <input type="checkbox" value="<?php echo $val['craftCode'];?>" name="p[craft][<?php echo $key; ?>]" <?php if( in_array($val['craftCode'],$data['craft'])){ echo 'checked';}?> <?php if( $data['productId'] ){ echo 'disabled';}?>/><?php echo $val['title'];?>(<?php echo $val['craftCode'];?>)</label>
		  <?php if ( array_key_exists( 'childs', $val ) ){ ?>
		  	<?php foreach ( $val['childs'] as $cval ) { ?>
        <label class="checkbox-inline">
    		<input type="checkbox" value="<?php echo $cval['craftCode'];?>" name="p[craft][<?php echo $key; ?>]" <?php if( in_array($cval['craftCode'],$data['craft'])){ echo 'checked';}?> <?php if( $data['productId'] ){ echo 'disabled';}?>/>
    		<?php echo $cval['title'];?>(<?php echo $cval['craftCode'];?>)
        </label>
    	  <?php }?>
		  
		   <?php }?>
			</div> 		
      </div>
	  <?php }?>
    </div>
  </div>
  </div>
  
<script>seajs.use('statics/app/product/create/js/craft.js');</script>