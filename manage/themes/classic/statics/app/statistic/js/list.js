define(function(require, exports, module) {
  var input = require('modules/form/js/input.js');
  var $saleman = $('input[name="saleman"]');
  var $name = $('input[name="salemanName"]');
  var $product = $('input[name="product"]');
  var $memberName =  $('input[name="memberName"]');
  var $member =  $('input[name="member"]');
  input.suggestion($name, {
    cb: function($li) {
      $name.val($li.text());
      $saleman.val($li.data('id'));
    }
  });
  input.suggestion($product, {
    cb: function($li) {
      $product.val($li.text());
    }
  });
  input.suggestion($memberName, {
    cb: function($li) {
      $memberName.val($li.text());
      $member.val($li.data('id'));
    }
  });

  $memberName.change(function(){
    $member.val(0);
    });

  $name.change(function(){
    $saleman.val(0);
   });

	var nav=$(".headerss");
	var tableHead = $("#tableHead");
	var html = tableHead.html();
	nav.html( '<br>'+ html );

	var win=$(window);
	var sc=$(document);

	win.scroll(function(){
		if(sc.scrollTop()>=200){
			nav.removeClass("hide");
		}else{
			nav.addClass("hide");
		}
	});
});