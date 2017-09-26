define(function (require, exports, module) {
    var input = require('modules/form/js/input.js');
    input.intOnly();

    // var $pay = $('.paymonthly');
    // var $monthly = $pay.find('[name="monthlyType"]');

    // if($pay.find('[name="payModel[1]"]').prop('checked')){
    // $monthly.prop('disabled',false);
    // }
    // $pay.on('click','[name="payModel[1]"]',function(){
    // if(this.checked){
    // $monthly.prop('disabled',false);
    // }else{
    // $monthly.prop('disabled',true);
    // }
    // });

    var $monthlyPay = $('#monthlyPay');
    if ($monthlyPay.prop('checked')) {
        $('#credit').prop('disabled', false);
        $('#billingCycle').prop('disabled', false);
    }

    $monthlyPay.on('click', function () {
        if (this.checked) {
            $('#credit').prop('disabled', false);
            $('#billingCycle').prop('disabled', false);
        } else {
            $('#credit').prop('disabled', true);
            $('#billingCycle').prop('disabled', true);
        }
    });


});
