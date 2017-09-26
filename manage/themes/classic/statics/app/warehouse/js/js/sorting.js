var nub = 1;
var htmlbtn = '<div class="new-div"><button onclick="myclickDel(this)" class="btn btn-default">x</button><button onclick="myclick(this)"  class="btn btn-default">+</button><span>米</span></div> ';
var htmlbtn1 = '<button onclick="myclick(this)" class="btn btn-default">+</button><span>米</span>';
var htmlbtn2 = '<button onclick="myclickDel(this)" class="btn btn-default">x</button>';
function myclick(btn) {
    nub++;
    var htmltxt = "<div class='new-div'><input type='text' class='txt' name='remainder[" + nub + "]' id='z" + nub + "'/></div>";
    var tr = $(btn).parents("tr").eq(0);
    console.log(tr);
    tr.after("<tr class='pull-left'><td>" + htmltxt + "</td><td>" + htmlbtn + "</td></tr>");
    var newbtn = $(btn).parent();
    $(newbtn).empty();

    if ($(newbtn).parents("table").eq(0).find("input[type='text']").length > 2) {
        $(newbtn).html(htmlbtn2);
    }
};
function myclickDel(btn) {
    if ($(btn).parents('td').eq(0).find('button').length > 1) {
        $(btn).parents('tr').eq(0).prev().eq(0).find('td').eq(1).append(htmlbtn1);
    }
    $(btn).parents('tr').eq(0).remove();
};
function change() {
    var inputTime = $('.input-data').val();
    $('.span-data').html(inputTime);
};
$(function () {
    $('#date').fdatepicker({
        format: 'yyyy-mm-dd '
    });
});


 function sort(orderProductId,singleNumber,orderId,positionId,unit,num,name,user,memo,warehouse) {
     $("#sort").modal('show');
       // alert(singleNumber);
        $('.title').text(singleNumber);
        $('.order').text(orderId);
        $('.unit').text(unit);
        $('.nums').text(num);
        $('.name').text(name);
        $('.packUser').text(user);
        $('.memo').text(memo);
        $('.warehouse').text(warehouse);
        $('input[name=positionId]').val(positionId);
        $('input[name=orderProductId]').val(orderProductId);
        $('input[name=unit]').val(unit);
        $('input[name=single]').val(singleNumber);
        $('input[name=remark]').val(memo);

        $("#sort").modal('show');
       // $('#report').modal('show');
     }

