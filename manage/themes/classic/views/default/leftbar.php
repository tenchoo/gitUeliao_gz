<div class="side-menu">
    <div class="panel-group">
        <?php foreach($groups as $key => $group){?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title" data-toggle="collapse" data-target="#panel<?php echo $key;?>">
                    <?php echo $group->title;?>
                </h4>
            </div>

            <div class="panel-collapse list-group collapse in" id="panel<?php echo $key;?>">
                <?php foreach($group->childrens as $menu){
                    $url = empty($menu->url)? $this->createUrl($menu->route) : $url;
                    echo CHtml::link($menu->title, $url, ['class'=>'list-group-item','target'=>'frameContent']);
                }?>
            </div>
        </div>
        <?php } ?>
    </div>
</div>
<script>
var $menu = $('.side-menu');
$menu.on('click', '.list-group-item', function() {
  var $lis = $menu.find('.list-group-item');
  $lis.removeClass('active');
  $(this).addClass('active');
});
</script>