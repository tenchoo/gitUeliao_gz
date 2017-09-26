<link rel="stylesheet" href="/app/cart/css/style.css"/>
<div class="cart container">
  <div class="cart-step">
    <ul class="list-unstyled">
      <li class="first done done-a"><span class="one">1.查看购物车</span></li>
      <li class="cur"><span class="two">2.确认订单信息</span></li>
      <li><span class="three">3.付款</span></li>
      <li><span class="four">4.确认收货</span></li>
      <li class="last"><span class="five">5.评价</span></li>
    </ul>
  </div>
  <?php if( $userType == tbMember::UTYPE_SALEMAN ){ ?>
  <div class="select-client">
      <h2 class="c-hd">选择客户</h2>
      <div class="bd c-box">
        <label>客户名称：</label>
        <input type="text" class="form-control input-xs" data-suggestion="searchMember" data-api="/ajax" data-search="action=search&optype=member&keyword=%s">
      </div>
    </div>
	<?php } ?>
  <div class="ajax-wrap">
  <?php $this->beginContent('_confirm',array('userType'=>$userType,'address'=>$address));?>
  <?php $this->endContent(); ?>
  </div>
  <form method="post" action="" class="order-form">
		<input type="hidden" name="order[addressId]" value="<?php echo isset($defaultAddress)?$defaultAddress:''?>"/>
		 <?php if($userType == tbMember::UTYPE_SALEMAN ) { ?>
		<input type="hidden" name="order[memberId]" value="<?php echo isset($memberId)?$memberId:''?>"/>
		<?php } ?>
		<?php if(isset($productId)){
			$textName = ( $type == 'tail')?'tailId':'productId';
		?>
		<input type="hidden" name="<?php echo $textName;?>" value="<?php echo $productId;?>"/>
		<?php } ?>
	<div class="frame-list-bd firm-order">
  <?php
  $otypes = array(
		'0' =>array('title'=>'现货','class'=>'now','tag'=>'spot'),
		'1' =>array('title'=>'预订','class'=>'future','tag'=>'booking'),
		'3' =>array('title'=>'尾货','class'=>'tail','tag'=>'tail'),
		);
  foreach ( $list as $key=>$val ){?>
    <div class="c-hd"><?php echo $otypes[$key]['title'];?>订单信息</div>
		<table class="order-<?php echo $otypes[$key]['class'];?>">
			<thead>
        <tr>
          <th class="title">商品信息</th>
          <th width="140">单价（元）</th>
          <th width="80">数量</th>
          <th width="110">小计（元）</th>
		  <?php if( $key == '0' ){ ?>
          <th width="80">是否赠板</th>
		   <?php }else if( $key == '1' ){ ?>
		  <th width="80">订金比例</th>
		  <?php } ?>
        </tr>
      </thead>
		 <tbody class="list-page-body">
<?php foreach ((array)$val['list'] as $pkey=>$pval){
		if( array_key_exists ( 'cartId', $pval ) ) {
?>
			<input type="hidden" name="order[<?php echo $key;?>][product][<?php echo $pkey;?>][cartId]" value="<?php echo $pval['cartId'];?>" />
			<input type="hidden" name="order[<?php echo $key;?>][product][<?php echo $pkey;?>][num]" value="<?php echo $pval['num'];?>" />
<?php }else{
		if ( $type == 'tail') {
			if( $pval['saleType']!= 'whole' ){
?>
			<input type="hidden" name="cart[<?php echo $pval['singleNumber'];?>]" value="<?php echo $pval['num'];?>" />

<?php	 }}else{ ?>
			<input type="hidden" name="order[<?php echo $key;?>][product][<?php echo $pkey;?>][stockId]" value="<?php echo $pval['stockId'];?>" />
			<input type="hidden" name="order[<?php echo $key;?>][product][<?php echo $pkey;?>][num]" value="<?php echo $pval['num'];?>" />
<?php 	}} ?>

		<tr class="list-body-bd" data-id="<?php echo $pval['productId']; ?>" data-price="<?php echo $pval['sumprice']*100;?>" <?php if($key=='1'){?>data-deposit="<?php echo $pval['depositRatio']/100;?>"<?php }?>>
      <td class="title">
        <div class="c-img product-img pull-left">
  			 <a href="<?php echo $pval['url']?>" target="_blank">
  				<img src="<?php echo $this->imageUrl($pval['mainPic'],50);?>" width="50" height="50" alt="<?php echo $pval['title'];?>" />
  				</a>
        </div>
        <div class="product-title">
  				<a href="<?php echo $pval['url']?>" target="_blank">
  					<?php echo '【'.$pval['serialNumber'].'】'.$pval['title'];?>
  				</a>
        </div>
        <p class="text-minor"><?php echo $pval['relation'];?></p>
      </td>
      <td class="price"><?php echo $pval['realPrice']?></td>
      <td class="num"><?php echo Order::quantityFormat( $pval['num'] );?></td>
      <td class="text-warning total"><?php echo Order::priceFormat($pval['sumprice'] );?></td>
	   <?php if( $key == '0' ){ ?>
 	  <td>
	  <?php if(  $userType == tbMember::UTYPE_SALEMAN ) { ?>
		<input class="isfree" type="checkbox" name ="order[<?php echo $key;?>][product][<?php echo $pkey;?>][isSample]" value="1" <?php if($pval['num']>=5){echo 'disabled';}?>/>
	  <?php }else{ ?>
		  <input type="checkbox" disabled/>
	  <?php }?>
	  </td>
	   <?php }else if( $key == '1' ){ ?>
	   <td><?php echo $pval['depositRatio'];?> % </td>
	    <?php } ?>
    </tr>
    <?php }?>
      <tr class="delivery">
        <td colspan="<?php echo ($key=='3')?'4':'5';?>" data-key="<?php echo $key;?>">
          <span><span class="text-red">*</span>交货日期：</span>
          <input name="order[<?php echo $key;?>][batches][0][exprise]" type="text" class="form-control input-xs int-only input-date" readonly value="<?php echo date('Y-m-d');?>" />
          <span>备注：</span>
          <input name="order[<?php echo $key;?>][batches][0][remark]" type="text" class="form-control input-xs" />
          &nbsp;
          <a href="javascript:" class="text-link add">添加</a>
        </td>
      </tr>
      <?php if( $userType == tbMember::UTYPE_SALEMAN && $key =='0'  ) { ?>
        <tr>
          <td colspan="5">
            <div class="date pull-left">
              <label class="checkbox-inline">
                <input type="checkbox" name ="order[0][iskeep]" value="1"/>需留货<span class="text-minor">（从下单起可留货<?php echo $keeyday?>天）</span><span class="hide">将留货到<?php echo date("Y-m-d",strtotime("$keeyday day")) ;?></span>
              </label></div>
            </td>
        </tr>
      <?php } ?>
		   </tbody>
      <tfoot>
        <tr class="list-body-foot">
          <td colspan="<?php echo ($key=='3')?'4':'5';?>">
            <div class="message pull-left">
              <span>订单留言：</span><textarea class="textarea-box" name="order[<?php echo $key;?>][memo]"></textarea>
            </div>
            <div class="price-list text-right pull-right">
      			  配送方式：
      			  <?php echo CHtml::dropDownList('order['.$key.'][deliveryMethod]','',$deliveryMethod,array('class'=>'form-control input-xs'))?>
    			   运费：
    			    <input name="order[<?php echo $key;?>][freight]" type="text" class="form-control input-xs price-only" value="0.00" data-price="0" maxlength="8"/></span>元</span>
              商品总额：<span data-price="<?php echo $val['totalPrice']*100;?>"><?php echo  Order::priceFormat($val['totalPrice'] );?></span>元
              <br>
              小计：<strong class="text-warning total" data-price="<?php echo $val['totalPrice']*100;?>"><?php echo  Order::priceFormat($val['totalPrice'] );?></strong>元
			  <?php if( $key == '1' && $val['deposit']>0  ){ ?>
			  : 其中订金：<strong class="text-warning deposit" data-price="<?php echo $val['deposit']*100;?>"><?php echo  Order::priceFormat($val['deposit'] );?></strong>元，尾款：<strong class="text-warning balance" data-price="<?php echo $val['balanceDue']*100;?>"><?php echo  Order::priceFormat($val['balanceDue'] );?></strong>元
			  <?php }?>
            </div>
          </td>
        </tr>
      </tfoot>
      </table>
      <br>
	<?php } ?>

 <?php $this->beginContent('_pay',array('userType'=>$userType,'payModels'=>$payModels));?>
  <?php $this->endContent(); ?>

 <?php $this->beginContent('_warehouse',array('warehouseList'=>$warehouseList,'defaulthouse'=>$defaulthouse));?>
  <?php $this->endContent(); ?>
  
  <div class="text-right submit-info">
    <?php $keys = array_keys( $list );
	if( count( $keys )>1 ) { ?>
		选择付款订单：
	<?php foreach ( $keys as $key ){ ?>
	 <label class="checkbox-inline"><input name="order[<?php echo $otypes[$key]['tag'];?>]" checked value="1" type="checkbox"><span><?php echo $otypes[$key]['title'];?>订单</span></label>
	<?php } }else{ ?>
		<input type="hidden" name ="order[<?php echo $otypes[$keys[0]]['tag'];?>]" checked value="1"/>
    <?php } ?>
    实付款 <span class="text-warning"><?php echo Order::priceFormat( $realToalPrice );?></span>元
    <br>
    <br>
	<?php if( Yii::app()->getController()->getAction()->id !='buynow'){?>
	 <a href="<?php echo $this->createUrl('index');?>">&lt;&lt;返回购物车修改</a>
	<?php }?>
    <button  class="btn btn-warning" type="submit">提交订单</button>
  </div>
  </div>
	 </form>
