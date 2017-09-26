<?php $this->beginContent('//layouts/_error');$this->endContent();?>
<form class="form-horizontal" method="post">
    <div class="form-group">
        <label class="control-label col-md-2"><span class="text-danger">*</span>账号：</label>
        <div class="col-md-4">
            <input type="hidden" name="form[id]" value="<?php echo Yii::app()->request->getQuery('id');?>" />
            <input type="text" readonly name="form[account]" value="<?php echo $seller->account;?>" class="form-control input-sm">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-md-2"><span class="text-danger">*</span>密码：</label>
        <div class="col-md-4">
            <input type="password" name="form[password]" value="" class="form-control input-sm">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-md-2"><span class="text-danger">*</span>确认密码：</label>
        <div class="col-md-4">
            <input type="password" name="form[comfirmpassword]" value="" class="form-control input-sm">
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-offset-2 col-md-10">
            <button type="submit" class="btn btn-success">保存</button>
        </div>
    </div>
</form>