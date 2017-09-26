define(function(require, exports, module) {
  var dialog = require('modules/dialog/js/dialog.js');
  var $newslist = $('.newslist');
  var getCheckeds = require('app/member/frame/js/checkedAll.js')();

  function del(ids) {
    ids = $.isArray(ids) ? ids : [ids];
    dialog.confirm('确定删除？', function() {
      $.post('/ajax/shopnews', {
        optype: 'del',
        newsIds: ids
      }, function(res) {
        if (res.state) {
          location.href = location.href;
        } else {
          dialog.tip(res.message || '删除失败，请稍后重试！');
        }
      }, 'json');
    });
  }


  $newslist.on('click', 'a.del', function(event) {
    event.preventDefault();
    del($(this).data('newsid'));
  }).on('click', 'a.dels', function(event) {
    event.preventDefault();
    var $checkeds = getCheckeds();
    var ids = [];
    if ($checkeds.length > 0) {
      $checkeds.each(function() {
        ids.push($(this).val());
      });
      return del(ids);
    }
    dialog.tip('请选择数据后操作！');
  });
});