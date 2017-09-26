<div class="pull-right frame-content">
  <div class="frame-tab">
    <ul class="clearfix list-unstyled frame-tab-hd">
      <li class="active">
        <a href="javascript:">设置打印机</a>
      </li>
    </ul>
  </div>
  <div class="frame-list-search">
    <form action="" method="post">
      绑定打印机：
      <?php echo CHtml::dropDownList('printerId', $printerId, $printers,array( 'empty'=>'请选择打印机' ,'class'=>'form-control input-xs')); ?>
      <button class="btn btn-xs btn-success">保存</button>
    </form>
  </div>
</div>
<script>
seajs.use('app/member/settlement/js/setprint.js');
</script>