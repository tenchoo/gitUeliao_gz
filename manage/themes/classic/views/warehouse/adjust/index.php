  <div class="panel panel-default search-panel">
   <div class="panel-body">
    <form role="search" class="pull-left form-inline" action="<?php echo $this->createUrl('index');?>">
	生成时间:  <input type="text" name="createTime1" value="<?php echo $createTime1; ?>" class="form-control input-sm input-date" id="starttime" readonly/>
		到 <input type="text" name="createTime2" value="<?php echo $createTime2; ?>" class="form-control input-sm input-date" id="endtime" readonly/>
     <div class="form-group">
      <input type="text" name="singleNumber" value="<?php echo $singleNumber;?>" placeholder="请输入单品编号" class="form-control input-sm" />
	  <input type="text" name="adjustId" value="<?php echo $adjustId;?>" placeholder="请输入调整单号" class="form-control input-sm" />
     </div>
     <button class="btn btn-sm btn-default">查找</button>
    </form>
   </div>
  </div>
  <div class="clearfix well well-sm list-well">
  <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>
  <table class="table table-condensed table-bordered table-hover">
   <thead>
    <tr>
     <td width="12%">调整单号</td>
	 <td>产品编号</td>
     <td width="12%">调整数量</td>
	 <td width="15%">操作人</td>
	 <td width="15%">调整时间</td>
     <td width="12%">操作</td>
    </tr>
   </thead>
    <?php foreach(  $list as $val  ){?>
	<tr class="list-bd">
   <td> <?php echo $val['adjustId'];?></td>
   <td> <?php echo $val['singleNumber'];?></td>
   <td> <?php echo $val['num'].' '.$val['unit'];?></td>
    <td> <?php echo $val['username'];?></td>
	 <td> <?php echo $val['createTime'];?></td>
    <td>
		<a href="<?php echo $this->createUrl('view',array('id'=>$val['adjustId']));?>">查看</a><br>		
	</td>
    </tr>
   <?php }?>
      </tbody>
  </table>
   <br>
  <div class="clearfix well well-sm list-well">
    <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>
<script>seajs.use('statics/app/order/js/applypricelist.js');</script>