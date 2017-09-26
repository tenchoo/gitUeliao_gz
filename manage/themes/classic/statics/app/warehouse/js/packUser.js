define(function(require, exports, module) {
	var template = require('libs/arttemplate/3.0.0/template.js');
	var input = require('modules/form/js/input.js');
	var cache;

	var $content = $('.content-wrap');
	var $tbody = $content.find('.import tbody');
	var $addBtn = $('#btn-add');
	var $username = $('input[name="username"]');

	input
		.suggestion($username, {
		  er: function() {
			$('#userId').val('');
			$addBtn.prop('disabled', true);
		  },
		  cb: function($li, data) {
			cache = data;
			$('#userId').val($li.data('id'));
			$addBtn.prop('disabled', false);
		  }
		});

$content
    .on('click', '.del', function(event) {
      event.preventDefault();
    //  $(this).parents('tr').remove();
    })
    .on('submit', 'form', function(event) {		
      event.preventDefault();
	  
	  var id = $('#userId').val();
		if( id === '' || id < 1 ){
			alert( '请先搜索并选择员工' );
			return;
		}

		var username = $username.val();
		if( document.getElementById( 'w_'+id ) ){
			alert( username+'已增加' );
			return ;
		}
	  
      var $form = $(this);
      $.post(location.href, $form.serializeArray(), function(res) {
        if (res.state) {
          $tbody.append( template('choose-list',{'id':id,'username':username} ) );
          return;
        }
        alert(res.message || '保存失败，请稍后重试！');
      }, 'json');
    });

	$(".manage[type=radio]").click(function(event){
		$.get( window.location.href, { userId: $(this).val(),'op':'setManger' }, function(res) {
          if (res.state) {
            return;
          }
          alert(res.message || '冻结失败，请稍后重试');
        }, 'json');
	});
});
