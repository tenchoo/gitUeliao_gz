define(function(require, exports, module) {
  var validator = require('modules/form/js/validator.js');
  var dialog = require('modules/dialog/js/dialog.js');
  var $form = $('.deliver-goods form');
  var $hide = $('.express-form');
  var expressData = require('api/ajax/expressinfo?callback=define');
  var expressList = require('libs/arttemplate/3.0.0/template.js')('expressList', expressData);

  $form.find('[name="shopForm[companyMark]"]').append(expressList);

  $form.on('change', '[name="shopForm[type]"]', function() {
    if ($(this).val() == 2) {
      $hide.addClass('hide');
    } else {
      $hide.removeClass('hide');
    }
  }).validate({
    rules: {
      'deliver[other]': {
        maxlength: 10
      },
      'deliver[number]': {
        required: true,
        maxlength: 20
      }
    },
    messages: {
      'deliver[other]': {
        maxlength: '最多10个字符'
      },
      'deliver[number]': {
        required: '不能为空',
        maxlength: '最多10个字符'
      }
    },
    submitHandler: function(form) {
      validator.formAjax($form, 'shopForm');
    }
  });
});