<script type="text/html" id="delivery">
<tr class="delivery">
  <td colspan="{{colspan}}" data-key="{{key}}">
    <span><span class="text-red">*</span>交货日期：</span>
    <input name="order[{{key}}][batches][{{t}}][exprise]" type="text" class="form-control input-xs int-only input-date" readonly />
    <span>备注：</span>
    <input name="order[{{key}}][batches][{{t}}][remark]" type="text" class="form-control input-xs" />
    &nbsp;
    <a href="javascript:" class="text-link add">添加</a>
    <a href="javascript:" class="text-link del">删除</a>
  </td>
</tr>
</script>
</div>
<br><br>
<script type="text/html" id="item">
<li data-addressid="{{addressId}}">
  <a class="modify pull-right" href="#" data-href="/ajax?action=address&optype=getinfo&id={{addressId}}">修改</a>
  {{isDefault}}
  <label class="radio-inline"><input class="radio" name="addressId" value="{{addressId}}" type="radio">
  <span>{{cityinfo}}{{address}}</span> <em>( {{name}} 收 ){{mobile}}</em></label>
</li>
</script>

<script type="text/html" id="list">
<div class="clearfix address-list">
  <ul class="list-unstyled">
    {{each list}}
    <li data-addressid="{{$value.addressId}}">
      <a class="modify pull-right" href="#" data-href="/ajax?action=address&optype=getinfo&id={{$value.addressId}}">修改</a>
      {{$value.isDefault}}
      <label class="radio-inline"><input class="radio" name="addressId" value="{{$value.addressId}}" type="radio">
      <span>{{$value.cityinfo}}{{$value.address}}</span> <em>( {{$value.name}} 收 ){{$value.mobile}}</em></label>
    </li>
    {{/each}}
  </ul>
  {{add}}
