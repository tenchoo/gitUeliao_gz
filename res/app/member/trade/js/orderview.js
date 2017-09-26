define(function(require, exports, module) {
  
  var dialog = require('modules/dialog/js/dialog.js');
  var $form = $('.frame-content');

  $form.on('click','.cancel-order',function(){
    var $t = $(this).parents().find('.cancel-order-tip');
    var orderid = $(this).data('orderid');
    dialog.confirm($t.html(),function($dialog){
      $.post('/ajax', {
        action: 'order',
        optype: 'cancleorder',
        orderId: orderid,
        closeReason: $dialog.find('select').val(),
      }, function(res) {
        if (res.state) {
          location.href = location.href;
        } else {
          dialog.tip(res.message || '取消失败，请稍后重试！');
        }
      }, 'json');
    });
  });
  
});