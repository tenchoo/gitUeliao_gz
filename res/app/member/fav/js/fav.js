define(function(require, exports, module) {
  var dialog = require('modules/dialog/js/dialog.js');
  var $content = $('.frame-content');
  var getCheckes = require('app/member/frame/js/checkedAll.js')($content, {
    all: '.page-wrap :checkbox',
    list: '.list :checkbox'
  });
  $content.on('click', '.batch-del', function(e) {
    e.preventDefault();
    var checkeds = getCheckes();
    var ids = [];
    if (checkeds.length === 0) {
      return dialog.alert('请选择数据后操作！');
    }
    dialog.confirm('确定删除？', function() {
      checkeds.each(function() {
        ids.push(this.value);
      });

      $.get(seajs.data.apiPath + '/ajax', {
        action: 'collection',
        optype: 'cancle',
        productId: ids.join()
      }, function(res) {
        if (res.state) {
          location.href = location.href;
          return;
        }
        dialog.alert('删除失败，请稍后重试！');
      }, 'json');

    });
  });
});