define(function(require, exports, module) {
  var dialog = require('modules/dialog/js/dialog.js');
  var $salelist = $('.salelist');
  var getCheckeds = require('app/member/frame/js/checkedAll.js')();

  /*$salelist.on('click', '.qrcode a', function() {
    var $qrcode = $(this).parent('.qrcode');
    var $qrcodeimg = $(this).next('.qrcode-img');
    if ($qrcodeimg.is('.hide')) {
      $qrcodeimg.removeClass('hide');
      $qrcode.css("z-index","10");
    } else {
      $qrcodeimg.addClass('hide');
      $qrcode.removeAttr('style');
    }
  });*/
  
  $('.qrcode a').bind({
    mouseenter:function(){
      $(this).next('.qrcode-img').removeClass('hide');
      $(this).parent('.qrcode').css('z-index','10');
    },
    mouseleave:function(){
      $(this).next('.qrcode-img').addClass('hide');
      $(this).parent('.qrcode').removeAttr('style');
    }
  });
});