<link rel="stylesheet" href="/app/member/trade/css/style.css"/>

<div class="pull-right frame-content order-check">
<?php $this->beginContent('_orderinfo',array('model'=>$model,'member'=>$member));$this->endContent();?>
<form  method="post" action="">
<div class="frame-box">
	 <div class="form-horizontal">
    <div class="form-group">
      <label class="control-label">审核结果：</label> 
      <label class="radio-inline"><input type="radio" name="data[state]" value="1" />同意取消订单</label> 
      <label class="radio-inline"><input type="radio" name="data[state]" value="2" />不同意取消订单</label>
    </div>
    <div class="form-group textarea-group">
      <label class="control-label"> 审核反馈：</label> 
      <textarea class="form-control" name="data[remark]"></textarea>
      <span class="text-warning"><?php echo $this->getError();?></span>
    </div>
    </div>
    </div>
    <br/>
  <div align="center">
	<button type="submit" class="btn btn-xs btn-success">提交信息</button>
 </div>
 </form>
</div>