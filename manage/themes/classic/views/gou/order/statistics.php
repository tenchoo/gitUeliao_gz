<?php $this->beginContent('//layouts/_error2');$this->endContent();?>  
  <div class="panel panel-default search-panel">
   <div class="panel-body">
    <form role="search" class="pull-left form-inline" action="<?php echo $this->createUrl(Yii::app()->getController()->getAction()->id);?>">
     <div class="form-group">
      <div class="inline-block pull-left">
	  送货日期: <input id="t1" name="t1"  type="text" value="<?php echo $t1; ?>" readonly="readonly" maxlength="20"  class="form-control input-sm input-date" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd',maxDate:'#F{$dp.$D(\'t2\')}'})"/>
      -
        <input id="t2" name="t2" type="text" readonly="readonly" maxlength="20"  value="<?php echo $t2; ?>"  class="form-control input-sm input-date" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd',minDate:'#F{$dp.$D(\'t1\')}',maxDate:'%y-%M-%d'})"/>		
		<?php echo CHtml::dropDownList('deliverymanId',$deliverymanId,$mems,array('class'=>'form-control input-sm','empty'=>'送货员'))?>
	 </div>
     </div>
     <button class="btn btn-sm btn-default">查找</button>
    </form>

   </div>
  </div>
  <table class="table table-condensed table-bordered">
   <colgroup><col width="50px"><col><col><col></colgroup>
   <thead>
    <tr>
	 <td>序号</td>
     <td>日期</td>
	 <td>已送订单数</td>
	 <td>已送件数</td>
    </tr>
   </thead>
  <tbody>
   <?php
   foreach(  $list as  $index=>$val  ):
   ?>
    <tr>
     <td><?php echo $index+1;?></td>
	 <td><?php echo $val['t'];?></td>
	 <td><?php echo $val['c'];?></td>
	 <td><?php echo $val['total'];?></td>
    </tr>
   <?php endforeach;?>
   </tbody>
  </table>