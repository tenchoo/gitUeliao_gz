define(function(require, exports, module) {
  require('modules/area/css/style.css');
  var area = require('modules/area/js/area.js');
  var $form = $('form.form-horizontal');

  function areaSelectInit() {
    area.select($('.area-select'), {
	  step:2,
      realField: '[name="areaId"]',
      'default': $form.find('[name="areaId"]').val()
    });
  }
  areaSelectInit();
});