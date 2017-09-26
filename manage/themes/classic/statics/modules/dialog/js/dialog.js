define(function(require, exports, module) {
  require('../css/style.css');
  var $body = $('body');
  var timer;

  function create(message, type, title) {
    clearTimeout(timer);
    dialogClose();
    var html = '';
    switch (type) {
      case 'confirm':
        html += ['<div class="dialog dialog-' + type + '">',
          '      <div class="dialog-content">',
          '        <div class="dialog-header">',
          '          <button class="dialog-close" type="button"></button>',
          '          <h4 class="dialog-title">' + (title || '提示') + '</h4>',
          '        </div>',
          '        <div class="dialog-body">',
          '          <p>' + message + '</p>',
          '        </div>',
          '        <div class="dialog-footer">',
          '          <button class="dialog-btn-success" type="button">确定</button>',
          '          <button class="dialog-btn-cancel" type="button">取消</button>',
          '        </div>',
          '      </div>',
          '    </div>'
        ].join('');
        break;
      case 'tip':
      case 'warn':
      case 'error':
      case 'success':
        html += '<div class="dialog dialog-' + type + '">' + message + '</div>';
        break;
      default:
        html += ['<div class="dialog dialog-' + type + '">',
          '      <div class="dialog-content">',
          '        <div class="dialog-header">',
          '          <button class="dialog-close" type="button"></button>',
          '          <h4 class="dialog-title">' + (title || '提示') + '</h4>',
          '        </div>',
          '        <div class="dialog-body">',
          message,
          '        </div>',
          '        <div class="dialog-footer">',
          '        </div>',
          '      </div>',
          '    </div>'
        ].join('');
    }
    $body.append(html);
    var $dialog = $('.dialog-' + type);

    setTimeout(function() {
      var width = $dialog.width() + 1;
      var height = $dialog.height() + 1;
      $dialog.css({
        width: width,
        marginLeft: 0 - width / 2,
        marginTop: 0 - height / 2,
        visibility: 'visible'
      });
    });

  }

  function dialogClose() {
    $body.find('.dialog,.dialog-tip').remove();
  }

  $body.on('click', '.dialog-close,.dialog-btn-cancel', dialogClose);

  exports.close = dialogClose;

  exports.confirm = function(message, options) {
    options = $.extend({
      okFun: function() {

      },
      init: function() {

      }
    }, options ? $.isPlainObject(options) ? options : {
      okFun: options
    } : {});
    create(message, 'confirm');
    options.init($('.dialog-confirm'));
    $body.find('.dialog-btn-success').on('click', function(argument) {
      options.okFun($('.dialog-confirm'));
      dialogClose();
    });
  };

  exports.tip = function(message, options) {
    options = $.extend({
      time: 3000,
      cb: function() {},
      type: 'tip'
    }, options || {});
    create(message, options.type);
    timer = setTimeout(function() {
      dialogClose();
      options.cb();
    }, options.time);
  };

  exports.create = create;

  exports.alert = exports.tip;

});