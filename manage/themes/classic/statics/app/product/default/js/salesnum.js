define(function(require, exports, module) {
  var input = require('modules/form/js/input.js');
  input.priceOnly();
  var $supplier = $('input[name="s"]');
  var $addBtn = $('#btn-add');
  var cache;
  input
    .suggestion($supplier, {
      er: function() {
        $addBtn.prop('disabled', true);
      },
      cb: function($li, data) {
        cache = data;
        $addBtn.prop('disabled', false);
      }
    });

});