</div>
</script>

<script type="text/html" id="address">
<input type="hidden" name="action" value="address"/>
<input type="hidden" name="optype" value="edit"/>
<input type="hidden" name="memberId" value="{{memberId}}"/>
<input type="hidden" name="address[addressId]" value="{{addressId}}"/>
<div class="form-group">
  <label class="control-label" for="name"><span class="text-red">*</span>收货人姓名：</label>
  <input name="address[name]" class="form-control input-xs" id="name" type="text" value="{{name}}">
</div>
<div class="form-group">
  <label class="control-label" for="address"><span class="text-red">*</span>省份：</label>
  <div class="inline-block area-select">
    <select name="province" class="form-control input-xs province">
      <option value="default">请选择省份</option>
    </select>
    <select name="city" class="form-control input-xs city">
      <option value="default">请选择市</option>
    </select>
    <select name="county" class="form-control input-xs county">
      <option value="default">请选择区/县</option>
    </select>
    <input type="hidden" name="address[areaId]" class="form-control input-xs" value="{{areaId}}" />
  </div>
</div>
<div class="form-group">
  <label class="control-label" for="street-address"><span class="text-red">*</span>地址：</label>
  <input name="address[address]" class="form-control input-xs" id="street-address" type="text"  value="{{address}}">
</div>
<div class="form-group">
  <label class="control-label" for="phone"><span class="text-red">*</span>手机：</label>
  <input name="address[mobile]" class="form-control input-xs" id="phone" type="text" value="{{mobile}}">
</div>
<div class="form-group">
  <label class="control-label" for="landline">固定电话：</label>
  <input name="address[tel]" class="form-control input-xs" id="landline" type="text"  value="{{tel}}">
</div>
<div class="form-group">
  <label class="control-label" for="zip-code">邮政编码：</label>
  <input name="address[zip]" class="form-control input-xs" id="zip-code" type="text" value="{{zip}}">
</div>
<div class="form-group form-group-offset">
  <button class="btn btn-warning btn-xs" type="submit">保存信息</button>
</div>
</script>
<?php if( $error =$this->getError()){ ?>
<script>
  seajs.use('modules/dialog/js/dialog.js',function(dialog){
    dialog.alert('<?PHP echo $error; ?>',{type:'error'});
  });
</script>
<?PHP } ?>
<script>
  seajs.use('app/cart/js/firmorder.js');
</script>