define(function(require, exports, module) {
  var getCheckds = require('modules/checkedall/js/checkedall.js')();
  var dialog = require('modules/dialog/js/dialog.js');
  var $content = $('.content-wrap');
  
  $content.on('click','.addbuylist',function(){
    var url = $(this).data('url');
    var id = $(this).data('id');
    dialog.confirm('确定要加入到采购单吗？', function() {
      $.get(url,{id:id},function(res){
        console.log(res.state);
        if(res.state){
          location.href = location.href;
        }
      },'json');
    });
  });
  
});