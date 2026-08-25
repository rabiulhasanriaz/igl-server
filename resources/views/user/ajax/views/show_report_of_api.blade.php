<table id="api_archived_report" class="table table-striped table-bordered table-hover">
    <thead>
        <tr>
            <th>SL</th>
            <th>Campaign Title</th>
            <th>Submit time</th>
            <th>SenderID</th>
            <th>Submitted</th>
            <th>Total sent</th>
            <th>Charge</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
        @php($serial = 1)

        @foreach($api_reports as $api_report)
            <tr>
                <td>{{ $serial++ }}</td>

                <td title="{{ $api_report->sci_campaign_title }}">
                    {{ $api_report->sci_campaign_id }}
                </td>

                <td>
                    @if(!empty($api_report->sci_targeted_time))
                        {{ $api_report->sci_targeted_time->format('H:i a, d-M-Y') }}
                    @endif
                </td>

                <td>{{ optional($api_report->sender)->sir_sender_id }}</td>

                <td class="text-center">
                    {{ $api_report->sms_count }}
                </td>

                <td class="text-center">
                    {{ $api_report->sms_count }}
                </td>

                <td class="text-right">
                    BDT {{ number_format($api_report->sci_total_cost, 2) }}
                </td>

                <td>
                    <a href="#my-modal"
                       onclick="show_archived_details('{{ $api_report->id }}')"
                       role="button"
                       data-toggle="modal"
                       class="btn-none-edit CampaignId_one">
                        View
                    </a>
                    |
                    <a href="{{ route('user.reports.download_archived_report', $api_report->id) }}"
                       target="_blank"
                       class="btn-none-download">
                        Download
                    </a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>