<script>
    /*check email existence for new user registration*/
    function checkEmailExistence(email) {
        var email = email;
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: 'POST',
            url: "{{ url('/ajax/checkEmailExistence') }}",
            data: {email: email},
            success: function (data) {
                if (data == 'valid') {
                    $(".invalid-email").text('');
                    $(".retErrEmail").text('');
                    $(".valid-email").text("Available...");
                }
                else if (data == 'invalidEmail') {
                    $(".valid-email").text('');
                    $(".retErrEmail").text('');
                    $(".invalid-email").text("Please enter a valid email...");
                }
                else if (data == 'exist') {
                    $(".valid-email").text('');
                    $(".retErrEmail").text('');
                    $(".invalid-email").text("Already exist... please use another");
                }
            }
        });
    }


    /*check phone existence for new user registration*/
    function checkPhoneExistence(phone) {
        var phone = phone;
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: 'POST',
            url: "{{ url('/ajax/checkPhoneExistence') }}",
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


    /*check employee phone existence for new user registration*/
    function checkEmployeePhoneExistence(phone) {
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


    /*check email existence for update a user*/
    function checkEmailExistenceForUpdate(email, user_id){
        var email = email;
        var user_id = user_id;
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: 'POST',
            url: "{{ url('/ajax/checkEmailExistenceForUpdate') }}",
            data: {email: email, user_id: user_id},
            success: function (data) {
                if (data == 'valid') {
                    $(".invalid-email").text('');
                    $(".retErrEmail").text('');
                    $(".valid-email").text("Available...");
                }
                else if (data == 'invalidEmail') {
                    $(".valid-email").text('');
                    $(".retErrEmail").text('');
                    $(".invalid-email").text("Please enter a valid email...");
                }
                else if (data == 'exist') {
                    $(".valid-email").text('');
                    $(".retErrEmail").text('');
                    $(".invalid-email").text("Already exist... please use another");
                }
            }
        });
    }



    /*check phone existence for update user*/
    function checkPhoneExistenceForUpdate(phone, user_id, e) {
        e.preventDefault();
        var phone = phone;
        var user_id = user_id;
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: 'POST',
            url: "{{ url('/ajax/checkPhoneExistenceForUpdate') }}",
            data: {phone: phone, user_id: user_id},
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
        return false;
    }
</script>