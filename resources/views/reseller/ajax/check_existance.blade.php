<script type="text/javascript">
    /*check phone existence for new Employee registration*/
    function checkPhoneExistence(phone) {
        var phone = phone;
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: 'POST',
            url: "{{ url('/ajax/checkEmployeePhoneExistence') }}",
            data: {phone: phone},
            success: function (data) {
                if (data == 'valid') {
                    $(".invalid-phone").text('');
                    $(".retErrPhone").text('');
                    $(".valid-phone").text("Available...");
                }
                else if (data == 'invalidPhone') {
                    $(".valid-phone").text('');
                    $(".retErrPhone").text('');
                    $(".invalid-phone").text("Please enter a valid cell no...");
                }
                else if (data == 'exist') {
                    $(".valid-phone").text('');
                    $(".retErrPhone").text('');
                    $(".invalid-phone").text("Already exist... please use another");
                }
            }
        });
    }


    $("#employeeCommision").tooltip({title: "Set a commission in Percentage"} );
</script>

