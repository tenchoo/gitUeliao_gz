define(function(require, exports, module) {
  var $uploader;

  require('modules/uploader/js/uploader.js').uploader({
    accept: {
      title: '音频',
      extensions: 'amr',
      mimeTypes: 'audio/*'
    },
    error: function error(type) {
      var msg;
      switch (type) {
        case 'Q_TYPE_DENIED':
          msg = '只能上传amr格式音频';
          break;
        case 'Q_EXCEED_SIZE_LIMIT':
        case 'F_EXCEED_SIZE':
          msg = '音频大小不能超过2M';
          break;
        case 'Q_EXCEED_NUM_LIMIT':
          msg = '一次只能上传1个文件';
          break;
        default:
          msg = '上传失败，请稍后重试';
      }
      alert(msg);
    },
    success: function(file, res) {
      $uploader = $('#rt_' + file.source.ruid).parents('.uploader');
      $uploader.find('button').text('重新上传');
      $uploader.prev().find('input').val(res.data);
    },
    formData: {
      'case': 'sound'
    }
  });

});
