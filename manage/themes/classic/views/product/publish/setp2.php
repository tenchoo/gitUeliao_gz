<link rel="stylesheet" href="/themes/classic/statics/modules/uploader/css/style.css"/>
<link rel="stylesheet" href="/themes/classic/statics/app/product/create/css/style.css">
 <?php $this->beginContent('//layouts/_error2');$this->endContent();?>
<?php
$canSave = true;
 $this->beginContent('_tab',array('active'=>'edit','productId'=>$publicinfo['productId']));$this->endContent();?>

<div class="clearfix alert alert-warning">
  <?php if ( $publicinfo['action'] == 'add' && $publicinfo['productType'] !=  tbProduct::TYPE_CRAFT ){ ?>
  <div class="pull-right">
    <span class="text-muted">如果重新选择，页面信息可能会清空</span>
    <a href="<?php echo $this->createUrl('index');?>">重新选择</a>
  </div>
 <?php }?>
  您当前选择的类目：<strong class="text-warning"><?php echo implode(' &gt; ',$categorys);?></strong>
</div>
<form action="" class="form-horizontal" method="post" >
  <div><span class="h4">基本信息</span>(<span class="text-danger">*</span>表示该项必填)</div>
  <br>
  <div class="form-group">
    <label class="control-label col-md-2"><span class="text-danger">*</span>产品名称：</label>
    <div class="col-md-4">
      <div class="input-group title-group">
        <input type="text" name="p[title]" value="<?php echo $publicinfo['title'] ?>" class="form-control input-sm" required maxlength="60">
        <div class="input-group-addon">0/30</div>
      </div>
    </div>
  </div>
  <?php if( $publicinfo['productType'] ==  tbProduct::TYPE_CRAFT ){ ?>
<?php $this->beginContent('_craft',array('data'=>$publicinfo,'craft'=>$craft));$this->endContent();?>
<?php }else{ ?>
  <div class="form-group">
    <label class="control-label col-md-2"><span class="text-danger">*</span>产品编号：</label>
    <div class="col-md-2">
      <input type="text" name="p[serialNumber]" value="<?php echo $publicinfo['serialNumber'] ?>" class="form-control input-sm"  <?php if( $publicinfo['action'] == 'edit' ){ echo 'disabled';}?>/>
    </div>
  </div>
  <?php }?>
  <?php
   //产品规格
	$this->beginContent('_spec',array('speclist'=>$speclist ,'specs'=>$publicinfo['specs'],'colorgroups'=>$colorgroups) );
	$this->endContent();

	//产品属性
	$this->beginContent('_productattr',array('attrlist'=>$attrlist ,'attrs'=>$publicinfo['attrs'],'attrgroups'=>$attrgroups) );

	$this->endContent(); ?>
  <h3 class="h4">主图及产品详情</h3>
  <div class="form-group">
    <label class="control-label col-md-2"><span class="text-danger">*</span>产品图片：</label>
    <div class="control-div col-md-offset-2">
      <p class="text-muted">请上传1M以内的实拍产品图片，默认第一张为主图，建议图片大小为750*750px。</p>
      <ul class="linst-unstyled">
        <?php for($i=0;$i<5;$i++){ $pic = '';?>
        <li class="uploader uploader-image">
          <button class="image-wrap" type="button">
            <?php if( array_key_exists( $i,$publicinfo['pictures'] )){
				$pic = $publicinfo['pictures'][$i];
			?>
              <img src="<?php echo $this->img(false).$pic ;?>" alt="" width="80" height="80"><span class="bg"></span><span>重新上传</span>
              <?php }?>
          </button>
          <input type="hidden" name="pictures[]" value="<?php echo $pic ; ?>"/>
        </li>
       <?php } ?>
      </ul>
    </div>
  </div>
  <div class="form-group details">
    <label class="control-label col-md-2"><span class="text-danger">*</span>产品描述：</label>
    <div class="col-md-8 details">
      <ul class="nav nav-tabs">
        <li class="active" for="pc"><a href="javascript:">电脑端</a></li>
        <li for="mobile"><a href="javascript:">手机端</a></li>
      </ul>
      <br>
      <div class="pc">
        <p class="text-muted">插入图片的规格：宽度小于750px</p>
        <div>
          <textarea id="pc" name="p[content]" class="content"><?php echo $publicinfo['content'] ?></textarea>
        </div>
      </div>
      <div class="mobile hide">
        <p class="text-muted">插入图片的规格：宽度小于600px</p>
        <div>
         <textarea id="mobile" name="p[phoneContent]"><?php echo $publicinfo['phoneContent'] ?></textarea>
        </div>
      </div>
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2">产品测试：</label>
    <div class="col-md-8">
        <p class="text-muted">插入图片的规格：宽度小于750px</p>
        <div>
          <textarea id="testresults" name="p[testResults]" class="testResults"><?php echo $publicinfo['testResults'] ?></textarea>
        </div>
    </div>
  </div>
  <?php if( $canSave ):?>
  <div class="form-group">
    <div class="control-div col-md-offset-2">
      <button class="btn btn-success" type="submit">保存信息</button>
    </div>
  </div>
  <?php endif;?>
</form>

<script>
 seajs.use('statics/app/product/create/js/info.js');

<?php if( !$canSave ):?>
var a = document.getElementsByTagName("input");
for(var i = 0; i< a.length; i++) {
    a[i].setAttribute("disabled","true");
}
var $form = $('.content-wrap form');
$form.find('.webuploader-element-invisible').prop('disabled', true);
<?php endif;?>
</script>