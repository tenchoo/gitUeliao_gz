define(function(require, exports, module) {
  var $payment = $('.payment');
  var $detail = $payment.find('.detail');
  var $online = $payment.find('.online');
  var $uploader;
  require('modules/uploader/js/uploader.js').uploader({
    success: function(file, res) {
      $uploader = $('#rt_' + file.source.ruid).parent('.uploader');	  
      $payment.find('input[name="Pay[paymentVoucher]"]').val(res.data);
	  $payment.find('.image-url').val(res.data);	  
    }
  });

  $payment.on('mouseenter', '.item', function(event) {
    $detail.removeClass('hide');
  }).on('mouseleave', '.item', function(event) {
    $detail.addClass('hide');
  });

  $payment.on('change', '[name="Pay[bank]"]', function(event) {
    var $li = $(this).parents('li').addClass('active');
    $li.siblings('li').removeClass('active');
    $payment.find('[name="Pay[logistics]"]').prop('disabled', !$li.is('.express'));
  });

});