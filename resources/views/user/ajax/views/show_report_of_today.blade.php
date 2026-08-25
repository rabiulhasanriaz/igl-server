
<table id="view_archived_report" class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th>SL</th>
            <th>Campaign Title</th>
            <th class="hidden-600">Submit time</th>
            <th>SenderID</th>
            <th>SMS Count</th>
            <th>Total sent</th>
            <th>Charge</th>
            <th>Action</th>
        </tr>
        </thead>
    
        <tbody>
        @php($serial=1)
        @foreach($todays_reports as $today_report)
        <tr>
            <td>{{ $serial++ }}</td>
            <td>{{ $today_report->sci_campaign_id }}</td>
            <td>
                @if(!empty($today_report->sci_targeted_time))
                {{ $today_report->sci_targeted_time->format('H:i a, d-M-Y') }}
                @endif
            </td>
            <td>{{ optional($today_report->sender)->sir_sender_id }}</td>
            <td class="text-center">{{ $today_report->report_sms_count }}</td>
            <td class="text-center">{{ $today_report->sci_total_submitted }}</td>
            <td class="text-right">BDT {{ number_format($today_report->sci_total_cost, 2) }}</td>
            <td>
                <label>
                    <a href="#my-modal" onclick="show_today_details('{{$today_report->id}}')"
                        role="button" data-toggle="modal"
                        class="btn-none-edit CampaignId_one"> View </a>
                </label>
                |
                <label>
                    <a href="{{ route('user.reports.download_todays_report', $today_report->id) }}" target="_blank" class="btn-none-download"> Download </a>
                </label>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
