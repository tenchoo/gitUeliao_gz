<link rel="stylesheet" href="/themes/classic/statics/app/warehouse/css/style.css" />
  <div class="panel panel-default search-panel">
   <div class="panel-body">
    <form role="search" class="pull-left form-inline" action="<?php echo $this->createUrl(Yii::app()->getController()->getAction()->id);?>">
     <div class="form-group">
      <input type="text" name="orderId" value="<?php echo $orderId;?>" placeholder="订单编号" class="form-control input-sm" />
	    <input type="text" name="singleNumber" value="<?php echo $singleNumber;?>" placeholder="产品编号" class="form-control input-sm">
     </div>
     <button class="btn btn-sm btn-default">查找</button>
    </form>
   </div>
  </div>
   <div class="clearfix well well-sm list-well">
   <div class="pull-left form-inline">
    <!-- <label class="checkbox-inline"> <input type="checkbox" class="checkedall"/> 全选 </label> -->
  <!--   <button class="btn btn-sm btn-default"  data-toggle="modal" data-target=".setarea-confirm" title="批量设置分拣区域" >设置分拣区域</button> -->
   </div>
   <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>
  <table class="table table-condensed table-bordered ">
  <colgroup><col width="50px"><col width="20%"><col width="20%"><col><col></colgroup>
   <thead>
    <tr>
	<td></td>
   <td>产品编号</td>
	   <td>订单编号</td>
     <td>下单时间</td>
     <td>配送方式</td>
     <td>需分拣数量</td>
     <td>辅助单位</td>
     <td>整卷</td>
     <td>零码</td>
     <td>操作</td>
    <!--  <td>分拣区域</td> -->
    </tr>
   </thead>
    <tbody>
	 <?php foreach(  $list as $val  ){ ?>
	 <tr>
	 <td><input type="checkbox" value="<?php echo $val['orderProductId'];?>" name="id[]"/></td>
    <td><?php echo $val['singleNumber'];?></td>
	  <td><?php echo $val['orderId'];?></td>
	  <td><?php echo $val['time'];?></td>
    <td><?php echo $val['method'];?></td>
    <td><?php echo $val['num'];?></td>
    <td><?php echo $val['unit']; echo $val['unitname'];?></td>
    <td><?php echo $val['int']; echo $val['unitname'];?></td>
    <td><?php echo $val['remainder'];?></td>
    <td><a herf="" class="btn btn-sm btn-default" data-toggle="modal" onclick="report('<?php echo $val['orderProductId'];?>',
              '<?php echo $val['singleNumber'];?>',
              '<?php echo $val['orderId'];?>',
              '<?php echo $val['positionId'];?>',
              '<?php echo $val['unit'];?>',
              '<?php echo $val['num'];?>',
               '<?php echo $val['name'];?>',
               '<?php echo $user;?>',
                '<?php echo  $val['memo'];?>',
                '<?php echo  $val['warehouse'];?>'
              )">分拣</a></td>
    </tr>
	 <?php }?>
	</tbody>
   </table>
   <br>
	<div class="clearfix well well-sm list-well">
   <div class="pull-left form-inline">
    <!-- <label class="checkbox-inline"> <input type="checkbox" class="checkedall"/> 全选 </label> -->
   <!--  <button class="btn btn-sm btn-default"  data-toggle="modal" data-target=".setarea-confirm" title="批量设置分拣区域" >设置分拣区域</button> -->
   </div>
   <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>
  <script>seajs.use('statics/app/warehouse/js/scheduling.js');</script>

 <div class="modal fade setarea-confirm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="exampleModalLabel">批量设置分拣区域</h4>
      </div>
      <div class="modal-body">
        <form action="" method="post">
			<input type="hidden" value="" name="ids"/>
			<input type="hidden" value="scheduling" name="action"/>
			<?php echo CHtml::dropDownList('positionId','',$areas,array('class'=>'form-control input-sm'))?>
		</form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
        <button type="button" class="btn btn-success">确定</button>
      </div>
    </div>
  </div>
</div>

  <!--分拣-->
  <div class="modal fade" id="report" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <form action="confirmSort" method='post'>
        <div class="modal-content">
          <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">分拣(<span class="title"></span>)</h4>
          </div>
          <div class="modal-body">
               <table class="table table-condensed table-bordered   import">
              <tbody>
                <input type="hidden" value="" name="positionIds"/>
                <input type="hidden" value="" name="orderProductId"/>
                <input type="hidden" value="" name="unit"/>
                <input type="hidden" value="" name="remark"/>
                <tr>
                    <td>分拣员：<span class="packUser"></span></td>
                    <td>发货仓库：<span class="warehouse"></span></td>
                </tr>
                 <tr>
                    <td>客户名称：<span class="name" ></span></td>
                    <td>订单号：<span class="order"></span></td>
                </tr>
                <tr>
                    <td>订单数量：<span class="num" ></span></td>
                    <td>辅助数量：<span class="unit" ></span>卷/米</td>
                </tr>
                <tr>
                   <td colspan="2" >特殊要求：<span class="memo" ></span></td>
                 </tr>
                 <tr>
                    <td><div class="col-sm-4">整料仓位：</div>
                    <div class="col-sm-8">
                    <div class="warehouse-list">
                      <select class="form-control input-sm cate1">
                      <option value="default">请选择</option>
                    </select>
                    <select class="form-control input-sm cate2">
                      <option value="default">请选择</option>
                    </select>
                    <select class="form-control input-sm cate3">
                      <option value="default">请选择</option>
                    </select>
                    <input type="hidden" name="positionId[0]" value="" />
                    </div>
                    </div></td>
                      <td>整料数量（卷）：<input type='text' value="" name="int"/></td>
                </tr>
                <tr>
                  <td>
                    <div class="col-sm-4">零码仓位：</div>
                    <div class="col-sm-8"> <div class="warehouse-list">
                      <select class="form-control input-sm cate1">
                      <option value="default">请选择</option>
                    </select>
                    <select class="form-control input-sm cate2">
                      <option value="default">请选择</option>
                    </select>
                    <select class="form-control input-sm cate3">
                      <option value="default">请选择</option>
                    </select>
                    <input type="hidden" name="positionId[1]" value="" />
                    </div>
                    </div>
                  </td>

                    <td>零码数量（米）：<input type='text' value="" name="remainder[0]"/></td>
                </tr>

              </tbody>
              </table>
          </div>

          <div class="modal-footer">
              <input class="btn btn-primary" type="submit" value="确定分拣">
              <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
          </div>

        </div>
      </form>
    </div>
  </div>
 <script>seajs.use('statics/app/warehouse/js/packingdetail.js');</script>

 <script>seajs.use('statics/app/warehouse/js/user.js');</script>
