$(function () {
    //alert('ok');
    /*
     * 自定义校验 $.validator.addMethod(mehodName,function(value,element,param){
     * 
     * },msg);
     */
    $.validator.addMethod("isMobile", function (value, element) {
        var length = value.length;
        var mobile = /^1[3|4|5|7|8]\d{9}$/;
        return this.optional(element) || (length == 11 && mobile.test(value));
    }, "请正确填写您的手机号码");

    $.validator.addMethod("isName", function (value, element) {
        var name = /^[A-Za-z0-9_\-\u4e00-\u9fa5]+/;
        return this.optional(element) || name.test(value);
    }, "请正确填写您的姓名");

    $("#myForm").validate({
        //debug: true,
        rules: {
            'data[phone]': {
                required: true,
                minlength: 11,
                isMobile: true
            },
            'data[driverName]': {
                required: true,
                minlength: 2,
                isName: true
            }
        },
        messages: {
            'data[phone]': {
                required: "请输入手机号",
                minlength: "确认手机号码不能小于11个数字",
                isMobile: "请正确填写您的手机号码"
            },
            'data[driverName]': {
                required: "请输入姓名",
                minlength: "最小长度两个字符",
                isName: "请正确的输入您的姓名"

            }
        }
    });
})
