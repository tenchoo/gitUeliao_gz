define(function(require, exports, module) {
  require('modules/warehouse/css/style.css');
  require('vue');
  var input = require('modules/form/js/input.js');
  var dialog = window;
  var template = require('libs/arttemplate/3.0.0/template.js');
  var $content = $('.ajax_form');
  var $dialog = $('.add-confirm');
  var $dialogContent = $dialog.find('.category-select');
  var ajaxLoading;
  var $supplier = $('input[name="singleNumber"]');
  var $addBtn = $('#btn-add');
  var $saveBtn = $('#btn-save');
  var cache;

  input.intOnly();
  input.numFloatOnly();

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



  var vm = new Vue({
    data: {
      selected: {},
      sorters: {},
      hasData: false,
      warehouses: {}
    },
    el: 'form',
    methods: {
      doSelect: function(id, e) {
        this.selected[id] = $(e.target).val();
      },
      getSorters: function(id, s) {
        var that = this;
        if (that.sorters[id]) return;
        $.get('/api/packinger_at_ware', {
          warehouseId: id
        }, function(res) {
          if (res.state) {
            var sorters = {};
            sorters[id] = res.data.list;
            that.sorters = $.extend(sorters, that.sorters);
            return;
          }
          alert(res.message || '数据加载出错，请刷新页面重试！');
        }, 'json');
      }
    }
  });


  function getStorage(serial, cb) {
    if (ajaxLoading) return;

    $dialogContent.children('div').addClass('hide');
    if ($('#s_' + serial).length) {
      $('#s_' + serial).removeClass('hide');
      return cb();
    }
    ajaxLoading = true;
    $.get('/api/product_at_storage', {
      serial: serial
    }, function(res) {
      ajaxLoading = false;
      if (res.state) {
        $dialogContent.append(template('storage', $.extend(res, {
          serial: serial
        })));
        return cb();
      }
      alert('对不起，没有查询到该产品库存！');
    }, 'json');
  }

  function getArea(serial, house) {
    var $area = $('#s_' + serial).find('.area');
    $.get('/api/contain_product_area', {
      serial: serial,
      house: house
    }, function(res) {
      if (res.state) {
        $area.html(template('area', $.extend(res, {
          serial: serial
        })));
        return;
      }
      alert('数据加载出错，请刷新页面重试！');
    }, 'json');
  }

  function getPosition(serial, area) {
    var $position = $('#s_' + serial).find('.position');
    $.get('/api/contain_product_position', {
      serial: serial,
      area: area
    }, function(res) {
      if (res.state) {
        $position.html(template('position', $.extend(res, {
          serial: serial
        })));
        return;
      }
      alert('数据加载出错，请刷新页面重试！');
    }, 'json');
  }
  function getBatch(serial, position) {
    var $wrap = $('#s_' + serial);
    var id = serial + position;
    $wrap.find('.cate2-wrap').removeClass('hide');
    $wrap.find('ul').addClass('hide');
    if ($('#b_' + id).length) {
      $('#b_' + id).removeClass('hide');
      return;
    }
    var $house = $wrap.find('.house');
    var $position = $wrap.find('.position');
    $.get('/api/product_at_batch2', {
      serial: serial,
      position: position
    }, function(res) {
      if (res.state) {
        $wrap.find('div').append(template('batch', $.extend(res, {
          serial: serial,
          storageId: $house.val(),
          storage: $house.find(':selected').data('title'),
          positionId: position,
          position: $position.find(':selected').data('title'),
          id: id
        })));
        return;
      }
      alert('数据加载出错，请刷新页面重试！');
    }, 'json');
  }
  function updateVm() {
    vm.warehouses = {};
    var $storage = $content.find('td.storage');
    if ($storage.length === 0) {
      vm.hasData = false;
      return;
    }
    $storage.each(function(i, v) {
      var $t = $(this);
      var w = $t.find('input').val();
      vm.warehouses[w] = {
        w: $.trim($t.text())
      };
      vm.getSorters(w);
    });
    vm.hasData = true;
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
    updateVm();
    $dialog.modal('hide');
  });

  $content.on('click', 'a[data-serial]', function(event) {
    event.preventDefault();
    var serial = $(this).data('serial');
    getStorage(serial, function(storage) {
      $dialog.modal();
    });
  }).on('change', '.category-select .house', function(event) {
    var $t = $(this);
    var serial = $t.data('serial');
    getArea(serial, this.value);
    $t.parent().find('.position option').remove();
    $t.parent().children('div').addClass('hide');
  }).on('change', '.category-select .area', function(event) {
    var $t = $(this);
    var serial = $t.data('serial');
    getPosition(serial, this.value);
    $t.parent().children('div').addClass('hide');
  }).on('change', '.category-select .position', function(event) {
    var serial = $(this).data('serial');
    getBatch(serial, this.value);
  }).on('click', '.del', function(event) {
    event.preventDefault();
    var $tr = $(this).parents('tr');
    if ($tr.siblings().length === 2) {
      $tr.siblings('.empty').removeClass('hide');
    }
    // $('#' + $tr.attr('id').replace(/^s_/, 'c_')).prop('checked', false);
    $tr.remove();
    updateVm();
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
    $remainder.html(remainder);
  }).on('submit', 'form', function(e) {
	e.preventDefault();
	$saveBtn.prop('disabled', true);
    $.post('', $(this).serializeArray(), function(res) {
      if (res.state) {
        location.href = res.data;
        return;
      }
      alert(res.message || '保存失败，请稍后重试！');
	  $saveBtn.prop('disabled', false);
    }, 'json');
  });
});
