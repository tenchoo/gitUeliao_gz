define(function(require, exports, module) {
  require('vue');
  var $list = $('#list');
  var getCheckds = require('modules/checkedall/js/checkedall.js')(null, { async: true });
  var List = Vue.extend({});
  var opt = {
    data: {
      select: [],
      org: null
    },
    methods: {
      submit: function(event) {
        var $t = $(event.target);
        var $form = $t.prev();
        var html = '';
        var RegOrg;
        if (!this.select.length) return alert('请选择产品');
        RegOrg = new RegExp('^' + this.select[0].split('-')[0] + '-');
        for (var i = 0; i < this.select.length; i++) {
          if (RegOrg.test(this.select[i]) === false) return alert('只能选择同一系列产品！');
          html += '<input type="hidden" name="singleNumber[]" value="' + this.select[i] + '"/>';
        }
        $form.html(html).trigger('submit');
      }
    }
  };
  new List(opt).$mount('#list');
  $list.on('click', '.pagination a', function(event) {
    event.preventDefault();
    var $t = $(this);
    $list.load($t.attr('href') + ' #list>*', function() {
      new List(opt).$mount('#list');
    });
  });
});
