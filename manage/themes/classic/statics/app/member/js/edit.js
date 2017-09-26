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
            'form[account]': {
                required: true,
                minlength: 11,
                isMobile: true
            },
            'form[username]': {
                required: true,
                minlength: 2,
                isName: true
            },
            'form[password]': {
                required: true,
                rangelength: [5, 16]
            },
            'form[repassword]': {
                required: true,
                rangelength: [5, 16],
                //equalTo : "#password"
                equalTo: "input[type='password']"
            },
            'form[departmentId]': {
                required: true
            },
            'form[depPositionId]': {
                required: true
            },
            'form[printerId]': {
                required: true
            }
        },
        messages: {
            'form[account]': {
                required: "请输入手机号",
                minlength: "确认手机号码不能小于11个数字",
                isMobile: "请正确填写您的手机号码"
            },
            'form[username]': {
                required: "请输入姓名",
                minlength: "最小长度两个字符",
                isName: "请正确的输入您的姓名"

            },
            'form[password]': {
                required: "请输入密码",
                rangelength: "长度5-16之间的数字或字母"
            },
            'form[repassword]': {
                required: "请输入确认密码",

                rangelength: "长度5-16之间的数字或字母",
                equalTo: "两次密码不一样"
            },
            'form[departmentId]': {
                required: "必须选择"
            },
            'form[depPositionId]': {
                required: "必须选择"
            },
            'form[printerId]': {
                required: "必须选择"
            }
        }
    });
})
