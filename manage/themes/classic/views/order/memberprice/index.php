<link rel="stylesheet" href="/themes/classic/statics/app/order/css/style.css" />
  <div class="panel panel-default search-panel">
   <div class="panel-body">
    <form role="search" class="pull-left form-inline" action="<?php echo $this->createUrl('index');?>">
	所有状态:  <?php echo CHtml::dropDownList('state',$state,$stateTitle,array('class'=>'form-control input-sm','empty'=>'所有状态'))?>
     <div class="form-group">
      <input type="text" name="keyword" value="<?php echo $keyword;?>" placeholder="请输入客户名称或单号" class="form-control input-sm" />
     </div>
     <button class="btn btn-sm btn-default">查找</button>
    </form>
   </div>
  </div>
  <div class="clearfix well well-sm list-well">
  <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>
  <table class="table table-condensed table-bordered">
   <colgroup><col/><col width="15%"/><col width="15%"/><col width="15%"/><col width="15%"/></colgroup>
   <thead>
    <tr>
     <td>产品信息</td>
	 <td>批发价</td>
     <td>客户名称</td>
     <td>状态</td>
     <td>操作</td>
    </tr>
   </thead>
   </table>
   <br>
   <?php foreach(  $list as $val  ){?>
     <table class="table table-condensed table-bordered order">
   <colgroup><col/><col width="15%"/><col width="15%"/><col width="15%"/><col width="15%"/></colgroup>
   <tbody>

	<tr class="list-hd">
    <td colspan="5">
		<span class="first">单号：<?php echo $val['applyPriceId'];?></span>
		<span>业务员：<?php echo $val['saleman'];;?></span>
		<span><?php echo $val['createTime'];?></span>
	  </td>
  </tr>
   <tr class="list-bd">
   <td>
   <div class="c-img pull-left">
     <a href="javascript:"><img src="<?php echo $this->img().$val['mainPic'];?>_50" alt="" width="50" height="50"/></a>
   </div>
	 <div class="product-title">【<?php echo $val['serialNumber'];?>】<?php echo $val['title'];?></div>
	 </td>
     <td> <?php echo Order::priceFormat($val['applyPrice']);?>元/<?php echo $val['unitName'];?></td>
	 <td><?php echo $val['companyname']?></td>
     <td style="color:#<?php switch ($val['state']) {
              case 0:
                $style = 'f60';
                break;
              case 2:
                $style = '288e00';
                break;
              case 3:
                $style = '999';
                break;
              default:
                $style = '444';
                break;
            }
            echo $style;
             ?>"><?php echo $val['stateTitle']?></td>
	 <td>
		<?php if( $val['state'] == '0' ){ ?>
			<a href="<?php echo $this->createUrl('check',array('id'=>$val['applyPriceId']));?>" >审核</a><br/>
		<?php } ?>
			<a href="<?php echo $this->createUrl('view',array('id'=>$val['applyPriceId']));?>" >查看</a><br/>
	</td>
    </tr>
	  </tbody>
  </table>
  <br>
   <?php }?>

  <div class="clearfix well well-sm list-well">
    <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>
<?php $this->beginContent('//layouts/_del');$this->endContent();?>