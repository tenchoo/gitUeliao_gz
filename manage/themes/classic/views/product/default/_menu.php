<?php
	$tabs = array(
		'edit'=>array('title'=>'编辑'),
		/* 'voices'=>array('title'=>'语音介绍'),
		'procurement'=>array('title'=>'采购信息'),
		'safetystock'=>array('title'=>'安全库存设置'),
		'deposit'=>array('title'=>'订金比例设置'), */
		'saleinfo'=>array('title'=>'价格设置'),
		);
?>
<?php foreach ( $tabs as $key=>$val ){
	$action = '/product/publish/'.$key;
	if( $this->checkAccess( $action ) ){
?>
<a href="<?php echo $this->createUrl($action,array('id' =>$productId));?>"><?php echo $val['title'];?></a><br>
<?php }}?>
<?php if( $type=='0' && $this->checkAccess( '/product/publish/addcraft' ) ){ ?>
	<a href="<?php echo $this->createUrl('/product/publish/addcraft',array('id' => $productId));?>">添加工艺产品</a>
<?php }?>