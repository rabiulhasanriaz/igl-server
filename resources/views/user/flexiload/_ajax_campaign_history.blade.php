<script>
    function show_campaign_details(campaign_id) {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        url = 'flexiload/get-campaign-data-by-ajax';

        $.ajax({
            type: 'POST',
            url: "{{ url('/flexiload/get-campaign-data-by-ajax') }}",
            data: {campaign_id: campaign_id},
            success: function (data) {
                console.log(data);
                $("#SmsInformation").html(data);
            }
        });
    }


    function show_current_month_campaign_details(campaign_id) {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        url = 'flexiload/get-campaign-data-by-ajax';

        $.ajax({
            type: 'POST',
            url: "{{ url('/flexiload/get-current-month-campaign-data-by-ajax') }}",
            data: {campaign_id: campaign_id},
            success: function (data) {
                $("#SmsInformation").html(data);
            }
        });
    }

</script>
