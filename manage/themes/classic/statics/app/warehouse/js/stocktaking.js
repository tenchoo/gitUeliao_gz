define(function(require, exports, module) {
  var input = require('modules/form/js/input.js');
  input.priceOnly();
  var $supplier = $('input[name="serialNumber"]');
  var $addBtn = $('#btn-add');
  var cache;
  var $form = $('.content-wrap form');

  input
    .suggestion($supplier, {
      er: function() {
        $addBtn.prop('disabled', true);
      },
      cb: function($li, data) {
        cache = data;
        $addBtn.prop('disabled', false);
      }
    });

	var $op = $('input[name="op"]');
	$('.btn-comfirm').on('click',function(){
		$op.val($(this).data('op'));
		if(confirm('确定提交保存？')){
		 $('form').trigger('submit');
		}
	});
});
