define(function(require, exports, module) {
  var category = require('./category.js');
  var $demo2 = $('.demo2');
  category.select($('.demo1'));
  category.select($('.demo2'), {
    default: $demo2.find('input').val()
  });
  category.select($('.demo3'), {
    level: 2
  });
});