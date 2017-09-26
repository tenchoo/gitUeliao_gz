define(function(require, exports, module) {
  var dialog = require('modules/dialog/js/dialog.js');
  var $content = $('.content-wrap');


  $content.on('click', '[data-type="audio"]', function(e) {
    e.preventDefault();
    var audio = $(this).attr('href');
    if ($content.find('[data-id="' + audio + '"]').length) {
      return $content.find('[data-id="' + audio + '"]').trigger('play');
    }
    $content.append('<div class="hide"><audio autoplay data-id="' + audio + '"><source src="' + audio + '" type="audio/mp3" /><source src="' + audio + '" type="audio/ogg" /><embed height="100" width="100" src="' + audio + '" /></audio></div>');
  }).on('click', '[data-type="image"]', function(e) {
    e.preventDefault();
    dialog.image('<img src="' + $(this).attr('href') + '" alt="" />');
  }).on('click', '.edit', function(e) {
    e.preventDefault();
    var $t = $(this);
    var $div = $(this).parent().prev().find('div:last').hide();
    $div.after('<form method="post" action="' + $t.attr('href') + '" class="form-horizontal"><div class="form-group"><div class="col-md-4"><textarea name="content" class="form-control pull-left">' + $div.text() + '</textarea></div><div class="pull-left" style="padding-top:35px"><a href="javascript:" class="save">保存</a> <a href="javascript:" class="cancel">取消</a></div></div></form>');
  }).on('click', '.save', function(e) {
    e.preventDefault();
    $(this).parents('form').trigger('submit');
  }).on('click', '.cancel', function(e) {
    e.preventDefault();
    var $form = $(this).parents('form');
    $form.prev().show();
    $form.remove();
  });
});