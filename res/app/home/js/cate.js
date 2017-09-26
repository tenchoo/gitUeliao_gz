define(function(require, exports, module) {
	var $attrFilter = $('div.attr-filter');
	$attrFilter.on('click', '.control', function(event) {
		var $t = $(this),
			$arr = $t.find('span'),
			$li = $t.parent(),
			isShow = $arr.is('.arr-down');
		$t.html(isShow ? '收起<span class="arr arr-up"></span>' : '更多<span class="arr arr-down"></span>');
		isShow ? $li.removeClass('hidden') : $li.addClass('hidden');
	}).find('.item li:last-child').each(function() {
		var $t = $(this);
		if ($t.position().top == 5) {
			$t.parents('.item').find('.control').remove();
		}
	});

	var $page = $('.page');
	var $input = $page.find('input');
	var pagecount = $input.data('pagecount');
	if (pagecount == 1 || pagecount == 0) {
		$input.prop('readonly', true);
	}

});