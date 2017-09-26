<?php $this->beginContent('//layouts/_error');$this->endContent();?>
<?php $this->beginContent('//layouts/_success');$this->endContent();?>
<?php $this->beginContent('view',array('order'=>$order));$this->endContent();?>
<form method="post" class="">
  <div class="panel panel-default">
        <div class="panel-heading clearfix">
           取消理由： <textarea name="reason" class="form-control" style="width:400px"></textarea>
        </div>
    </div>

    <div class="text-center">
        <input type="submit" value="取消采购单" class="btn btn-success" />
    </div>
</form>