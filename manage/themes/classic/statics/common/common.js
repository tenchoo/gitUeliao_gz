define(function(require, exports, module) {
  var $delconfirm = $('.del-confirm');
  $delconfirm.on('show.bs.modal', function(event) {
    var a = $(event.relatedTarget);
    var id = a.data('id');
    var rel = a.data('rel');
    $(this).find('.modal-footer .btn-success').attr({ 'data-id': id, 'data-rel': rel });
  }).on('click', '.btn-success', function() {
    var id = $(this).data('id');
    var rel = $(this).data('rel');
    $.get(rel, {
      id: id
    }, function(res) {
      if (res.state) {
        location.href = location.href;
      } else {
        if (res.message) {
          alert(res.message);
        }

      }
    }, 'json');
  });

  var $shelfconfirm = $('.shelf-confirm');
  $shelfconfirm.on('show.bs.modal', function(event) {
    var a = $(event.relatedTarget);
    var id = a.data('id');
    var rel = a.data('rel');
    var recipient = a.data('whatever');
    var modal = $(this);
    modal.find('.modal-title').text(recipient + '提醒');
    modal.find('.modal-body p').text('你确定要执行' + recipient + '操作吗？');
    modal.find('.modal-footer .btn-success').attr({ 'data-id': id, 'data-rel': rel });
  }).on('click', '.btn-success', function() {
    var id = $(this).data('id');
    var rel = $(this).data('rel');
    $.get(rel, {
      id: id
    }, function(res) {
      if (res.state) {
        location.href = location.href;
      } else {
        if (res.message) {
          alert(res.message);
        }
      }
    }, 'json');
  });

});
