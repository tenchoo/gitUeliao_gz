define(function(require, exports, module) {
	var template = require('libs/arttemplate/3.0.0/template.js');

	var $content = $('.content-wrap');
	var $tbody = $content.find('.import tbody');
	var $_wobj  = $content.find('.import tfoot .cate1');
    var $cate2 = $content.find('.import tfoot .cate2');

$content
    .on('click', ' tfoot button', function(event) {
		var wid = $_wobj.val();
		if( wid === '' || wid < 1 ){
			alert( '请选择仓库' );
			return;
		}

		var pid = $cate2.val();
		if( pid === '' || pid === 'default' || pid < 1 ){
			alert( '请选择仓位' );
			return;
		}

		var wtitle = $_wobj.find("option:selected").text();
		if( document.getElementById( 'w_'+wid ) ){
			alert( wtitle+'已增加' );
			return ;
		}

		var ptitle = $cate2.find("option:selected").text();
		$tbody.append( template('choose-list',{'wid':wid,'pid':pid,'ptitle':ptitle,'wtitle':wtitle} ) );
    })
    .on('click', '.del', function(event) {
      event.preventDefault();
      $(this).parents('tr').remove();
    })
    .on('submit', 'form', function(event) {
      event.preventDefault();
      var $form = $(this);
      $.post(location.href, $form.serializeArray(), function(res) {
        if (res.state) {
          location.href = res.data || location.href;
          return;
        }
        alert(res.message || '保存失败，请稍后重试！');
      }, 'json');
    });



 $_wobj.on('change', function(event) {
	var wid =  $(this).val();
	 if ( wid === '') return clearSelect($cate2, '一');
    getData('warehouseId',wid, function(data) {
        if (data.data === '') {
          disableSelect($cate2);
          $field.val(cate1);
          options.success(cate1);
          return;
        }
        createOptions($cate2, data.data);
      });
  });


  function render(data) {
    return template.compile('{{each list}}<option value="{{$index}}">{{$value}}</option>{{/each}}')({
      list: data
    });
  }

  function clearSelect($select, level) {
    $select.removeClass('input-disabled').prop('disabled', false).html('<option value="default">' + ('请选择') + '</option>');
  }

  function createOptions($select, data, defaultVal) {
    var haSize = $select.removeClass('input-disabled').prop('disabled', false).is('[size]');
    $select[haSize ? 'html' : 'append'](data).val(defaultVal || 'default');
  }

  function disableSelect($select) {
    $select.addClass('input-disabled').prop('disabled', true).html('<option value="">暂无数据</option>');
  }

  function clearRealField($field, clear) {
    if (!clear) return;
    $field.val('');
  }

  function getData(name,id, cb) {
    if (caches[name+'_'+id]) {
      return cb(caches[id]);
    }

	if( name == 'parent' ){
		var param = { id: id };
	}else{
		var param = { warehouseId: id };
	}

    $.get('/api/storage_position_info', param, function(res) {
      caches[id] = {
        parent: id,
        data: res.state ? render(res.data.childs) : ''
      };
      cb(caches[id]);
      //ajax出错处理

    }, 'jsonp');
  }
});
