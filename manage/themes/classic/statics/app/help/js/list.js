define(function(require, exports, module) {
  var getCheckeds = require('modules/checkedall/js/checkedall.js')();

  $('.batch-del').on('click', function(e) {
    var ids = [];
    var checkeds = getCheckeds();
    e.preventDefault();
    if (checkeds.length === 0) {
      return alert('请选择数据');
    }
    if (!confirm('确定删除？')) return;
    checkeds.each(function() {
      ids.push(this.value);
    });
    $.get('/content/help/del.html', {
      id: ids.join()
    }, function(res) {
      if (res.state) {
        location.href = location.href;
        return;
      }
      alert('删除失败，请稍后重试！');
    }, 'json');


  });

});