define(function(require, exports, module) {
  var login = require('modules/login/js/login.js');
  var $body = $('body');

  $('a').on('click', login.open);

  $body.on('loginopen',function(event){
    console.log(event);
  }).on('loginclose',function(event){
    console.log(event);
  }).on('loginsuccess',function(event){
    console.log(event);
  });

});