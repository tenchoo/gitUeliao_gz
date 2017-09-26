define(function(require, exports, module) {
  var input = require('modules/form/js/input.js');
  var template = require('libs/arttemplate/3.0.0/template.js');
  input.intOnly();
  input.numFloatOnly();

  var totalNum;
  var buyNum;
  var unitConversion = 0;

  var ajaxLoading;

	var $packConfirm = $('.pack-confirm');
	var $form = $packConfirm.find('form');
	var $dialogContent = $packConfirm.find('.panel');

	$packConfirm.on('show.bs.modal', function(event) {
		var a = $(event.relatedTarget);
		var id = a.data('id');
		var p =  a.data('product');

		$(this).find('.modal-title').html( '分拣（'+p +'）' );

		$.get(location.href, {'orderProductId':id}, function(res) {
			if (res.state) {
				totalNum = res.data.num;
				buyNum = res.data.num;
				unitConversion = res.data.unitConversion;
				$dialogContent.html( template('pack-info',res.data ) );
			}
		}, 'json');
	}).on('click', '.btn-success', function(event) {
		event.preventDefault();
		if( totalNum > buyNum ){
			alert('分拣总数量不能大于购买数量');
			return;
		}

		if( totalNum < buyNum ){
			if( confirm('分拣总数量小于购买数量,确定提交保存吗 ？') ){
				postPack();
			}
		}else{
			postPack();
		}
	}).on('click', '.glyphicon-plus-sign', function() {
		$t = $(this).parents('.pieces-list');
		$t.append( template('piece-input',{} ) );
		changeTag();
	}).on('click', '.glyphicon-minus-sign', function() {
		$(this).parents('.packForm').remove();
		changeTag();
	}).on('change', 'input[name="wholeNum"]', function() {
		changeTag();
	}).on('change', '.packForm input', function() {
		changeTag();
	});

	function postPack(){
		var data = $form.serializeArray();
		$.post(location.href, data, function(res) {
		if (res.state) {
			location.href = location.href;
		}else{
			alert ( res.message || '保存失败，请稍后再试' );
		}
		}, 'json');
	}

	function changeTag(){
		var $tags = $packConfirm.find('span .tags');

		var $numShow = $packConfirm.find('span .packing-num');

		var $wholeObj = $packConfirm.find('input[name="wholeNum"]');

		var $t = parseInt( $wholeObj.val() );

		totalNum = unitConversion * $t *100;

		var $pieces = $packConfirm.find('.packForm input');

		$pieces.each( function(){
			var $i = $(this).val();
			$i = $i*100;
			if( $i> 0 ){
				totalNum = totalNum + $i;
				$t = $t+1;
			}
		} );
		totalNum = totalNum/100;

		$tags.html( $t );
		$numShow.html( totalNum );
	}
});