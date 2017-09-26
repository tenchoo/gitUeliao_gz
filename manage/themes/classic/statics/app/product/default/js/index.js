define(function(require, exports, module) {
  var category = require('modules/category/js/category.js');
  var $demo2 = $('.demo2');
  category.select($('.demo2'), {
    //default: $demo2.find('input').val()
  });
  var getCheckds = require('modules/checkedall/js/checkedall.js')();
  $('.btn-export').on('click', function() {
    if (getCheckds().length) {
      console.log('导出操作');
      return;
    }
    alert('请选择数据后操作！');
  });
});