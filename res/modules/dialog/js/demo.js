define(function(require, exports, module) {
  var dialog = require('./dialog.js');
  $('body').on('click', '.confirm_btn', function() {
    var $t = $(this);
    dialog.confirm($t.prev().val());
  }).on('click', '.tip_btn', function() {
    var $t = $(this);
    dialog.tip($t.prev().val());
  });
});