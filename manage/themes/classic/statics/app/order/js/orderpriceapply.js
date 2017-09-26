define(function(require, exports, module) {
  var dialog = require('modules/dialog/js/dialog.js');
  require('modules/form/js/input.js').priceOnly();

  $('.table').on('click', '.del', function(e) {
    e.preventDefault();
    var $tr = $(this).parents('tr');
    dialog.confirm('确定删除？', function() {
      $tr.remove();
    });
  });
});