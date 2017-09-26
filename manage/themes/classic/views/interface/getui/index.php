<?php $this->beginContent('//layouts/_error');$this->endContent();?>
<form class="form-horizontal" method="post" action="">
    <div class="form-group">
        <label class="control-label col-md-2"><span class="text-danger">*</span> 接口地址：</label>
        <div class="col-md-4">
            <input type="text" name="data[host]" value="<?php echo $data['host']; ?>" class="form-control input-sm" />
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-md-2"><span class="text-danger">*</span> AppID：</label>
        <div class="col-md-4">
            <input type="text" name="data[appID]" value="<?php echo $data['appID']; ?>" class="form-control input-sm" />
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-md-2"><span class="text-danger">*</span>AppKey：</label>
        <div class="col-md-4">
            <input type="text" name="data[appKey]" value="<?php echo $data['appKey']; ?>" class="form-control input-sm" />
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-md-2"><span class="text-danger">*</span>AppSecret：</label>
        <div class="col-md-4">
            <input type="text" name="data[appSecret]" value="<?php echo $data['appSecret']; ?>" class="form-control input-sm" />
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-md-2"><span class="text-danger">*</span>MasterSecret：</label>
        <div class="col-md-4">
            <input type="text" name="data[masterSecret]" value="<?php echo $data['masterSecret']; ?>" class="form-control input-sm" />
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-offset-2 col-md-10">
            <button class="btn btn-success">保存</button>
        </div>
    </div>
</form>
