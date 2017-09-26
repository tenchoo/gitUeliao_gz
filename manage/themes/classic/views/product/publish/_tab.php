<?php
	$tabs = array(
		'edit'=>array('title'=>'产品基本信息','action'=>'list','param'=>array(),'access'=>false),
		'voices'=>array('title'=>'语音介绍','action'=>'index','param'=>array('step'=>'voices'),'access'=>true),
		'procurement'=>array('title'=>'采购信息','action'=>'procurement','param'=>array('step'=>'voices'),'access'=>true),
		'safetystock'=>array('title'=>'安全库存设置','action'=>'procurement','param'=>array('type'=>'safetystock'),'access'=>true),
		'deposit'=>array('title'=>'订金比例设置','action'=>'saleinfo','param'=>array('type'=>'deposit'),'access'=>true),
		'saleinfo'=>array('title'=>'价格设置','action'=>'saleinfo','param'=>array(),'access'=>true),
		'glass'=>array('title'=>'呆滞等级','action'=>'glass','param'=>array(),'access'=>true),
		'glasstime'=>array('title'=>'呆滞时长','action'=>'glass','param'=>array('type'=>'glasstime'),'access'=>true),
		'adjustratio'=>array('title'=>'调整单比例','action'=>'adjustratio','param'=>array(),'access'=>true),
		'packarea'=>array('title'=>'默认分拣区域','action'=>'packarea','param'=>array(),'access'=>true),
		);
?>
<ul class="nav nav-tabs">
<?php foreach ( $tabs as $key=>$val ){
	if( $val['access'] ){
		$action = '/product/publish/'.$val['action'];
		if( !$this->checkAccess( $action ) ){
			continue;
		}
	}

	if( $productId && $key != $active ){
		$val['param']['id'] = $productId;
		$url = $this->createUrl( $val['action'] ,$val['param'] );
	}else{
		$url = 'javascript:';
	}
?>
	 <li <?php if( $key == $active ){ echo 'class="active"';}?>><a href="<?php echo $url;?>"><?php echo $val['title']?></a></li>
<?php }?>
</ul>
<br>