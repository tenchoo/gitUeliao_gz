define(function(require, exports, module) {
  var $craft = $('.craft-wrap');
  var $serialNumber = $('[name="p[serialNumber]"]');
  var serialNumber = $serialNumber.data('val');

  $craft.on('change', ':checkbox', function() {
    $(this).parent().siblings().find(':checkbox').prop('checked', false);
    updateSerialNumber();
  });

  function updateSerialNumber() {
    var newSerialNumber = [serialNumber];
    $craft.find(':checkbox:checked').each(function() {
      newSerialNumber.push(this.value);
    });
    $serialNumber.val(newSerialNumber.join('-'));
  }

});