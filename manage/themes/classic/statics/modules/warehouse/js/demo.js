define(function(require, exports, module) {
  var warehouse = require('./warehouse.js');
  var $demo2 = $('.demo2');
  warehouse.select($('.demo1'));
  warehouse.select($('.demo2'), {
    default: $demo2.find('input').val()
  });
  warehouse.select($('.demo3'), {
    level: 2
  });
});