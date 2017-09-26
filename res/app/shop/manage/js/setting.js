define(function(require, exports, module) {
  var category = require('modules/category/js/category.js');
  var validator = require('modules/form/js/validator.js');
  require('modules/uploader/js/uploader.js').uploader({
    success: function(file, res) {
      $image.find('.image-wrap').html('<img src="' + seajs.data.uploaderPath + '/../..' + res.data + '" alt="" width="120" height="120"><span class="bg"></span><span>重新上传</span>');
      $image.find('.image-url').val(res.data);
    }
  });
  var $form = $('.shopsetting form');
  var $image = $form.find('.image-group');

  category.select($('.category-select'), {
    level: 2,
    'default': $('[name="shopForm[categoryId]"]').val(),
    realField: '[name="shopForm[categoryId]"]'
  });

  $form.validate({
    rules: {
      'shopForm[shopName]': {
        required: true
      },
      'shopForm[contactPerson]': {
        required: true,
        maxlength: 25
      },
      'shopForm[contactPhone]': {
        required: true,
        regexp: validator.regexps.mobile
      }
    },
    messages: {
      'shopForm[shopName]': {
        required: '不能为空'
      },
      'shopForm[contactPerson]': {
        required: '不能为空',
        maxlength: '不能超过25个字'
      },
      'shopForm[contactPhone]': {
        required: '不能为空',
        regexp: '请输入11位手机号码'
      }
    },
    submitHandler: function(form) {
      validator.formAjax($form, 'shopForm', true);
    }
  });
});