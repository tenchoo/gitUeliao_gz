<link rel="stylesheet" href="/app/home/css/style.css" />
  <div class="logo-search">
	  <div class="container">
	    <h1 class="pull-left logo"><a href="/"><img src="/app/home/image/logo.png" alt="" width="213" height="58"/></a></h1>
	    <div class="pull-left search">
	      <div class="clearfix search-form">
	        <form action="/default/product">
	          <input name="q" type="text" placeholder="请输入搜索关键字" class="pull-left search-q" value="<?php echo Yii::app()->request->getQuery('q');?>" />
            <div class="image-search"></div>
	          <button type="submit" class="pull-left search-btn">搜索</button>
	        </form>
	      </div>
	    </div>
	  </div>
  </div>
  <div class="nav">
    <div class="container">
      <div class="pull-left all-cate">
        <?php if($this->getRoute()!=='default/product' && $this->getRoute()!=='default/tail'){?>
        <h2>所有产品分类<span class="arr"></span></h2>
        <?php $this->widget('application.widgets.CategoryMenu');?>
        <?php } else {?>
        <h2>所有产品分类</h2>
        <?php } ?>
      </div>
      <ul class="pull-left nav-bd list-unstyled">
        <li <?php if($this->getRoute()==='default/index'){?>class="active"<?php } ?>><a href="/"><span>首页</span></a></li>
		    <li <?php if($this->getRoute()==='default/tail'){?>class="active"<?php } ?>><a href="/default/tail"><span>尾货</span></a></li>
      </ul>
    </div>
  </div>
  <script>seajs.use('modules/topbar/js/cate.js');</script>