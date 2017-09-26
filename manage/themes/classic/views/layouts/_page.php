   <div class="pull-right">
    <?php
     $this->widget('widgetLinkpager', array(
        'pages' => $pages,
        'selectedPageCssClass' => 'active', //当前页的class
        'hiddenPageCssClass' => 'disabled', //禁用页的class
        'header' => '', //分页前显示的内容
        'maxButtonCount' => 10, //显示分页数量
        'htmlOptions' => array('class' => 'pagination pagination-sm'),
        'firstPageLabel' => '首页',
        'nextPageLabel' => '下一页',
        'prevPageLabel' => '上一页',
        'lastPageLabel' => '末页',
        )
    );
    ?>
   </div>