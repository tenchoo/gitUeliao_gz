<div class="modal fade cancel-order-confirm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="exampleModalLabel">取消订单</h4>
      </div>
      <div class="modal-body">
        <form action="<?php echo $this->createUrl('cancle')?>" method="post">
					<input type="hidden" value="" name="orderId"/>
					<select name="closeReason">
					<?php foreach ( $reasons as $val ) { ?>
					<option value="<?php echo $val;?>"><?php echo $val;?></option>
					<?php }?>
					</select>
				</form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
        <button type="button" class="btn btn-success">确定</button>
      </div>
    </div>
  </div>
</div>