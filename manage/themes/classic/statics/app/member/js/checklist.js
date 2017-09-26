define(function (require, exports, module) {
    var getCheckds = require('modules/checkedall/js/checkedall.js')();
    $('.btn-export').on('click', function () {
        if (getCheckds().length) {
            console.log('导出操作');
            return;
        }
        alert('请选择数据后操作！');
    });
});