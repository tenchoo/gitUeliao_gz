define(function(require, exports, module) {
  var getCheckds = require('modules/checkedall/js/checkedall.js')();
  var dialog = require('modules/dialog/js/dialog.js');
  var $content = $('.content-wrap');
  
  $content.on('click','.addall',function(){
    var url = $(this).data('url');
    var $checkeds = getCheckds();
    var ids = [];
    var $trs = $checkeds.parents('tr');
    if ($checkeds.length < 1) {
      dialog.confirm('请选择数据后操作');
      return false;
    }
    $checkeds.each(function() {
      ids.push($(this).val());
    });
    dialog.confirm('确定要加入到采购单吗？', function() {

    $.post(url,{id:ids},function(res){
      if(res.state){
        location.href = location.href;
      }
    },'json');
    });
  });
  
});