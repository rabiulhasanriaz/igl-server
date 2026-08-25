<script>
    function get_category_name(cat_id) {
        var cat_id = cat_id;
        $("#ContactCategoryId").val(cat_id);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: 'POST',
            url: "{{ url('/ajax/getCategoryNameForEdit') }}",
            data: {cat_id: cat_id},
            success: function (data) {
                $("#edit_category_name").val(data);
            }
        });
    }


    function get_phone_number(contact_id) {

        var contact_id = contact_id;
        $("#contact_edit_id").val(contact_id);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: 'POST',
            url: "{{ url('/ajax/getPhoneNumberForEdit') }}",
            data: {contact_id: contact_id},
            success: function (data) {
                console.log(data);
                $("#contact_number_edit").val(data);
            }
        });
    }
</script>