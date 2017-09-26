define(function(require, exports, module) {
  var template = require('libs/arttemplate/3.0.0/template.js');
  var cache = {
    p: {
      prev: '<option value="" >请选择职位</option>',
      id: 'regions',
      $id: $('[name="form[depPositionId]"]')
    },
    r: {
      prev: '',
      id: 'roleGroups',
      $id: $('.group')
    }
  };


  function render(id, t) {
    if (cache[t][id]) {
      cache[t]['$id'].html(cache[t].prev + template(cache[t]['id'], cache[t][id]));
    } else {
      $.get(t === 'p' ? getpositionUrl : getRolesUrl, {
        id: id
      }, function(res) {
        if (res.state) {
          cache[t][id] = res;
          cache[t]['$id'].html(cache[t].prev + template(cache[t]['id'], cache[t][id]));
        }
      }, 'json');
    }



  }


  $('[name="form[departmentId]"]').on('change', function() {
    render(this.value, 'p');
    render(this.value, 'r');
  });

});