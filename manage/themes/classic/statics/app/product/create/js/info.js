define(function(require, exports, module) {
  var $uploader;
  var $details = $('.details');
  var editor = require('modules/editor/js/editor.js');
  var $pc = $details.find('.pc');
  var $mobile = $details.find('.mobile');
  var $title = $('.title-group');
  var $addon = $title.find('.input-group-addon');

  require('./sales.js');
  require('./craft.js');

  editor('#pc');
  editor('#testresults');

  function getEnLeng(str) {
    return (str || '').replace(/[^\x00-\xff]/g, "xx").length;
  }

  function renderTip(length) {
    if (length > 30) {
      length = '<strong class="text-danger">' + length + '</strong>';
    }
    $addon.html(length + '/30');
  }

  $title.on('keyup', 'input', function(event) {
    renderTip(Math.ceil(getEnLeng(this.value)/2));
  }).find('input').trigger('keyup');

  require('modules/uploader/js/uploader.js').uploader({
    success: function(file, res) {
      $uploader = $('#rt_' + file.source.ruid).parents('.uploader');
      $uploader.find('button').html('<img src="' + seajs.data.uploaderPath + '/../..' + res.data + '" alt="" width="80" height="80"><span class="bg"></span><span>重新上传</span>');
      $uploader.find('[name="pictures[]"]').val(res.data);
    },
    formData: {
      'case': 'product'
    }
  });

  $details.on('click', '.nav li', function(event) {
    var $t = $(this);
    var client = $t.attr('for');
    $t.addClass('active').siblings('.active').removeClass('active');
    if (client === 'mobile') {
      $mobile.removeClass('hide');
      $pc.addClass('hide');
      if (!$t.data('editor')) {
        editor('#mobile');
        $t.data('editor', true);
      }
      return;
    }
    $mobile.addClass('hide');
    $pc.removeClass('hide');

  });

});