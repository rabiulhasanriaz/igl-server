<script>
    function customer_balance(customerId) {
        var cId = customerId;
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: 'POST',
            url: "{{ url('/ajax/checkCustomerAvailableBalance') }}",
            data: {cId: cId},
            success: function (data) {
                var result = Number(data).toFixed(2);
                $("#CustomerBalance").html("<b>Reseller Balance : </b><span id='balanceOfCustomer'>"+result+"</span>");
            }
        });
    }


    function user_balance(customerId) {
        var cId = customerId;
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: 'POST',
            url: "{{ url('/ajax/checkUserAvailableBalance') }}",
            data: {cId: cId},
            success: function (data) {
                var result = Number(data).toFixed(2);
                $("#CustomerBalance").html("<b>User Balance : </b><span id='balanceOfCustomer'>"+result+"</span>");
            }
        });
    }
</script>
