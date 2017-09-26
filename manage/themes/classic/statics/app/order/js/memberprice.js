define(function(require, exports, module) {
  require('modules/form/js/input.js').priceOnly();

  require('vue');
  new Vue({
    el: 'form.member-price',
    methods: {
      submit: function(event) {
        var $form = $(event.target);
        if (this.state === '2' && !this.remark) {
          return alert('请输入反馈内容');
        }

        $.post($form.attr('action'), $form.serializeArray(), function(res) {
          if (res.state) {
            location.href = res.data;
            return;
          }
          alert(res.message || '提交失败，请稍后重试！');
        }, 'json');
      }
    }
  });
});
