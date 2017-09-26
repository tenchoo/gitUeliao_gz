define(function(require, exports, module) {
  require('modules/warehouse/css/style.css');
  var input = require('modules/form/js/input.js');
  var dialog = window;
  var template = require('libs/arttemplate/3.0.0/template.js');
  var $content = $('.content-wrap');
  var $dialog = $('.add-confirm');
  var $dialogContent = $dialog.find('.category-select');
  input.intOnly();
  input.numFloatOnly();

  function fomatFloat(src, pos) {
    return Math.round(src * Math.pow(10, pos)) / Math.pow(10, pos);
  }

  function getArea(serial, house, cb) {
    $dialogContent.children('div').addClass('hide');
    if ($('#s_' + serial).length) {
      $('#s_' + serial).removeClass('hide');
      return cb();
    }

    $.get('/api/contain_product_area', {
      extraOrderId: orderid,
      serial: serial,
      house: house
    }, function(res) {
      if (res.state) {
        $dialogContent.append(template('area', $.extend(res, {
          serial: serial
        })));
        return cb();
      }
      alert('数据加载出错，请刷新页面重试！');
    }, 'json');
  }

  function getPosition(serial, area) {
    var $wrap = $('#s_' + serial).find('.cate2-wrap');
    var id = serial + area;
    /*$wrap.children('ul').addClass('hide');
    if ($('#p_' + id).length) {
      $('#p_' + id).removeClass('hide');
      return;
    }*/
    $.get('/api/contain_product_position', {
      extraOrderId: orderid,
      serial: serial,
      area: area
    }, function(res) {
      if (res.state) {
        $wrap.html(template('position', $.extend(res, {
          serial: serial
            /* storageId: storage,
             id: id*/
        })));
        return;
      }
      alert('数据加载出错，请刷新页面重试！');
    }, 'json');
  }

  function getBatch(serial, position, positiontitle) {
    var $wrap = $('#s_' + serial).find('.cate3-wrap');
    var id = serial + position;
    $wrap.children('ul').addClass('hide');
    if ($('#b_' + id).length) {
      $('#b_' + id).removeClass('hide');
      return;
    }
    $.get('/api/product_at_batch2', {
      extraOrderId: orderid,
      serial: serial,
      position: position
    }, function(res) {
      if (res.state) {
        $wrap.append(template('batch', $.extend(res, {
          serial: serial,
          positionId: position,
          positiontitle: positiontitle,
          id: id
        })));
        return;
      }
      alert('数据加载出错，请刷新页面重试！');
    }, 'json');
  }

  $dialog.on('click', '.save', function(event) {
    var $serial = $dialogContent.children('div:not(.hide)');
    var $checkeds = $serial.find('input:checked').prop('checked', false);
    var serial = $serial.attr('id').replace(/^s_/, '');
    var data = [];
    var $tbody = $('#b_' + serial);
    if ($checkeds.length === 0) return alert('请选择数据后操作');
    $checkeds.each(function() {
      data.push($(this).data());
    });
    $tbody.append(template('list', {
      pid: $tbody.data('pid'),
      serial: serial,
      data: data,
      t: (new Date()).getTime()
    })).find('tr.empty:not(.hide)').addClass('hide');
    $dialog.modal('hide');
  });

  $content.on('click', 'a[data-serial]', function(event) {
    event.preventDefault();
    var serial = $(this).data('serial');
    var house = $(this).data('wid');
    getArea(serial, house, function(storage) {
      $dialog.modal();
    });
  }).on('change', '.category-select select.cate1-wrap', function(event) {
    var serial = $(this).data('serial');
    getPosition(serial, this.value);
  }).on('change', '.category-select select.cate2-wrap', function(event) {
    var serial = $(this).data('serial');
    getBatch(serial, this.value, $(this).find('[value="' + this.value + '"]').data('positiontitle'));
  }).on('click', '.del', function(event) {
    event.preventDefault();
    var $tr = $(this).parents('tr');
    if ($tr.siblings().length === 2) {
      $tr.siblings('.empty').removeClass('hide');
    }
    // $('#' + $tr.attr('id').replace(/^s_/, 'c_')).prop('checked', false);
    $tr.remove();

  }).on('change', '.num input', function() {

    var $t = $(this);
    var val = $t.val();
    var $total = $t.parents('tr:first').find('.total');
    var $integer = $t.siblings('.integer');
    var $remainder = $t.siblings('.remainder');
    var total = $total.html();
    var integer = $integer.html();
    var remainder = $remainder.html();
    integer = parseInt(total / val, 10);
    remainder = total - integer * val;
    $integer.html(integer);
    $remainder.html(fomatFloat(remainder, 1));

  }).on('submit', 'form', function(e) {
    e.preventDefault();
    $.post('', $(this).serializeArray(), function(res) {
      if (res.state) {
        location.href = res.data;
        return;
      }
      alert(res.message || '保存失败，请稍后重试！');
    }, 'json');
  });



});
