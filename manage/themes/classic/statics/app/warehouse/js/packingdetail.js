   function report(orderProductId,title,orderId,positionId,unit,num,name,user,memo,warehouse) {
        $('.title').text(title);
        $('.order').text(orderId);
        $('.unit').text(unit);
        $('.num').text(num);
        $('.name').text(name);
        $('.packUser').text(user);
        $('.memo').text(memo);
        $('.warehouse').text(warehouse);
        $('input[name=positionId]').val(positionId);
        $('input[name=orderProductId]').val(orderProductId);
        $('input[name=unit]').val(unit);
        $('input[name=remark]').val(memo);

        $("#report").modal('show');
        }



 // function sort(orderProductId,title,orderId,positionId,unit,num,name,user,memo,warehouse) {
 //        alert(title);
 //        $('.title').text(title);
 //        $('.order').text(orderId);
 //        $('.unit').text(unit);
 //        $('.nums').text(num);
 //        $('.name').text(name);
 //        $('.packUser').text(user);
 //        $('.memo').text(memo);
 //        $('.warehouse').text(warehouse);
 //        $('input[name=positionId]').val(positionId);
 //        $('input[name=orderProductId]').val(orderProductId);
 //        $('input[name=unit]').val(unit);
 //        $('input[name=remark]').val(memo);
 //         $('input[name=singleNumber]').val(title);
 //       // $("#sort").modal('show');
 //        $('#sort').modal('show');
 //     }