define(function(require, exports, module) {
  require('modules/area/css/style.css');
  var template = require('libs/arttemplate/3.0.0/template.js');
  var $cart = $('.cart');
  var area = require('modules/area/js/area.js');

  var dialog = require('modules/dialog/js/dialog.js');
  var validator = require('modules/form/js/validator.js');
  var price = parseInt($cart.find('span[data-price]').data('price'), 10);
  var $expressPrice = $cart.find('.price-list span:first');
  var $totalPrice = $cart.find('.order-info b.text-warning');
  var $ajaxWrap = $cart.find('.ajax-wrap');
  var ajaxWrapHtml = $ajaxWrap.html();
  var $address = $cart.find('.receiving-address');
  var $pay = $cart.find('.order-payModel');
  var $trs = $('.cart').find('tr[data-id]');

  var input = require('modules/form/js/input.js');

  var $suggestion = $cart.find('[data-suggestion]');
  var memberId;
  template.config('escape', false);

  require('libs/my97datepicker/4.8.0/WdatePicker.js');
  $cart.on('click', '.input-date', function() {
    WdatePicker({
      minDate: '%y-%M-%d',
      dateFmt: 'yyyy-MM-dd'
    });
  });

  function renderList(data) {
    $address.find('.bd').next('.address-list').remove();
    if (data) {
      $address.find('.bd').addClass('hide').after(template('list', {
        list: data,
        add: data.length < 10 ? '<div class="pull-right"><a class="btn btn-cancel btn-xs add" href="#">新增收货地址</a></div>' : ''
      }));
      $address.find('span.pull-right').parent().trigger('click');
    } else {
      $address.find('.bd').removeClass('hide');
    }
  }

  function updateTotalPrice() {
    var total = 0;
    var $total = $cart.find('.order-form .submit-info > span');
    if ($cart.find('table').length === 1) {
      $total.text(input.int2Price($cart.find('table strong.total').data('price')));
      return;
    }
    if ($cart.find('.submit-info [name="order[spot]"]').is(':checked')) {
      total += input.intFormat($cart.find('.order-now strong.total').data('price'));
    }
    if ($cart.find('.submit-info [name="order[booking]"]').is(':checked')) {
      total += input.intFormat($cart.find('.order-future strong.total').data('price'));
    }
    if ($cart.find('.submit-info [name="order[tail]"]').is(':checked')) {
      total += input.intFormat($cart.find('.order-tail strong.total').data('price'));
    }
    if (total === 0) {
      $cart.find('.submit-info button').addClass('btn-disabled').prop('disabled', true);
    } else {
      $cart.find('.submit-info button').removeClass('btn-disabled').prop('disabled', false);
    }
    $total.text(input.int2Price(total));
  }

  function updateTrPrice(prices) {
    $trs.each(function() {
      var $t = $(this);
      var price = input.price2Int(prices[$t.data('id')]);
      var num = parseFloat($t.find('.num').text());
      var total = price * num;
      $t.data('price', total);
      $t.find('.price').text(input.int2Price(price));
      $t.find('.total').text(input.int2Price(total));
    });

    $('.cart').find('table').each(function() {
      var $t = $(this);
      var total = 0;
      var deposit = 0;
      $t.find('tr[data-price]').each(function() {
        var $t = $(this);
        var price = $t.data('price');
        if (!$t.data('isfree')) {
          total += price;
        }
        deposit += price * $t.data('deposit');
      });
      deposit = input.intFormat(deposit);
      $t.find('.list-body-foot span[data-price]').text(input.int2Price(total)).data('price', total);
      $t.find('.deposit').text(input.int2Price(deposit)).data('price', deposit);
      updateTablePrice($t);
    });
  }

  function updateTablePrice($t) {
    var price = input.price2Int($t.find('.list-body-foot .price-only').val()) +
      $t.find('.list-body-foot span[data-price]').data('price');
    var balance = price - $t.find('.deposit').data('price');
    $t.find('strong.total').data('price', price).text(input.int2Price(price));
    $t.find('strong.balance').data('price', balance).text(input.int2Price(balance));
    updateTotalPrice();
  }

  function getList(memberId, cb) {
    cb = cb || function() {};
    $.post(seajs.data.apiPath + '/ajax', {
      action: 'address',
      optype: 'getlist',
      memberId: memberId || ''
    }, function(res) {
      if (res.state) {
        $.each(res.data, function(i, v) {
          res.data[i].isDefault = v.isDefault !== '1' ? '' : '<span class="text-minor pull-right">（默认地址）</span>';
        });
        cb(res.data);
      }
    }, 'json');
  }



  input.suggestion($suggestion, {
    er: function() {
      $ajaxWrap.html(ajaxWrapHtml);
      $cart.find('[name="order[memberId]"]').val('');
      $cart.find('[name="order[addressId]"]').val('');
    },
    cb: function($li) {
      var member = $li.text();
      $suggestion.val(member);
      memberId = $li.data('id');
      $cart.find('[name="order[memberId]"]').val(memberId);
      $.get('/cart/default/choosemember', {
        memberId: memberId
      }, function(res) {
        if (res.state) {
          $address = $ajaxWrap.html(res.data.addressHtml).find('.receiving-address');
          validateForm();
          $cart.find('[name="order[addressId]"]').val($address.find(':radio:checked').val());
          updateTrPrice(res.data.price);
		  if( res.data.isMonthPay ){
			$pay.addClass('hide');
		  }else{
			$pay.removeClass('hide');
		  }

        }
      }, 'json');
    }
  });

  //getList(window.memberId, renderList);

  function areaSelectInit() {
    area.select($('.area-select'), {
      realField: '[name="address[areaId]"]',
      'default': $address.find('.address-form form [name="address[areaId]"]').val()
    });
  }


  areaSelectInit();

  function validateForm() {
    $cart.find('.address-form form').validate({
      rules: {
        'address[name]': {
          required: true
        },
        'address[address]': {
          required: true
        },
        'address[mobile]': {
          required: true,
          regexp: validator.regexps.mobile
        }
      },
      messages: {
        'address[name]': {
          required: '不能为空'
        },
        'address[address]': {
          required: '不能为空'
        },
        'address[mobile]': {
          required: '不能为空',
          regexp: '请输入11位手机号码'
        }
      },
      submitHandler: function(form) {
        if ($address.find('.address-form form').data('submit')) return;
        $address.find('.address-form form').data('submit', true);
        $.post($address.find('.address-form form').attr('action'), $address.find('.address-form form').serializeArray(), function(res) {
          $address.find('.address-form form').data('submit', false);
          if (res.state) {
            var $ul = $address.find('ul');
            var $li = $ul.find('li[data-addressid="' + res.data.addressId + '"]');

            var isEdit = $li.length === 1;
            var isSelect = isEdit && $li.is('.select');
            var address = template('item', $.extend(res.data, {
              isDefault: ''
            }));
            var $emptyWrap = $('.no-data').parent();
            dialog.tip((isEdit ? '修改' : '添加') + '成功');
            $address.find('.address-form form').empty().parent().addClass('hide');

            if (isEdit) {
              $li.after(address);
              if (isSelect) {
                $li.next().trigger('click');
              }
              return $li.remove();
            }else{
				$ul.append( address );
			}
            $emptyWrap.after('<div class="address-list"><ul class="clearfix list-unstyled">' + address + '</ul></div>');
            $emptyWrap.remove();
            $address.find('li').trigger('click');
            return;
          }
          validator.formAjaxError(res, 'address', false);
        }, 'json');
      }
    });
  }

  validateForm();

  $cart.on('click', '.modify', function(event) {
    event.preventDefault();
    event.stopPropagation();
    var $t = $(this);
    $.get($t.data('href'), {
      memberId: memberId
    }, function(res) {
      if (res.state) {
        $address.find('.address-form form').html(template('address', res.data)).parent().removeClass('hide');
        areaSelectInit();
      }
    }, 'json');


  }).on('click', '.address-list li:not(.select)', function(event) {
    var $t = $(this).addClass('select');
    $t.siblings('.select').removeClass('select').find(':radio').prop('checked', false);
    $t.find(':radio').prop('checked', true).trigger('change');
  }).on('change', ':radio', function(event) {
	if( this.name == 'addressId'  ){
		$cart.find('.order-form [name="order[addressId]"]').val(this.value);
	}
  });

  input.priceOnly();

  $cart.on('change', '.date :checkbox', function(event) {
    $cart.find('.order-now .date span:last')[this.checked ? 'removeClass' : 'addClass']('hide');
  }).on('click', '.receiving-address .add', function(event) {
    event.preventDefault();
    $address.find('.address-form form').html(template('address', {
      memberId: memberId
    })).parent().removeClass('hide');
    areaSelectInit();
  }).on('change', '[name="CartForm[integral]"]', function() {
    var $integral = $cart.find('.integral-input');
    if (this.checked) return $integral.removeClass('hide');
    $integral.addClass('hide');
  }).on('mouseenter mouseleave', '.freight-info .item', function(event) {
    var $hide = $(this).parent().find('.freight-detail');
    if (event.type === 'mouseenter') return $hide.removeClass('hide');
    $hide.addClass('hide');
  }).on('click', '.delivery .add', function(event) {
    event.preventDefault();
    var $t = $(this);
    var $td = $t.parent('td');
    var $tr = $td.parent('tr');
    $tr.after(template('delivery', {
      colspan: $td.attr('colspan'),
      key: $td.data('key'),
      t: (new Date()).getTime()
    }));
    $t.addClass('hide');
  }).on('click', '.delivery .del', function(event) {
    event.preventDefault();
    var $t = $(this);
    var $tr = $t.parents('tr:first');

    if (!$t.prev().is('.hide')) {
      $tr.prev().find('.hide').removeClass('hide');
    }
    $tr.remove();

  }).on('change', '.firm-order .isfree', function() {
    var $tr = $(this).parents('tr');
    var $table = $tr.parents('table');
    var $total = $table.find('.list-body-foot span[data-price]');
    var price = $tr.data('price');
    var total = $total.data('price');
    total = this.checked ? (total - price) : (total + price);
    $total.data('price', total).text(input.int2Price(total));
    $tr.data('isfree', this.checked);
    updateTablePrice($table);
  }).on('blur', '.price-only', function() {
    updateTablePrice($(this).parents('table'));
  }).on('change', '.submit-info :checkbox', updateTotalPrice);



});
