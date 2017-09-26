define(function(require, exports, module) {
	var $ops = $('.ops');
	$ops.on('click', function(event) {
		var $_t = $(this).parent().next();
		if (  $_t.is('.hide') ) {
			 $_t.removeClass('hide');
			return;
		}else{
			 $_t.addClass('hide');
		}
	});
});