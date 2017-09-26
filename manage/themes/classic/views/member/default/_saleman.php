<div class="modal fade distribution-confirm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="exampleModalLabel">分配业务员</h4>
      </div>
      <div class="modal-body">
        <form action="<?php echo $this->createUrl('dodistribution')?>" method="post">
					<input type="hidden" value="" name="memberId"/>
					<?php foreach($saleList as $key=>$val ){ ?>
					 <div class="radio">
		         <label class="radio-inline"><input type="radio" name="userId" value="<?php echo $key;?>"/><?php echo $val;?></label>
           </div>
          <?php }?>
		</form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
        <button type="button" class="btn btn-success">确定</button>
      </div>
    </div>
  </div>
</div>