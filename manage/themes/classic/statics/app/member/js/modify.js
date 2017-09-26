define(function(require, exports, module) {
  var $form = $('form');

  require('modules/uploader/js/uploader.js').uploader({
    success: function(file, res) {
      $faceWrap.html('<img src="' + seajs.data.uploaderPath + '/../..' + res.data + '" alt="" width="80" height="80"><span class="bg"></span><span>重新上传</span>');
      $icon.val(res.data);
    }
  });
  var $faceWrap = $form.find('.image-wrap');
  var $icon = $form.find('input[name="face"]');

});