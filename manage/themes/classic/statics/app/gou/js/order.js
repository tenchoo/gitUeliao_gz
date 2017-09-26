define(function(require, exports, module) {
  var getCheckds = require('modules/checkedall/js/checkedall.js')();
  var dialog = window;
  var $content = $('.content-wrap');

	function getIds(){
		var $checkeds = getCheckds();
		var ids = [];

		if ($checkeds.length < 1) {
		  dialog.alert('请选择数据后操作');
		  return false;
		}
		$checkeds.each(function() {
		  ids.push($(this).val());
		});
		return ids;
	}

   $content.on('click', '.well .form-inline .setBatch', function(event) {
	   var ids = getIds();
	   if( !ids ) return false;

		var title = $(this).attr('title');
	    if (!confirm('您确定要'+title+'吗？')) return;

	    ids = $.isArray(ids) ? ids : [ids];
		var url = $(this).data('url');

		event.preventDefault();
		$.post( url, {
			ids: ids
		}, function(res) {
			if (res.state) {
				location.href = location.href;
			}
		}, 'json');
  });

  var $setdeliveryman = $('.setdeliveryman-confirm');
  var $form = $setdeliveryman.find('form');
  $setdeliveryman.on('show.bs.modal', function(event) {
     var ids = getIds();
	if( !ids ) return false;
    $(this).find('[name="ids"]').val(ids);

  }).on('click', '.btn-success', function() {
    $.post($form.attr('action'), $form.serializeArray(), function(res) {
      if (res.state) {
        location.href = location.href;
      }
    }, 'json');
  });

  var $setarea = $('.setarea-confirm');
  var $form1 = $setarea.find('form');
  $setarea.on('show.bs.modal', function(event) {
    var ids = getIds();
	if( !ids ) return false;
    $(this).find('[name="ids"]').val(ids);

  }).on('click', '.btn-success', function() {
    $.post($form1.attr('action'), $form1.serializeArray(), function(res) {
      if (res.state) {
        location.href = location.href;
      }
    }, 'json');
  });


  $content
    .on('change', '.do-save', function(event) {
		var $tr = $(this).parents('tr');
		var $form = $tr.find('form');
		$form.submit();
    });
});
