  <div class="comments">
            <table>
              <colgroup><col width="500"><col width="180"><col width="88">
              </colgroup><tbody>
              <?php if( $list ){?>
			  <?php foreach ( $list as $val ) {?>
			  <tr>
                <td>
                  <p><?php echo $val['content'];?></p>
                  <p class="text-minor"><?php echo $val['createTime'];?></p>
                </td>
                <td>
                  <p class="text-muted"><?php echo $val['specifiaction'];?></p>
                </td>
                <td>
                  <p class="text-muted"><?php echo $val['nickName'];?></p>
                </td>
              </tr>
			  <?php if(!empty($val['reply'])){ ?>
			  <tr class="explanation">
                <td colspan="3">
                    <span class="text-tag">解释</span>
                    <p><?php echo $val['reply'];?></p>
                    <p class="text-minor"><?php echo $val['replyTime'];?></p>
                </td>
              </tr>
			  <?php } ?>
			<?php }}else{?>
		<tr><td colspan="3"><p class="text-center">暂无信息</p></td></tr>
		<?php }?>           
            </tbody></table>
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