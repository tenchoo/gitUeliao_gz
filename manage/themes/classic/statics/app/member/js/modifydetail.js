define(function(require, exports, module) {
  require('modules/area/css/style.css');
  require('vue');
  var area = require('modules/area/js/area.js');
  var $form = $('form.form-horizontal');
  var $stalls = $form.find('[name="stalls"]:checked');
  var $stallsaddress = $form.find('[name="stallsaddress"]');
  var $factory = $form.find('[name="factory"]:checked');
  var $factoryatt = $form.find('[name="factoryatt"]');

  function areaSelectInit() {
    area.select($('.area-select'), {
      realField: '[name="areaId"]',
      'default': $form.find('[name="areaId"]').val()
    });
  }
  areaSelectInit();

  new Vue({
    props: ['companyname'],
    el: 'form',
    computed: {
      companynameError: function() {
        return this.companyname.length > 80;
      }
    }
  });

  if ($stalls.val() == 2) {
    $stallsaddress.prop('disabled', true);
  }
  $form.on('click', '[name="stalls"]', function() {
    if (this.checked && this.value == 1) {
      $stallsaddress.prop('disabled', false);
    } else {
      $stallsaddress.prop('disabled', true);
    }
  });

  if ($factory.val() == 2) {
    $factoryatt.prop('disabled', true);
  }
  $form.on('click', '[name="factory"]', function() {
    if (this.checked && this.value == 1) {
      $factoryatt.prop('disabled', false);
    } else {
      $factoryatt.prop('disabled', true);
    }
  });

});
