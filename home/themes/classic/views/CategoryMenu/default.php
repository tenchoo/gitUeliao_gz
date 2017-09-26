<ul class="sub-cate list-unstyled">
	      <?php
	      $is_current = false;
	      $listUrl = "default/index";
	      foreach( $categorys as $category ):
	      	$active1th = "";
		      if( $cur_level1 == $category['categoryId'] ) {
		      	$is_current = true;
		      	$active1th = "cate-1th-active";
		      }
	      ?>
	      	  <li class="cate-1th <?php echo $active1th;?>">
	          <div class="cate-1th-hd">
	            <?php echo $this->createUrl('<h3>'.$category['title'].'</h3>',array('c'=>$category['categoryId']));?>
	            <a href="javascript:" class="arr"></a>
	          </div>
	          <ul class="cate-1th-bd list-unstyled">
	          	<?php
	          	if( isset($category['childrens']) ):
	          	foreach ($category['childrens'] as $level2):
		          	$active2th = "";
		          	if( $is_current && $cur_level2==$level2['categoryId'] ) {
		          		$active2th = "cate-2th-active";
		          		$active = 0;
		          	}
	          	?>
	            <li class="cate-2th <?php echo $active2th;?>">
	              <div class="cate-2th-hd">
	                <a href="javascript:" class="arr"></a>
	                <h4><?php echo $this->createUrl($level2['title'],array('c'=>$level2['categoryId']));?></h3>
	              </div>
	              <ul class="cate-2th-bd list-unstyled">
	              	<?php
		          	if( isset($level2['childrens']) ):
		          	foreach ($level2['childrens'] as $level3):
		          	$active3th = array();
		          	
		          	if( $cur_level3==$level3['categoryId']) {
		          		$active3th = array("class"=>"active");
		          	}
		          	echo CHtml::tag('li',$active3th,false,false);
		          	echo $this->createUrl($level3['title'],array('c'=>$level3['categoryId']));?></li>
	              	<?php endforeach;endif;?>
	              </ul>
	            </li>
	            <?php
	            endforeach;
	            endif;
	            ?>
	            </ul>
	        <?php
	        endforeach;
	        ?>
	      </ul>