define(function(require, exports, module) {
  var dialog = require('modules/dialog/js/dialog.js');
  require('libs/my97datepicker/4.8.0/WdatePicker.js');
  var $content = $('.frame-content');

  $content
    .on('click', '.input-date', WdatePicker)
    .on('click', '.print', function(e) {
      e.preventDefault();
      $.get($(this).attr('href'), function(res) {
        if (res.seate) {
          dialog.tip(res.message || '打印成功！', { type: 'success' });
          return;
        }
        dialog.alert(res.message || '打印失败，请稍后重试！', { type: 'error' });
      }, 'json');
    });

});
