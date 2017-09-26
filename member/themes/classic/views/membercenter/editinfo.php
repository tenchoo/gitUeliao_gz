<link rel="stylesheet" href="/modules/uploader/css/style.css"/>
<link rel="stylesheet" href="/app/member/setting/css/style.css"/>
	<div class="pull-right frame-content">
	  <?php //if( $info['isCheck'] =='2' ){ ?>
	  <!--div>
	 信息审核不通过<br/>
	 理由：<?php //echo $info['checkReason']?>
	  </div-->
	   <?php //} ?>
		<div class="frame-tab profile">
      <ul class="clearfix list-unstyled frame-tab-hd">
        <li class="active"><a href="javascript::">基本资料</a></li>
		<?php
		if(Yii::app()->user->getState('usertype') != 'saleman' || $info['memberId']!=Yii::app()->user->id){ ?>
        <li><a href="<?php echo $detailurl?>">详细资料</a></li>
		<?php }?>
      </ul>
      <div class="frame-tab-bd frame-tab-bd-active">
	  <?php if( $info['memberId'] == Yii::app()->user->id){ ?>
      	<p>亲爱的<strong><?php echo $info['nickName']?></strong>，填写真实的资料，有助于联系到你哦。</p>
	  <?php }?>
			  <div class="form-horizontal">
			    <form action="" method="post">
			      <div class="form-group face-group">
              <label class="control-label">当前头像：</label>
              <span class="uploader uploader-image">
                <button type="button" class="image-wrap">
                <?php if($info['icon']) {?>
                	<img src="<?php echo $this->imageUrl($info['icon'],100);?>" alt="" width="80" height="80">
                	<span class="bg"></span>
                	<span>重新上传</span>
                <?php }?>
                </button>
              </span>
              <input type="hidden" name="Editinfo[icon]" value="<?php echo $info['icon']?>"/>
            </div>
			      <div class="form-group">
			        <label class="control-label" for="nickname"><span class="text-red">*</span>昵称：</label>
			        <input type="text" name="Editinfo[nickName]" class="form-control input-xs" id="nickname" data-help="不能为空" value="<?php echo $info['nickName']?>" maxlength="11"/>
			      </div>
			      <div class="form-group">
			        <label class="control-label" for="qq"><span class="text-red">*</span>QQ号绑定：</label>
			        <input type="text" name="Editinfo[qq]" class="form-control input-xs" id="qq" data-help="不能为空" value="<?php echo $info['qq']?>" maxlength="20"/>
			      </div>
			      <div class="form-group">
			        <label class="control-label" for="name">真实姓名：</label>
			        <input type="text" name="Editinfo[username]" class="form-control input-xs" id="name" value="<?php echo $info['username']?>" maxlength="10" />
			      </div>
			      <div class="form-group">
			        <label class="control-label">性别：</label>
			        <label class="radio-inline"><input type="radio" name="Editinfo[sex]" value="0" <?php if( $info['sex'] =='0' ) { echo 'checked="checked"';}?>/>男</label>
			        <label class="radio-inline"><input type="radio" name="Editinfo[sex]" value="1" <?php if( $info['sex'] =='1' ) { echo 'checked="checked"';}?>/>女</label>
			      </div>
			      <div class="form-group">
			        <label class="control-label" for="birthday">生日：</label>
			        <input type="text" name="Editinfo[birthdate]" class="form-control input-xs input-date" id="birthday" readonly value="<?php echo ($info['birthdate']=='0000-00-00')?'':$info['birthdate']?>"/>
			      </div>
			      <div class="form-group form-group-offset">
			        <button class="btn btn-success btn-xs" type="submit" data-loading="保存中...">保存</button>
			      </div>
			    </form>
			  </div>
    	</div>
    </div>
	</div>
<script>
  seajs.use('app/member/setting/js/profile.js');
</script>