define(function(require, exports, module) {
  var $form = $('form');
  var $action = $form.find('input[name="action"]');
  $form
    .on('click', '[data-target]', function(e) {
      $action.val(2);
    })
    .on('click', '.btn-success', function(e) {
      $.post($form.attr('action'), { action: 1 }, function(res) {
        if (res.state) {
          location.href = res.data;
          return;
        }
        alert(res.message || '审核失败，请稍后重试！');
      }, 'json');
    })
    .on('submit', function(e) {
      e.preventDefault();
      $form.find('.modal').modal('hide');
      $.post($form.attr('action'), $form.serializeArray(), function(res) {
        if (res.state) {
          location.href = res.data;
          return;
        }
        alert(res.message || '拒绝失败，请稍后重试！');
      }, 'json');
    });
});
