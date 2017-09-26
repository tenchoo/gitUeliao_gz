define(function(require, exports, module) {
  var dialog = require('modules/dialog/js/dialog.js');
  require('../css/login.css');
  var $body = $('body');

  var loginUrl = seajs.data.apiPath + '/user/popuplogin.html?done=' + encodeURIComponent(location.protocol + '//' + location.host + '/default/proxy.html');

  $body.on('click', '.dialog-close,.dialog-btn-cancel', function() {
    $body.trigger('loginclose').off('loginsuccess');
  }).on('loginsuccess.login', function() {
    $body.find('.dialog-login').remove();
    $body.trigger('loginclose');
  });

  exports.open = function() {
    dialog.create('<iframe width="480" height="240" src="' + loginUrl + '" height="" frameborder="0" scrolling="no"></iframe>', 'login', '登录');
    $body.trigger('loginopen');
  };

});