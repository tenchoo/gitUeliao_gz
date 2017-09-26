define(function(require, exports, module) {
  var template = require('libs/arttemplate/3.0.0/template.js');
  var warehouse = require('modules/warehouse/js/warehouse.js');
  var input = require('modules/form/js/input.js');
  var $content = $('.content-wrap');
  var $tbody = $content.find('.import tbody');
  // var $positionId = $content.find('#data_positionId');
  // var $name = $content.find('input[name="form[name]"]');
  // var $id = $content.find('input[name="form[id]"]');
  // var $contact = $content.find('input[name="form[contact]"]');
  // var $phone = $content.find('input[name="form[phone]"]');
  // var $product = $content.find('.product-search');
  // var $addlm=$('.addlm');
  // var $imporsub=$('.imporsub')

  require('modules/form/js/input.js').numFloatOnly();



  $tbody.find('tr').each(function() {
    var $t = $(this);
    var $n = $t.find('.warehouse-list').children('input');
    warehouse.select($t, {
      realField: $n,
      level: 3
    });
  });


});
