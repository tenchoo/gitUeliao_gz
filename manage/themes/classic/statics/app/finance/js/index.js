define(function(require, exports, module) {
	var input = require('modules/form/js/input.js');
	require('vue');

	var $memberName =  $('input[name="memberName"]');
    var $memberId =  $('input[name="memberId"]');
	var $addBtn = $('#btn-add');
	var cache;

	var $content = $('.navbar-default');

	input
		.suggestion($memberName, {
			er: function() {
				var val = $memberName.val();
				if (val != '' && val !== $supplier[0].defaultValue) {
					$memberId.val('');
					$addBtn.prop('disabled', true);
				}
			},
			cb: function($li, data) {
				cache = data;
				$memberName.val($li.text());
				$memberId.val($li.data('id'));
				$addBtn.prop('disabled', false);
			}
		   });


	$from = $('.navbar-form');
	var $d =  $('input[name="d"]');
	var $month =  $('input[name="month"]');


	$content.on('click', 'ul li', function(event) {
		$d.val( $(this).attr('rel') );
		$from.submit();
	});




});