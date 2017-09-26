define(function (require, exports, module) {
    var getCheckds = require('modules/checkedall/js/checkedall.js')();
    $('.btn-export').on('click', function () {
        if (getCheckds().length) {
            console.log('导出操作');
            return;
        }
        alert('请选择数据后操作！');
    });

    $('.content-wrap')
        .on('click', '.del', function (event) {
            event.preventDefault();
            var $t = $(this);
            if (confirm('确定冻结？')) {
                $.get($t.data('rel'), {id: $t.data('id')}, function (res) {
                    if (res.state) {
                        location.href = res.data;
                        return;
                    }
                    alert(res.message || '冻结失败，请稍后重试');
                }, 'json');
            }
        })
        .on('click', '.undel', function (event) {
            event.preventDefault();
            var $t = $(this);
            if (confirm('确定解冻?')) {
                $.get($t.data('rel'), {id: $t.data('id')}, function (res) {
                    if (res.state) {
                        location.href = res.data;
                        return;
                    }
                    alert(res.message || '解冻失败，请稍后重试');
                }, 'json');
            }
        });
});
