<!doctype html>
<html lang="zh-cn">
<head>
  <meta charset="utf-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="renderer" content="webkit|ie-stand|ie-comp">
  <title>优易料管理中心</title>
</head>
<?php
/**
 * 超级管理员显示导航栏(高度134)
 * 普通会员不显示导航栏(高度85)
 */
$height = 58;
if(Yii::app()->user->getState('isAdmin')==1) {
	$height = 94;
}
?>
<frameset rows="<?php echo $height;?>,*" frameborder="0">
    <frame src="<?php echo $this->createUrl('headbar');?>" scrolling="no" noresize name="headbar"/>
    <frameset cols="200,*" frameborder="0">
        <frame src="<?php echo $this->createUrl('leftbar');?>" scrolling="auto" noresize name="menubar"/>
        <frame src="<?php echo $this->createUrl('rightbar');?>" scrolling="auto" noresize name="frameContent"/>
    </frameset>
    <noframes>
        <body>
        优易料使用了框架技术，但是您的浏览器不支持框架，请升级您的浏览器以便正常访问。
        </body>
    </noframes>
</frameset>
</html>