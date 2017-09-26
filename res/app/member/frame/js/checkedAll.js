define(function(require, exports, module) {
  module.exports = function($list, options) {
    $list = $list || $('.frame-list-bd table');
    options = $.extend({
      all: '.selectall',
      list: 'tbody :checkbox'
    }, options || {});

    var $all = $list.find(options.all);
    var $checkboxList = $list.find(options.list);

    function getCheckds() {
      return $checkboxList.filter(':checked');
    }

    $list.on('change', options.all, function(event) {
      var checked = $(this).prop('checked');
      $all.prop('checked', checked);
      $checkboxList.prop('checked', checked).trigger('change');
    }).on('change', options.list, function(event) {
      var checked = $(this).prop('checked');
      if (checked === false) {
        $all.prop('checked', false);
        return;
      }
      if (getCheckds().length === $checkboxList.length) {
        $all.prop('checked', true);
      }
    });
    return getCheckds;
  };
});