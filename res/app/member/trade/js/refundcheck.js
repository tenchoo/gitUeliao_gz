define(function(require, exports, module) {
  var $content = $('.frame-content');
  var dialog = require('modules/dialog/js/dialog.js');

  $content
    .on('submit', 'form', function(e) {
      e.preventDefault();
      var $t = $(this);
      $.post($t.attr('action'), $t.serializeArray(), function(res) {
        if (res.state) {
          location.href = res.data;
          return;
        }
        dialog.tip(res.message || '提交失败，请稍后重试', { type: 'error' });
      }, 'json');
    });

});
