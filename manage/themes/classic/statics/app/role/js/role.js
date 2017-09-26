define(function(require, exports, module) {
  $('ul.auth-manage').on('change', 'div>label input', function() {
    var $div = $(this).parents('div:first');
    var $ul = $div.next('ul');
    var $ulP = $div.parents('ul:first');
    var $ulPP = $ulP.parents('ul:first');
    var $ulPPP = $ulPP.parents('ul:first');
    $ul.find('input').prop('checked', this.checked);
    $ulP.prev().find('input').prop('checked', $ulP.find(':checked').length > 0);
    $ulPP.prev().find('input').prop('checked', $ulPP.find(':checked').length > 0);
    $ulPPP.prev().find('input').prop('checked', $ulPPP.find(':checked').length > 0);
  }).on('change', 'li>label input', function() {
    var $ul = $(this).parents('ul:first');
    var $ulP = $ul.parents('ul:first');
    var $ulPP = $ulP.parents('ul:first');
    $ul.prev().find('input').prop('checked', $ul.find(':checked').length > 0);
    $ulP.prev().find('input').prop('checked', $ulP.find(':checked').length > 0);
    $ulPP.prev().find('input').prop('checked', $ulPP.find(':checked').length > 0);
  });

  var $content = $('.auth-manage');

  $content.on('click', '.glyphicon', function(event) {
	var $child = $(this).parent().next();
	if ($(this).is('.glyphicon-plus')) {
        $(this).removeClass('glyphicon-plus').addClass('glyphicon-minus');
		$child.removeClass('hide');
        return;
    }else{
		$(this).removeClass('glyphicon-minus').addClass('glyphicon-plus');
		$child.addClass('hide');
	}
  });
});