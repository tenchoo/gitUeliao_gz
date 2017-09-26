define(function(require, exports, module) {
  require('modules/editor/js/editor.js')();
  var validator = require('modules/form/js/validator.js');
  var $form = $('.newspost form');
  $form.validate({
    rules: {
      'dataForm[title]': {
        required: true
      },
      'dataForm[content]': {
        required: true
      }
    },
    messages: {
      'dataForm[title]': {
        required: '不能为空'
      },
      'dataForm[content]': {
        required: '不能为空'
      }
    },
    submitHandler: function(form) {
      validator.formAjax($form, 'dataForm');
    }
  });
});