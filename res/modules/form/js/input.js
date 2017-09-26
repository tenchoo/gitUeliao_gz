define(function(require, exports, module) {
  require('libs/lodash/3.10.1/lodash.min.js');
  var template = require('libs/arttemplate/3.0.0/template.js');

  var $body = $('body');
  var cache = {};

  function intFormat(val) {
    return parseInt(val, 10) || 0;
  }

  function int2Price(price) {
    return ((parseInt(price, 10) || 0) / 100).toFixed(2);
    // var length;
    // var arr = [];
    // var r;
    // price = String(((parseInt(price, 10) || 0) / 100).toFixed(2)).split('.');
    // length = price[0].length;
    // if (length < 4) return price.join('.');
    // r = length % 3;
    // for (var i = price[0].length - 1; i > -1; i--) {
    //   arr.push(price[0][i]);
    //   if (i % 3 === r && i !== 0) {
    //     arr.push(',');
    //   }
    // }
    // return arr.reverse().join('') + '.' + price[1];
  }

  function price2Int(s) {
    return (s || '').replace(/\,/g, '') * 10 * 10 || 0;
  }

  function toggleStatus($t, val) {
    var min;
    var max;
    if ($t.is('[min]')) {
      min = parseFloat($t.attr('min'));
      if (min < val) {
        $t.trigger('enable', ['.minus']);
      } else {
        $t.trigger('disable', ['.minus']);
        $t.val(min).trigger('change');
      }
    }

    if ($t.is('[max]')) {
      max = parseFloat($t.attr('max'));
      if (val < max) {
        $t.trigger('enable', ['.plus']);
      } else {
        $t.trigger('disable', ['.plus']);
        $t.val(max).trigger('change');
      }
    }
  }

  function intOnly(id, opt) {
    id = id || '.int-only';
    opt = $.extend({
      format: true
    }, opt || {});
    $body.on('keydown', id, function(event) {
      if (event.ctrlKey) return;
      var which = event.which;
      var isShiftKey = event.shiftKey;
      var isLetter = 64 < which && which < 91; //字母
      var isSpe = 185 < which && which < 223; //特殊字符
      var isNumSpe = 105 < which && which < 112; //小键盘特殊字符
      var isShiftSpe = isShiftKey && 47 < which && which < 58; //shift + 数字
      if (isLetter || isSpe || isNumSpe || isShiftSpe) return event.preventDefault();
    }).on('blur', id, function(event) {
      var $t = $(this);
      var val = intFormat(this.value);
      if (opt.format) {
        $t.val(val).trigger('change');
      }
      toggleStatus($t, val);
    });
  }

  function priceOnly(id, opt) {
    id = id || '.price-only';
    opt = $.extend({
      format: true
    }, opt || {});
    $body.on('keydown', id, function(event) {
      if (event.ctrlKey) return;
      var val = this.value;
      var which = event.which;
      var isShiftKey = event.shiftKey;
      var isLetter = 64 < which && which < 91; //字母
      var isSpe = 185 < which && which < 223 && which !== 190; //特殊字符
      var isNumSpe = 105 < which && which < 112 && which !== 110; //小键盘特殊字符
      var isShiftSpe = isShiftKey && (47 < which && which < 58 || which === 190); //shift + 数字或.时
      var isDel = which === 110 || which === 190;
      var hasDot = val.indexOf('.') > -1;
      var hasDel = isDel && hasDot;
      if (isLetter || isSpe || isNumSpe || isShiftSpe || hasDel) return event.preventDefault();
    }).on('blur', id, function(event) {
      var $t = $(this);
      var val = int2Price(price2Int(this.value));
      if (opt.format) {
        $t.val(val.replace(/,/g, '')).trigger('change');
      }
      toggleStatus($t, val);
    });
  }


  function numFloatOnly(id, opt) {
    id = id || '.num-float-only';
    opt = $.extend({
      format: true
    }, opt || {});
    $body.on('keydown', id, function(event) {

      if (event.ctrlKey) return;

      var val = this.value;
      var which = event.which;
      var isShiftKey = event.shiftKey;
      var isLetter = 64 < which && which < 91; //字母
      var isSpe = 185 < which && which < 223 && which !== 190; //特殊字符
      var isNumSpe = 105 < which && which < 112 && which !== 110; //小键盘特殊字符
      var isShiftSpe = isShiftKey && (47 < which && which < 58 || which === 190); //shift + 数字或.时
      var isDel = which === 110 || which === 190;
      var hasDel = isDel && val.indexOf('.') > -1;
      if (isLetter || isSpe || isNumSpe || isShiftSpe || hasDel) return event.preventDefault();
    }).on('blur', id, function(event) {
      var $t = $(this);
      var val = int2Price(price2Int(this.value)).replace(/\d$/, '');
      $t.val(val);
      if (opt.format) $t.trigger('change');
      toggleStatus($t, parseFloat(val) || 0);
    });
  }

  exports.plusMinus = function(id, opt) {
    var $id;
    var val;
    id = id || '.int-only';
    opt = $.extend({
      minus: '.minus',
      plus: '.plus'
    }, opt || {});
    numFloatOnly(id);
    $body.on('click', opt.plus, function(event) {
      $id = $(this).siblings(id);
      val = (parseFloat($id.val()) * 10 + 10) / 10;
      $id.val(val).trigger('change');
      toggleStatus($id, val);
    }).on('click', opt.minus, function(event) {
      $id = $(this).siblings(id);
      val = (parseFloat($id.val()) * 10 - 10) / 10;
      $id.val(val).trigger('change');
      toggleStatus($id, val);
    }).on('enable', id, function(event, id) {
      $(this).siblings(id).removeClass('dis').prop('disabled', false);
    }).on('disable', id, function(event, id) {
      $(this).siblings(id).addClass('dis').prop('disabled', true);
    });

  };

  exports.clearZero = function($id) {
    $id
      .on('focus', function(event) {
        if (!parseFloat(this.value)) this.value = '';
      })
      .on('blur', function(event) {
        if (this.value === '') this.value = this.defaultValue;
      });
  };

  exports.intFormat = intFormat;

  exports.intOnly = intOnly;

  exports.priceOnly = priceOnly;

  exports.numFloatOnly = numFloatOnly;

  exports.formatPrice = int2Price;

  exports.int2Price = int2Price;

  exports.price2Int = price2Int;

  var suggestionTempate = '<div class="suggestion" style="{{style}}" id="{{name}}"><ul class="list-unstyled">{{each list}}<li data-id="{{$value.id}}">{{$value.title}}</li>{{/each}}</ul></div>';

  function getSuggestionData(option) {
    if (cache[option.name][option.keyword]) return option.cb(cache[option.name][option.keyword]);
    $.get(option.api, option.search, function(res) {
      if (res.state) {
        option.cb(res.data);
        cache[option.name][option.keyword] = res.data;
      } else {
        option.cb([]);
        cache[option.name][option.keyword] = [];
      }
    }, 'jsonp');
  }

  function renderSuggestion($id, data) {
    clearSuggestion($id);
    $id.after(template.compile(suggestionTempate)(data));
    $('#' + data.name)
      .on('mouseenter', 'li', function() {
        $(this).addClass('active');
      })
      .on('mouseleave', 'li', function() {
        $(this).removeClass('active');
      });
    cache.data = data.list;
  }

  function clearSuggestion($id) {
    $id.next('.suggestion').remove();
  }

  function getSuggestion($id) {
    return $('#' + $id.data('suggestion')).length > 0;
  }

  function activSuggestioneItem($id, which) {
    var $suggestion = $('#' + $id.data('suggestion'));
    var $active = $suggestion.find('li.active').removeClass('active');
    var isFirst = $active.is(':first-child');
    var isLast = $active.is(':last-child');
    if ($active.length > 0) {
      if (isFirst && which === 38) {
        $active = $suggestion.find('li:last');
      } else if (isLast && which === 40) {
        $active = $suggestion.find('li:first');
      } else {
        $active = $active[which === 40 ? 'next' : 'prev']();
      }
    } else {
      $active = $suggestion.find('li:' + (which === 38 ? 'last' : 'first'));
    }
    $id.val($active.addClass('active').text());

  }

  exports.suggestion = function suggestion($id, option) {
    var name = $id.data('suggestion');
    var timer;
    require.async('../css/suggestion.css');
    if (!name) return;
    cache[name] = {};
    option = $.extend({
      cb: function() {},
      er: function() {}
    }, option || {});

    var style = (function() {
      var position = $id.position();
      return 'top:' + (intFormat(position.top) + intFormat($id.outerHeight()) - 1) + 'px;left:' + position.left + 'px;width:' + $id.width() + 'px;';
    }());

    $id.on('keydown', function(event) {
      var which = event.which;

      if ((which === 38 || which === 40) && getSuggestion($id)) {
        event.preventDefault();
        return activSuggestioneItem($id, which);
      }

      if (which === 13 && getSuggestion($id)) {
        event.preventDefault();
        var $active = $('#' + name).find('li.active');
        if ($active.length > 0) {
          $active.trigger('click');
        }
      }


    }).on('keyup focus', function(event) {
      var keyword = this.value;
      var which = event.which;
      if (which === 38 || which === 40 || which === 13) {
        return;
      }
      if (which === 27) {
        event.preventDefault();
        return clearSuggestion($id);
      }

      if (keyword !== '') {
        getSuggestionData({
          keyword: keyword,
          name: name,
          api: $id.data('api'),
          search: $id.data('search').replace('%s', keyword),
          cb: function(data) {
            if (data.length === 0) {
              clearSuggestion($id);
            } else {
              renderSuggestion($id, {
                name: name,
                style: style,
                list: data
              });
            }
          }
        });
      } else {
        clearSuggestion($id);
      }
    }).on('blur', function(event) {
      timer = setTimeout(function() {
        if ($id.val() !== $id.data('prev')) {
          $id.data('prev', $id.val());
          $id.data('error', true);
          option.er($id);
        }
        clearSuggestion($id);
      }, 200);
    }).parent().on('click', '#' + name + ' li', function(event) {
      clearTimeout(timer);
      var $t = $(this);
      var text = $t.text();
      $id.val(text);
      if ($id.data('error') || $id.data('prev') !== text) {
        $id.data('prev', text);
        $id.data('error', false);
        option.cb($t, _.find(cache.data, {
          id: $t.data('id').toString()
        }));
      }
      clearSuggestion($id);
    });
  };


});
