<div class="transaction-history">
  <table>
    <thead>
      <tr>
        <th width="15%">买家</th>
        <th width="25%">款式/型号</th>
        <th width="20%">数量(<?php echo $unitName;?>)</th>
        <th width="20%">价格</th>
        <th width="20%">成交时间</th>
      </tr>
    </thead>
    <tbody>
    <?php if( $list ){?>
		<?php foreach ( $list as $val ) { ?>
			<tr>
        <td class="text-muted">
          <p><?php echo $val['nickName'];?></p>
        </td>
        <td>
          <p class="text-muted"><?php echo $val['specifiaction'];?></p>
        </td>
        <td class="text-muted"><b><?php echo Order::quantityFormat( $val['num'] );?></b></td>
        <td class="text-muted">
          <p>&yen;<?php echo Order::priceFormat($val['price']);?></p>
        </td>
        <td>
          <p class="text-minor"><?php echo $val['createTime'];?></p>
        </td>
      </tr>
		<?php }}else{?>
		<tr><td colspan="5"><p class="text-center">暂无信息</p></td></tr>
		<?php }?>
              </tbody>
  </table>
</div>
<?php if( $list ){?>
<div class="text-right page">
  <?php $this->widget('application.widgets.PageNav', array( 
    'pages' => $pages,
  	'style'=> 'showNumberPlus',
    'maxLinkCount' => 10, //显示分页数量
    )
  );?>
</div>
<?php }?>