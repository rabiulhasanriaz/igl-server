<script>
    function show_archived_details(campaign_id) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: 'POST',
            url: "{{ url('/ajax/showArchivedReportDetail') }}",
            data: {campaign_id: campaign_id},
            success: function (data) {
                $("#SmsInformation").html(data);
            }
        });
    }

</script>