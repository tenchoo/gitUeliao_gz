<?php if( $this->checkAccess('role/menu/addnew') ):?>
    <div class="panel panel-default search-panel">
        <div class="panel-body">
		<?php if( !is_null( $title ) ){ ?>
		<span><?php echo implode('>',$title);?></span>
		<?php } ?>
            <div class="pull-right">
			<?php if( !is_null( $fatherId )){ ?>
				<a class="btn btn-sm btn-default" href="<?php echo $this->createUrl('index',array('id' => $fatherId));?>">上一级菜单</a>
			<?php }?>

                <a class="btn btn-sm btn-default" href="<?php echo $this->createUrl('add',array('fatherId' => $id));?>">新建菜单</a>
            </div>
        </div>
    </div>
<?php endif; ?>
<table class="table table-condensed table-bordered table-hover">
    <thead>
    <tr>
		<td>menuId</td>
        <td>菜单名称</td>
        <td width="20%">类型</td>
        <td width="20%">路由</td>
        <td width="20%">状态</td>
        <td width="20%">操作</td>
    </tr>
    </thead>
    <tbody>
	 <?php foreach( $list as $item ): ?>
        <tr>
            <td><?php echo $item['id']?></td>
            <td><?php echo $item['title']?></td>
            <td><?php echo $item['type']?></td>
            <td><?php echo $item['route']?></td>
			<td><?php echo ($item['hidden']=='1')?'隐藏':'显示'?></td>
            <td>
		<?php if($item['type'] !='action'){ ?>
			<a href="<?php echo $this->createUrl('index',array('id' => $item['id']));?>">子菜单</a>
		<?php }?>
			<a href="<?php echo $this->createUrl('edit',array('id' => $item['id']));?>">编辑</a>
			<a href="#" class="del" data-toggle="modal" data-target=".del-confirm" data-id="<?php echo $item['id'] ?>" data-rel="<?php echo $this->createUrl('del');?>">删除</a>
			</td>
        </tr>
	<?php endforeach; ?>
    </tbody>
</table>
 <?php $this->beginContent('//layouts/_del');$this->endContent();?>