define(function(require, exports, module) {
  var $uploader;
  require('./uploader.js').uploader({
    success: function(file, res) {
      $uploader = $('#rt_' + file.source.ruid).parents('.uploader');
      if ($uploader.is('.uploader-image')) {
        $uploader.find('button').html('<img src="' + seajs.data.uploaderPath + '/../..' + res.data + '" alt="" width="80" height="80"><span class="bg"></span><span>重新上传</span>');
      }
      if ($uploader.is('.uploader-button')) {
        $uploader.find('button').text('重新上传');
        $uploader.parent().find('.image-url').val(res.data);
      }
    }
  });
});