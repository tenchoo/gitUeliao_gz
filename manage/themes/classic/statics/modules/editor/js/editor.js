define(function(require, exports, module) {
  var uploader = require('modules/uploader/js/uploader.js').uploader;
  var imgUploader = uploader({
    pick: {},
    success: function(file, res) {
      var $uploader = $('.editor-upload-wrap');
      var img = '<span class="thumbnail pull-left" style="margin-right:4px;"><img width="80" src="' + seajs.data.uploaderPath.replace('/api/upfile', '') + res.data + '" alt="" style="height:80px;" /></span>';
      if ($uploader.find('.thumbnail').length) {
        $uploader.append(img);
      } else {
        $uploader.removeClass('text-center').html(img);
      }
    },
    formData: {
      'case': 'news'
    }
  });
  var fileUploader = uploader({
    error: function error(type) {
      var msg;
      switch (type) {
        case 'Q_TYPE_DENIED':
          msg = '只能上传office文件';
          break;
        case 'Q_EXCEED_SIZE_LIMIT':
        case 'F_EXCEED_SIZE':
          msg = '文件大小不能超过2M';
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
      var $keUrl = $('#keUrl').val(seajs.data.uploaderPath.replace('/api/upfile', '') + res.data);
      $keUrl.parents('.ke-dialog-content').find('.ke-dialog-yes>.ke-button').trigger('click');
    },
    pick: {},
    accept: {
      title: '文件',
      extensions: 'doc,docx,xls,xlsx,wps,txt,pdf,ppt',
    },
    formData: {
      'case': 'office'
    }
  });



  $('body').on('editorUpload', function(event) {
    imgUploader.addButton({
      id: '.editor-upload',
      innerHTML: '选择图片'
    });
  }).on('editorFileUpload', function(event) {
    fileUploader.addButton({
      id: '.editor-file-upload',
      innerHTML: '选择文件'
    });
  });


  require('libs/kindeditor/4.1.10/kindeditor-min.js');
  module.exports = function(id, options) {
    options = $.extend({
      height: 560,
      resizeType: 0,
      allowMediaUpload: false,
      uploadJson: seajs.data.uploaderPath,
      extraFileUploadParams: {
        'case': 'news'
      },
      filePostName: 'file',
      items: [
        'source', '|', 'undo', 'redo', '|',
        /*'preview', 'print', 'template', 'code', 'cut', 'copy', 'paste',
               'plainpaste', 'wordpaste', '|',*/
        'justifyleft', 'justifycenter', 'justifyright',
        'justifyfull',
        /* 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', 'subscript',
                'superscript', 'clearhtml', 'quickformat', 'selectall', '|',*/
        '|', 'formatblock', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold',
        'italic', 'underline', 'strikethrough', /*'lineheight'*/ , 'removeformat', '|', 'fullscreen', '/', 'image',
        /*'multiimage',
               'flash',*/
        'media', 'insertfile', 'table',
        /*'hr', 'emoticons', 'baidumap', 'pagebreak',
               'anchor',*/
        'link', 'unlink' /*, '|', 'about'*/
      ],
      afterBlur: function() {
        this.sync();
      }
    }, options || {});
    return KindEditor.create(id || '#editor', options);
  };
});