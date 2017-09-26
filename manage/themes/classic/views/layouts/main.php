<!doctype html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="renderer" content="webkit|ie-stand|ie-comp">
    <title><?php echo $this->pageTitle;?></title>
    <meta name="keywords" content=""/>
    <meta name="description" content=""/>
    <link rel="stylesheet" href="/themes/classic/statics/libs/bootstrap/3.3.5/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="/themes/classic/statics/common/style.css"/>
    <script src="/themes/classic/statics/libs/seajs/2.3.0/sea.js"></script>
    <script src="/themes/classic/statics/libs/seajs/2.3.0/seajs-css.js"></script>
    <script src="/themes/classic/statics/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="/themes/classic/statics/libs/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script src="/themes/classic/statics/common/init.js"></script>
</head>
<body>
<?php if( Yii::app()->session->get('alertSuccess') ){ Yii::app()->session->remove('alertSuccess'); ?>
    <div class="alert alert-success alert-dismissible fade in do-success" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
        <strong>操作成功！</strong>
    </div>
    <script>setTimeout(function(){
            $('.do-success').find('.close').click();
        },3500);</script>
<?php } ?>

<div class="col-md-12 content-wrap">
    <?php
    $this->widget('application.widgets.whereis');

	if( isset($this->currentTitle) && !empty( $this->currentTitle ) ){
		$this->pageTitle =  $this->currentTitle;
	}
	
	if( isset( $this->currentLink ) && !empty( $this->currentLink ) ){
		$this->pageTitle =  CHtml::link( $this->pageTitle,$this->currentLink );
	}	
	
    if(!isset($hiddenTitle) || !$hiddenTitle) {
        echo CHtml::tag('h2', ['class'=>'h3'], $this->pageTitle);
    }
    ?>

    <?php echo $content;?>
</div>
</body>
</html>