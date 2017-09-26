<link rel="stylesheet" href="/app/member/trade/css/style.css"/>
<div class="pull-right frame-content">
  <div class="frame-tab">
    <ul class="clearfix list-unstyled frame-tab-hd">
      <li class="active">
        <a href="javascript:">新增地址</a>
      </li>
    </ul>
  </div>
      <?php if( empty( $address ) ) {?>
      <div class="remind-box">友情提醒：最多可添加 <b class="text-warning">10</b>个收货地址</div>
      <?php } else {?>
      <div class="remind-box">友情提醒：还可添加 <b class="text-warning"><?php echo 10 - count($address);?></b>个收货地址</div>
      <?php } ?>
      <div class="frame-box addresses">
        <div class="bd form-horizontal">
          <form method="post" action="/address/save">
             <?php $this->renderPartial('_address', array('data'=>$data)); ?>
          </form>
        </div>
      </div>
      <?php if( !empty( $address ) ) {?>
      <div class="frame-list address-list">
        <div class="frame-list-bd">
          <table>
            <thead>
              <tr>
                <th width="60" class="title">默认</th>
                <th width="80">收货人</th>
                <th>收货地址</th>
                <th width="200">手机/固定号码</th>
                <th width="100">操作</th>
              </tr>
            </thead>
            <tbody class="list-page-body">
            <?php foreach ((array)$address as $val){?>
              <tr data-id="<?php echo $val->addressId;?>">
                <td class="check"><label class="radio-inline"><input type="radio" name="MemberAddress[addressId]" value="<?php echo $val->addressId?>" <?php if($val->isDefault==1){?> checked="checked"<?php }?>><?php if($val->isDefault==1){?><span>默认</span> <?php }?></label></td>
                <td><?php echo $val->name?></td>
                <td><div class="item"><?php echo tbArea::getAreaStrByFloorId( $val->areaId ).$val->address?></div></td>
                <td><p><?php echo $val->mobile?></p>
                  <p><?php echo $val->tel?></p></td>
                <td><a href="javascript:" class="text-link edit">编辑</a> <?php echo CHtml::link('删除',array('/address/delete','id'=>$val->addressId),array('class'=>'text-link del'))?></td>
              </tr>
              <?php }?>
            </tbody>
          </table>
        </div>
      </div>
      <?php } ?>
</div>
<br />
<script>seajs.use('app/member/trade/js/addresses.js');</script>