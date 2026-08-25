<script>
    function checkSenderIdExistence(senderId) {
        //var senderId = senderId;
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: 'POST',
            url: "{{ url('/ajax/checkSenderIdExistence') }}",
            data: {senderId: senderId},
            success: function (data) {
                if (data == 'valid') {
                    $(".duplicate-senderId").text('');
                    $(".retErrSenderId").text('');
                    $(".valid-senderId").text("Available...");
                }
                else if (data == 'exist') {
                    $(".valid-senderId").text('');
                    $(".retErrSenderId").text('');
                    $(".duplicate-senderId").text("Already exist... please use another");
                }
            }
        });
    }
</script>