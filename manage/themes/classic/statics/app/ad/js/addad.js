define(function(require, exports, module) {
  var $form = $('form');
  var $uploader;

  require('modules/uploader/js/uploader.js').uploader({
    success: function(file, res) {
      $uploader = $('#rt_' + file.source.ruid).parents('.uploader');
      $uploader.find('button').html('<img src="' + seajs.data.uploaderPath + '/../..' + res.data + '" alt="" width="80" height="80"><span class="bg"></span><span>重新上传</span>');
      $uploader.find('[name="data[image]"]').val(res.data);
    },
    formData: {
      'case': 'adv'
    }
  });


});