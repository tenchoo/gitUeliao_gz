define(function(require, exports, module) {
  require('libs/webuploader/0.1.5/webuploader.js');
  var dialog = window;
  var swf = seajs.resolve('libs/webuploader/0.1.5/webuploader.css') + '/../Uploader.swf';

  function error(type) {
    var msg;
    switch (type) {
      case 'Q_TYPE_DENIED':
        msg = '只能上传图片';
        break;
      case 'Q_EXCEED_SIZE_LIMIT':
      case 'F_EXCEED_SIZE':
        msg = '图片大小不能超过2M';
        break;
      case 'Q_EXCEED_NUM_LIMIT':
        msg = '一次只能上传1张图片';
        break;
      default:
        msg = '上传失败，请稍后重试';
    }
    dialog.alert(msg);
  }

  function uploadError(file, reason) {
    dialog.alert('上传失败，请稍后重试！');
  }

  exports.uploader = function(options) {
    options = $.extend({

      // swf文件路径
      swf: swf,

      // 文件接收服务端。
      server: seajs.data.uploaderPath,

      // 选择文件的按钮。可选。
      pick: {
        id: '.uploader',
        multiple: false,
        style: ''
      },

      //paste: document.body,

      accept: {
        title: '图片',
        extensions: 'gif,jpg,jpeg,bmp,png',
        mimeTypes: 'image/*'
      },
      error: error,

      formData: {
        'case': 'res'
      },

      fileSingleSizeLimit: 2 * 1024 * 1024,
      auto: true,
      duplicate: true,
      success: function() {

      },

      // 不压缩image, 默认如果是jpeg，文件上传前会压缩一把再上传！
      compress: false
    }, options || {});
    var uploader = WebUploader.create(options);
    // 前端验证出错
    uploader.on('error', options.error);
    // 服务器出错
    uploader.on('uploadError', uploadError);

    // 上传成功，要把服务器返回的信息做对应处理
    uploader.on('uploadSuccess', function(file, res) {
      //dialog.close();
      if (res.state) {
        options.success(file, res);
      } else {
        dialog.alert(res.message || '上传失败，请稍后重试！');
      }
    });
    return uploader;
  };
});
