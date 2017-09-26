define(function(require, exports, module) {
  var input = require('modules/form/js/input.js');
  input.intOnly();
  var $form = $('.content-wrap form');
  var $colorsChecked = $form.find('.colors-checked');
  $form
    .on('click', '.batch-save', function(event) {
      var num = $(this).prev().val();
      if (/^\d+$/.test(num)) {
        return $colorsChecked.find('input.int-only').val(num);
      }
      alert('只能是大于或者等于0的整数');
    })
    .on('submit', function() {
      var max = 0;
      $form.find('input.int-only').each(function() {
        max = Math.max(max, input.intFormat(this.value || 0));
      });

    });
});
