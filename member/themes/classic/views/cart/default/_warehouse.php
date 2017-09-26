<div class="order-payModel">
  <div class="c-hd"></div>
  <div class="pay-way">
	   <label class="radio-inline">
		<span class="c-hd">分拣仓库:</span>
       <?php echo CHtml::dropDownList('order[packingWarehouseId]',$defaulthouse,$warehouseList,array('class'=>'form-control input-xs'))?>
	    </label>
		 <label class="radio-inline">
	   <span class="c-hd">发货仓库</span>
       <?php echo CHtml::dropDownList('order[warehouseId]','',$warehouseList,array('class'=>'form-control input-xs','empty'=>'请选择分类'))?>
	   </label>

   </div>
</div>