define(function(require, exports, module) {
	var input = require('modules/form/js/input.js');
	require('vue');

	var $content = $('.content-wrap');
	var $details = $('.list-page-body');
	
	var $detailItem = $content.find('.order-detail tbody tr');


  input.priceOnly();

	$content.on('click', '#detail', function(event) {
		if ( $details.is('.hide') ) {
			$details.removeClass('hide');
			return;
		}else{
			$details.addClass('hide');
		}
	  }).on('click', '.nav-tabs li', function(event) {
		$(this).addClass('active').siblings('.active').removeClass('active');
		var client = $(this).attr('role');
		if( client === 'applyform' ){
			$('.applyform').removeClass('hide');
			$('.addform').addClass('hide');
		}else{
			$('.addform').removeClass('hide');
			$('.applyform').addClass('hide');
		}
	  }).on('submit', 'form', function(e) {
		e.preventDefault();
		var $saveBtn = $(this).find('.btn-success');
		var t = $saveBtn.html();
		if( t === '' ){
			t = $saveBtn.val();
		}
		if(confirm('确定'+ t+'?' )){
			$saveBtn.prop('disabled', true);
			$.post('', $(this).serializeArray(), function(res) {
			  if (res.state) {
				location.href = res.data;
				return;
			  }
			  alert(res.message || '保存失败，请稍后重试！');
			  $saveBtn.prop('disabled', false);
			}, 'json');
		}
	  }).on('click', '.navbar-default ul li:not(.active)[data-group]', function(event) {
			var $t = $(this).addClass('active');
			var group = $t.data('group');
			$t.siblings('.active').removeClass('active');
			if (group === 'all') return $detailItem.removeClass('hide');
			$detailItem.addClass('hide').filter('[data-group="' + group + '"]').removeClass('hide');
		//	toggleChecked();
	});
});
