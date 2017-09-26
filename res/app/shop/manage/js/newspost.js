define(function(require, exports, module) {
  require('modules/editor/js/editor.js')();
  var validator = require('modules/form/js/validator.js');
  var $form = $('.newspost form');
  $form.validate({
    rules: {
      'newsForm[title]': {
        required: true
      },
      'newsForm[content]': {
        required: true
      }
    },
    messages: {
      'newsForm[title]': {
        required: '不能为空'
      },
      'newsForm[content]': {
        required: '不能为空'
      }
    },
    submitHandler: function(form) {
      validator.formAjax($form, 'newsForm');
    }
  });
});