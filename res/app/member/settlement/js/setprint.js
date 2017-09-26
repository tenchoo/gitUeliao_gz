define(function(require, exports, module) {
  var $content = $('.frame-content');
  var dialog = require('modules/dialog/js/dialog.js');

  $content
    .on('submit', 'form', function(e) {
      e.preventDefault();
      var $form = $(this);
      $.post($form.attr('action'), $form.serializeArray(), function(res) {
        if (res.state) {
          return dialog.tip(res.message || '打印机绑定成功！', { type: 'success' });
        }
        dialog.alert(res.message || '提交失败，请稍后重试', { type: 'error' });
      }, 'json');
    });

});
