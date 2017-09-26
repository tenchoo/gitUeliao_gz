define(function(require, exports, module) {
  require('libs/my97datepicker/4.8.0/WdatePicker.js');
  var validator = require('modules/form/js/validator.js');
  var $form = $('.profile form');
  require('modules/uploader/js/uploader.js').uploader({
    success: function(file, res) {
      $faceWrap.html('<img src="' + seajs.data.uploaderPath + '/../..' + res.data + '" alt="" width="80" height="80"><span class="bg"></span><span>重新上传</span>');
      $icon.val(res.data);
    },
    formData: {
      'case': 'face'
    }
  });
  var $faceWrap = $form.find('.image-wrap');
  var $icon = $form.find('input[name="Editinfo[icon]"]');

  $form.on('click', '#birthday', WdatePicker).validate({
    rules: {
      'Editinfo[nickName]': {
        required: true
      },
      'Editinfo[qq]': {
        required: true,
        regexp: /^[1-9]\d{4,14}$/
      }
    },
    messages: {
      'Editinfo[nickName]': {
        required: '不能为空'
      },
      'Editinfo[qq]': {
        required: '不能为空',
        regexp: '请输入正确的QQ号'
      }
    },
    submitHandler: function(form) {
      validator.formAjax($form, 'Editinfo', true);
    }
  });
});