define(function(require, exports, module) {
  var dialog = require('modules/dialog/js/dialog.js');

  $('.freight-list').on('click', '.del', function(event) {
    event.preventDefault();
    var $t = $(this);
    var url = $t.attr('href');

    $.get(url.replace('/delete/', '/check/'), function(res) {
      if (res.state) {
        dialog.confirm('确定删除？', function() {
          $.get(url, function(res) {
            if (res.state) {
              dialog.tip(res.message || '删除成功！');
              $t.parents('li').remove();
              return;
            }
            dialog.tip(res.message || '删除失败，请稍后重试！');
          }, 'json');
        });
        return;
      }
      dialog.tip(res.message || '有产品使用运费模板，不可删除！');
    }, 'json');
  });
});