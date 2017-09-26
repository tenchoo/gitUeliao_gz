define(function(require, exports, module) {
  require('libs/kindeditor/4.1.10/kindeditor-min.js');
  module.exports = function(id, options) {
    options = $.extend({
      height: 560,
      resizeType: 0,
      items: [
        'source', '|', 'undo', 'redo', '|',
        /*'preview', 'print', 'template', 'code', 'cut', 'copy', 'paste',
               'plainpaste', 'wordpaste', '|',*/
        'justifyleft', 'justifycenter', 'justifyright',
        'justifyfull',
        /* 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', 'subscript',
                'superscript', 'clearhtml', 'quickformat', 'selectall', '|',*/
        '|', 'formatblock', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold',
        'italic', 'underline', 'strikethrough', /*'lineheight'* ,/ 'removeformat', '|', 'fullscreen', '/', 'image', 'multiimage',
        /*'flash',*/
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