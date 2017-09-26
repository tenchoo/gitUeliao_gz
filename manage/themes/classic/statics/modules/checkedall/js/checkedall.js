define(function(require, exports, module) {
  module.exports = function($list, options) {
    $list = $list || $('.content-wrap');
    options = $.extend({
      all: '.well .checkedall',
      list: 'tbody :checkbox',
      async: false
    }, options || {});

    var $all = $list.find(options.all);
    var $checkboxList = $list.find(options.list);

    function getCheckds() {
      return (options.async ? $list.find(options.list) : $checkboxList).filter(':checked');
    }

    $list.on('change', options.all, function(event) {
      var checked = $(this).prop('checked');
      $all.prop('checked', checked);
      (options.async ? $list.find(options.list) : $checkboxList).prop('checked', !checked).trigger('click');
    }).on('change', options.list, function(event) {
      var checked = $(this).prop('checked');
      if (checked === false) {
        $all.prop('checked', false);
        return;
      }
      if (getCheckds().length === (options.async ? $list.find(options.list) : $checkboxList).length) {
        $all.prop('checked', true);
      }
    });
    return getCheckds;
  };
});