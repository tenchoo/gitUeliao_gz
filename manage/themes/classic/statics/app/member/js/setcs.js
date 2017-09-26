$(function() {
   // alert('ok');
    /*
     * 自定义校验 $.validator.addMethod(mehodName,function(value,element,param){
     * 
     * },msg);
     */

    $.validator.addMethod("isName", function(value, element) {
        var name = /^[A-Za-z0-9_\-\u4e00-\u9fa5]+/;
        return this.optional(element) || name.test(value);
    }, "请正确填写客服类型");

    $("#myForm").validate({
        //debug: true,
        rules: {
            'data[csAccount]': {
                required: true,
                maxlength: 20
            },
            'data[csName]': {
                required: true,
                minlength: 4,
                isName: true
            }
        },
        messages: {
            'data[csAccount]': {
                required: "请输入QQ号（或旺旺号）",
                minlength: "请正确的输入QQ号（或旺旺号）"
            },
            'data[csName]': {
                required: "请输入客服类型",
                minlength: "最小长度四个字符",
                isName: "请正确的输入客服类型"
            }
        }
    });
})
