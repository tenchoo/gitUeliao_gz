define(function(require, exports, module) {
  var dialog = require('modules/dialog/js/dialog.js');
  var $content = $('.frame-content');
  require('modules/form/js/input').priceOnly();
  $content
    .on('click', '.add-product', function(event) {
      event.preventDefault();
      var member = $content.find('select').val();
      if (member === '') {
        $content.find('select').trigger('focus');
        dialog.alert('请先选择客户');
        return;
      }
      location.href = $(this).attr('href') + member;
    })
    .on('submit', 'form', function(event) {
      event.preventDefault();
      var $form = $(this);
      $.post($form.attr('action'), $form.serializeArray(), function(res) {
        if (res.state) {
          location.href = res.data;
          return;
        }
        dialog.alert(res.message || '申请失败，请稍后重试！');
      }, 'json');
    });
});
