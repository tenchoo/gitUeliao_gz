define(function(require, exports, module) {
  var category = require('modules/category/js/category.js');
  var validator = require('modules/form/js/validator.js');
  var $form = $('.apply form');
  var $phone = $('#contactPhone');
  var $image;
  var uploader = require('modules/uploader/js/uploader.js').uploader({
    success: function(file, res) {
      $image = $('#rt_' + file.source.ruid).parents('.image-group');
      $image.find('.image-wrap').html('<img src="' + seajs.data.uploaderPath + '/../..' + res.data + '" alt="" width="120" height="120"><span class="bg"></span><span>重新上传</span>');
      $image.find('.image-url').val(res.data);
    }
  });
  var $business = $('.business');

  category.select($('.category-select'), {
    level: 2,
    'default': $('[name="shopForm[categoryId]"]').val(),
    realField: '[name="shopForm[categoryId]"]'
  });
  $form.on('change', '[name="shopForm[type]"]', function() {
    if ($(this).val() == 2) {
      $business.removeClass('hide');
      if ($business.data('upload')) return;
      $business.data('upload', true);
      uploader.addButton({
        id: '.business-license',
        multiple: false
      });
    } else {
      $business.addClass('hide');
    }
  }).on('click', '.send-code', function() {
    validator.sendVerifyCode($(this), {
      field: $phone,
      fieldSuccess: true,
      postData: {
        phone: $phone.val(),
        optype: 'shopapply'
      },
      form: 'checkForm'
    });
  }).validate({
    rules: {
      'shopForm[identityCard]': {
        required: true,
        regexp: validator.regexps.idCard
      },
      'shopForm[contactPerson]': {
        required: true,
        maxlength: 25
      },
      'shopForm[contactPhone]': {
        required: true,
        regexp: validator.regexps.mobile
      },
      'shopForm[verifyCode]': {
        required: true,
        regexp: validator.regexps.verifyCode
      }
    },
    messages: {
      'shopForm[identityCard]': {
        required: '不能为空',
        regexp: '请输入正确的身份证号码'
      },
      'shopForm[contactPerson]': {
        required: '不能为空',
        maxlength: '不能超过25个字'
      },
      'shopForm[contactPhone]': {
        required: '不能为空',
        regexp: '请输入11位手机号码'
      },
      'shopForm[verifyCode]': {
        required: '请输入验证码',
        regexp: '验证码不正确，请重新输入'
      }
    },
    submitHandler: function(form) {
      validator.formAjax($form, 'shopForm');
    }
  });
});