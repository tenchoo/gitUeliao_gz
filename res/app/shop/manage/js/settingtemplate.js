define(function(require, exports, module) {
  var $uploader;
  var $form = $('.settingtemplate form');
  var validator = require('modules/form/js/validator.js');
  require('libs/jquery-colorpicker/1.0.0/jquery.colorpicker.min.js');
  require('modules/uploader/js/uploader.js').uploader({
    success: function(file, res) {
      $uploader = $('#rt_' + file.source.ruid).parents('.uploader');
      if ($uploader.is('.uploader-image')) {
        $uploader.find('button').html('<img src="' + seajs.data.uploaderPath + '/../..' + res.data + '" alt="" width="80" height="80"><span class="bg"></span><span>重新上传</span>');
        $uploader.prev().find('input:hidden').val(res.data);
      }
      if ($uploader.is('.uploader-button')) {
        $uploader.find('button').text('重新上传');
        $uploader.parent().find('.image-url').val(res.data);
      }
    }
  });

  $('.show-color').colorpicker({
    fillcolor: true,
    success: function(o, color) {
      o.css('background', color);
      o.find('input').val(color);
    },
    reset: function(o) {
      o.removeAttr('style');
      o.find('input').val('');
    }
  });

  $form.on('submit', function(event) {
    event.preventDefault();
    validator.formAjax($form, 'Setting', true);
  });

});