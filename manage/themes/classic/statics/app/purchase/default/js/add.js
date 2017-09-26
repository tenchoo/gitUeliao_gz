define(function(require, exports, module) {
  var input = require('modules/form/js/input.js');
  input.numFloatOnly();

  var $delconfirm = $('.del-confirm');
  $delconfirm.on('show.bs.modal', function(event) {
    var a = $(event.relatedTarget);
    var id = a.data('id');
    $(this).find('.modal-footer .btn-success').attr({ 'data-id': id });
  }).on('click', '.btn-success', function() {
    var id = $(this).data('id');
    $.get('/purchase/default/add', {
      id: id,
      event: 'remove'
    }, function(res) {
      if (res.state) {
        location.href = location.href;
      }
    }, 'json');
  });

  var $supplier = $('input[name="form[supplierName]"]');
  var $serial = $('input[name="form[supplierSerial]"]');
  var $contact = $('input[name="form[supplierContact]"]');
  var $phone = $('input[name="form[supplierPhone]"]');
  var $id = $('input[name="form[supplierId]"]');
  var $s = $('#serialnumber');
  var $c = $('#contact');
  var $p = $('#phone');
  var cache;
  input
    .suggestion($supplier, {
      er: function() {
        $serial.val('');
        $s.text('');
        $contact.val('');
        $c.text('');
        $phone.val('');
        $p.text('');
        $id.val('');
      },
      cb: function($li, data) {
        cache = data;
        $serial.val(cache.code);
        $s.text(cache.code);
        $.get('/api/supplier_by_code', {
          code: cache.id
        }, function(res) {
          if (res.state) {
            $contact.val(res.data.contact);
            $c.text(res.data.contact);
            $phone.val(res.data.phone);
            $p.text(res.data.phone);
            $id.val(res.data.supplierId);
          }

        }, 'json');
      }
    });
  /*$supplier.on('blur',function(){
    $serial.val(cache.code);

  });*/


});
