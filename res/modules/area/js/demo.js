define(function(require, exports, module) {
  var area = require('./area.js');
  var areaCheckbox = area.checkbox();
  area.select($('.demo1'), {
    success: function(data) {

    }
  });
  area.select($('.demo2'), {
    success: function(data) {

    },
    default: 529
  });

  $('.check-open').on('click', function() {
    var $t = $(this).parent().find('input');
    areaCheckbox.open({
      okFun: function(areas) {
        $t.val(areas);
      }
    });
  });
  $('.check-open-default').on('click', function() {
    var $t = $(this).parent().find('input');
    areaCheckbox.open({
      default: $t.val(),
      okFun: function(areas) {
        $t.val(areas.join(','));
      }
    });
  });

});