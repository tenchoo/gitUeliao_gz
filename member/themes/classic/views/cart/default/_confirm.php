  <div class="receiving-address">
    <h2 class="c-hd">选择收货地址</h2>
    <?php if($address){ $defaultAddress = '0';?>
    <div class="address-list">
      <ul class="clearfix list-unstyled">
      <?php foreach ($address as $val){ ?>
		  <li data-addressid="<?php echo $val['addressId']?>" <?php if( $val['isDefault'] ==1 ){$defaultAddress = $val['addressId']; echo 'class="select"' ;}?>>
        <a class="modify pull-right" href="#" data-href="/ajax?action=address&optype=getinfo&id=<?php echo $val['addressId']?>">修改</a>
        <?php if( $val['isDefault'] ==1 ){ echo '
        <span class="text-minor pull-right">（默认地址）</span>' ;}?>
			  <label class="radio-inline">
  				<input class="radio" name="addressId" <?php if( $val['isDefault'] ==1 ){ echo 'checked' ;}?> value="<?php echo $val['addressId']?>" type="radio"><span><?php echo $val['cityinfo'].$val['address']?></span> <em>( <?php echo $val['name']?>  收 )<?php echo $val['mobile']?></em>
  			 </label>
  		  </li>
        <?php }?>
      </ul>
	  <?php if( count($address) < '10') { ?>
      <div class="text-right">
        <a class="btn btn-cancel btn-xs add" href="javascript:">新增收货地址</a>
      </div>
	  <?php }?>
      <input type="hidden" name="memberId" value="<?php echo isset($memberId)?$memberId:''?>"/>
    </div>
    <?php }else{?>
    <div class="bd c-box">
		<div class="no-data">
	 <?php if( $userType == tbMember::UTYPE_SALEMAN && ( !isset($memberId) || empty( $memberId ) ) ){ ?>
		还未选择客户，请先选择客户
	 <?php }else{?>
	 还未添加收货地址，现在<a href="javascript:" class="btn btn-warning btn-xs add">新增收货地址</a>
	  <?php }?>
		</div>
    </div>
     <?php }?>
      <div class="address-form hide form-horizontal">
        <form method="post" action="<?php echo $this->createUrl('/ajax')?>">
        </form>
      </div>
  </div>
  
  