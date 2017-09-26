define(function(require, exports, module) {
  var input = require('modules/form/js/input.js');
  input.priceOnly();
  var $supplier = $('input[name="product[supplier]"]');
  var $supplierId = $('input[name="product[supplierId]"]');
  input.suggestion($supplier, {
    er: function() {
      var val = $supplier.val();
      $supplierId.val('');
      if (val != '' && val !== $supplier[0].defaultValue) {
        alert('此生产厂家不存在');
        $supplier.val('').trigger('focus');
      }
    },
    cb: function($li) {
      $supplierId.val($li.data('id'));
    }
  });

  input.clearZero($('.int-only,.price-only'));

  $('form').on('submit', function() {
    if ($supplierId.val() === '') {
      alert('请选择正确的生产厂家');
      return false;
    }
  });

});
