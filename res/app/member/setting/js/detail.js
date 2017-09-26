define(function(require, exports, module) {
  require('modules/area/css/style.css');
  var area = require('modules/area/js/area.js');
  var validator = require('modules/form/js/validator.js');
  var $form = $('.profile form');
  var $stalls = $form.find('[name="Editdetailinfo[stalls]"]:checked');
  var $stallsaddress = $form.find('[name="Editdetailinfo[stallsaddress]"]');
  var $factory = $form.find('[name="Editdetailinfo[factory]"]:checked');
  var $factoryatt = $form.find('[name="Editdetailinfo[factoryatt]"]');

  function areaSelectInit() {
    area.select($('.area-select'), {
      realField: '[name="Editdetailinfo[areaId]"]',
      'default': $form.find('[name="Editdetailinfo[areaId]"]').val()
    });
  }
  areaSelectInit();


  $form.on('change', '[name="Editdetailinfo[stalls]"]', function() {
    if (this.value == 1) {
      $stallsaddress.prop('disabled', false).parent().removeClass('hide');
    } else {
      $stallsaddress.prop('disabled', true).parent().addClass('hide');
    }
  }).on('change', '[name="Editdetailinfo[factory]"]', function() {
    if (this.value == 1) {
      $factoryatt.prop('disabled', false).parent().removeClass('hide');
    } else {
      $factoryatt.prop('disabled', true).parent().addClass('hide');
    }
  }).find('[name="Editdetailinfo[stalls]"]:checked,[name="Editdetailinfo[factory]"]:checked').trigger('change');


  $form.validate({
    rules: {
      'Editdetailinfo[companyname]': {
        required: true
      },
      'Editdetailinfo[shortname]': {
        required: true
      }
    },
    messages: {
      'Editdetailinfo[companyname]': {
        required: '不能为空'
      },
      'Editdetailinfo[shortname]': {
        required: '不能为空'
      }
    },
    submitHandler: function(form) {
      validator.formAjax($form, 'Editdetailinfo', true);
    }
  });
});