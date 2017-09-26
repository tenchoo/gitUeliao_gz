define(function (require, exports, module) {
    var getCheckds = require('modules/checkedall/js/checkedall.js')();
    var dialog = window;
    var $content = $('.content-wrap');

    var $distribution = $('.distribution-confirm');
    var $form = $distribution.find('form');
    $distribution.on('show.bs.modal', function (event) {
        var $checkeds = getCheckds();
        var ids = [];

        if ($checkeds.length < 1) {
            dialog.alert('请选择数据后操作');
            return false;
        }
        $checkeds.each(function () {
            ids.push($(this).val());
        });
        $(this).find('[name="memberId"]').val(ids);

    }).on('click', '.btn-success', function () {
        $.post($form.attr('action'), $form.serializeArray(), function (res) {
            if (res.state) {
                location.href = location.href;
            }
        }, 'json');
    });

    $content
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
