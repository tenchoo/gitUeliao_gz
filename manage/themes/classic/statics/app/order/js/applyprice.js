define(function(require, exports, module) {
  require('modules/form/js/input.js').priceOnly();
  var $form = $('form.apply-price');
  var $state = $form.find('[name=state]');
  $form.on('click', '.text-center button', function() {

    $state.val($(this).is('.btn-default') ? 2 : 1);
    $form.trigger('submit');
  });
});