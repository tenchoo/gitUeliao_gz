<link rel="stylesheet" href="/themes/classic/statics/app/role/css/style.css">
<style type="text/css">  
label.error {  
    color: orange;  
}  
</style> 
<?php $this->beginContent('//layouts/_error');$this->endContent();?>
<form method="post" class="form-horizontal" autocomplete="off" id="myForm">
<div class="form-group">
  <label class="control-label col-md-2"><span class="text-danger">*</span>员工姓名</label>
  <div class="col-md-4">
    <input type="text" class="form-control input-sm" name="form[username]" value="<?php echo $data['username'];?>" maxlength='12' autocomplete="off" >
  </div>
</div>
<div class="form-group">
    <label class="control-label col-md-2">超级管理员：</label>
    <div class="col-md-4">
      <label class="radio-inline"><input type="radio" name="form[isAdmin]" value="1" <?php if ($data['isAdmin'] == 1) {echo 'checked="checked"';}?>/>是</label>
      <label class="radio-inline"><input type="radio" name="form[isAdmin]" value="0" <?php if ($data['isAdmin'] == 0) {echo 'checked="checked"';}?>/>否</label>
    </div>
  </div>
<div class="form-group">
    <label class="control-label col-md-2">性别：</label>
    <div class="col-md-4">
      <label class="radio-inline"><input type="radio" name="form[gender]" value="0" <?php if ($data['gender'] == 0) {echo 'checked="checked"';}?>/>男</label>
      <label class="radio-inline"><input type="radio" name="form[gender]" value="1" <?php if ($data['gender'] == 1) {echo 'checked="checked"';}?>/>女</label>
    </div>
  </div>
<div class="form-group">
    <label class="control-label col-md-2"><span class="text-danger">*</span>所属部门：</label>
    <div class="col-md-4">
      <?php echo CHtml::dropDownList('form[departmentId]',$data['departmentId'],$departments,array('class'=>'form-control input-sm','empty'=>'请选择部门'))?>
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2"><span class="text-danger">*</span>职位：</label>
	 <div class="col-md-4">
	 <?php echo CHtml::dropDownList('form[depPositionId]',$data['depPositionId'],$positions,array('class'=>'form-control input-sm','empty'=>'请选择职位'))?>
	 </div>
  </div>
  <div class="form-group">
  <label class="control-label col-md-2"><span class="text-danger">*</span>手机/账号：</label>
  <div class="col-md-4">
    <input type="text" class="form-control input-sm" name="form[account]" value="<?php echo $data['account'];?>" maxlength="15" autocomplete="off">
  </div>
</div>
    <div class="form-group">
        <label class="control-label col-md-2"><span class="text-danger">*</span>打印机绑定：</label>
        <div class="col-md-4">
            <?php echo CHtml::dropDownList('form[printerId]', $data['printerId'], $printers,array('class'=>'form-control input-sm','empty'=>'请选择')); ?>
        </div>
    </div>
<?php $action = Yii::app()->getController()->getAction()->id;?>
<?php if( $action != 'view' ){
	if($action === 'edit') {?>
<div class="form-group">
  <label class="control-label col-md-2">修改密码：</label>
  <div class="col-md-4">
    <label class="radio-inline"><input class="pwd" type="radio" name="form[updatePasswd]" value="0" checked="checked" />否</label>
    <label class="radio-inline"><input class="pwd" type="radio" name="form[updatePasswd]" value="1" />是</label>
  </div>
</div>
	<?php }
	$options = ['id'=>'pwd'];
	if($action=='edit') {
		$options['style'] = "display:none";
	}
	echo CHtml::tag('div',$options,false,false);
?>
<div class="form-group">
  <label class="control-label col-md-2"><span class="text-danger">*</span>登录密码：</label>
  <div class="col-md-4">
    <input type="password" autocomplete="off" class="form-control input-sm" name="form[password]" maxlength="16"/>
  </div>
</div>
<div class="form-group">
  <label class="control-label col-md-2"><span class="text-danger">*</span>确认密码：</label>
  <div class="col-md-4">
    <input type="password" autocomplete="off" class="form-control input-sm" name="form[repassword]" maxlength="16" />
  </div>
</div>
</div>
<script>
var getRolesUrl = '<?php echo $this->createUrl( $this->getAction()->id,array('op'=>'getRoles') );?>';
var getpositionUrl = '<?php echo $this->createUrl( $this->getAction()->id,array('op'=>'getposition') );?>';
seajs.use('statics/app/role/js/edit.js');
</script>
<div class="form-group">
  <div class="col-md-offset-2 col-md-10">
    <button class="btn btn-success">保存</button>
  </div>
</div>
<?php }?>
</form>
<script type="text/template" id="regions">
  {{each data}}
    <option value="{{$value.depPositionId}}">{{$value.positionName}}</option>
  {{/each}}
</script>
<script type="text/template" id="roleGroups">
  {{each data}}
    <label class="checkbox-inline">
      <input value="{{$value.roleId}}" type="checkbox" name="form[roleids][]">
      {{$value.roleName}}
    </label>
  {{/each}}
</script>
<?php if( $action == 'view' ){ ?>
<script>
$('[name]').attr("disabled",true);
</script>
<?php }?>
<script>
$(".pwd[type=radio]").click(function(){
	if(this.value==1) {
		$("#pwd").show();
	}
	else {
		$("#pwd").hide();
	}
});
</script>
<script>seajs.use('statics/libs/jquery-validation/1.13.1/jquery.validate.js');</script>
<script>seajs.use('statics/libs/jquery-validation/1.13.1/localization/messages_zh.js');</script>
<script>seajs.use('statics/app/member/js/edit.js');</script>