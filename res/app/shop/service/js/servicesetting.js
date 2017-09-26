define(function(require, exports, module) {
	var dialog = require('modules/dialog/js/dialog.js');
	var validator = require('modules/form/js/validator.js');
  var $service = $('.service-list');
  var $serviceList = $service.find('.list-page-body');
  var $form = $('.add-service form');
  var template = require('libs/arttemplate/3.0.0/template.js');
  function updateList() {
  	$serviceList.find('tr .move').html('<i class="icon icon-control icon-blue-up"></i>\n<i class="icon icon-control icon-blue-down"></i>');
  	$serviceList.find('tr:first-child .icon-blue-up').removeClass('icon-blue-up').addClass('icon-blue-up-dis');
  	$serviceList.find('tr:last-child .icon-blue-down').removeClass('icon-blue-down').addClass('icon-blue-down-dis');
  }
  function del(csid) {
    dialog.confirm('确定删除？', function() {
      $.post('/ajax/shopcs', {
        optype: 'del',
        csId: csid
      }, function(res) {
        if (res.state) {
          location.href = location.href;
        } else {
          dialog.tip(res.message || '删除失败，请稍后重试！');
        }
      }, 'json');
    });
  }
  $service.on('click', '.icon-blue-down', function(event) {
    var $t = $(this);
    var $tr = $t.parents('tr:first');
    $.post('/ajax/shopcs', {
      optype: 'move',
      csId: $tr.data('csid'),
      goto: 'down'
    }, function(res) {
      if (res.state) {
      	$tr.insertAfter($tr.next());
      	updateList();
      }
    }, 'json');
  }).on('click', '.icon-blue-up', function(event) {
    $(this).parents('tr:first').prev().find('.icon-blue-down').trigger('click');
  }).on('click','[name="dataForm[default]"]',function(event){
  	event.preventDefault();
  	var $t = $(this);
  	$.post('/ajax/shopcs', {
      optype: 'setDefault',
      csId: $(this).parents('tr:first').data('csid')
    },function(res) {
    	if(res.state) {
    		$t.prop('checked', true);
    	}
    }, 'json');
  }).on('click', 'a.del', function(event) {
    event.preventDefault();
    del($(this).parents('tr:first').data('csid'));
  }).on('click','a.edit',function(){
  	$('.add-service .hd .title').html('编辑客服');
  	$.post('/ajax/shopcs', {
      optype: 'edit',
      csId: $(this).parents('tr:first').data('csid'),
    }, function(res) {
    	if (res.state) {
    		$form.html(template('formTemplate',res.data));
    		$form.find(':radio[value="'+res.data.type+'"]').prop('checked',true);
    	}
    }, 'json');
  });
  $form.validate({
    rules: {
      'dataForm[csName]': {
        required: true,
        rangelength:[2,10]
      },
      'dataForm[csAccount]': {
        required: true,
        rangelength:[2,20]
      }
    },
    messages: {
      'dataForm[csName]': {
        required: '不能为空',
        rangelength:'请输入2-10个字符'
      },
      'dataForm[csAccount]': {
        required: '不能为空',
        rangelength:'请输入2-20个字符'
      }
    },
    submitHandler: function(form) {
      validator.formAjax($form, 'dataForm', true);
    }
  });
  
  
});