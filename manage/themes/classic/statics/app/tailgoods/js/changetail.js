define(function(require, exports, module) {
  require('vue');
  new Vue({
    el: '#changetail',
    methods: {
      del: function(event) {
        var $tr = $(event.target).parents('tr');
        $tr.parent().find('.' + $tr.attr('class')).remove();
      }
    },
    filters: {
      currencyDisplay: {
        read: function(val) {
          return (val || 0).toFixed(2)
        },
        write: function(val, oldVal) {
          var number = +val.replace(/[^\d.]/g, '')
          return isNaN(number) ? 0 : parseFloat(number.toFixed(2))
        }
      }
    }
  });
});
