
<table id="view_archived_report" class="table table-striped table-bordered table-hover">
    <thead>
    <tr>
        <th>SL</th>
        <th>Campaign Title</th>
        <th class="hidden-600">Submit time</th>
        <th>Submitted</th>
        <th>Total sent</th>
        <th>Charge</th>
        <th>Action</th>
    </tr>
    </thead>

    <tbody>
    @php
    ($serial=1)
    @endphp
    @foreach($api_reports as $api_report)
    <tr>
        <td>{{ $serial++ }}</td>
        <td>{{ $api_report->sdci_campaign_id }}</td>
        <td>
            @if(!empty($api_report->sdci_targeted_time))
            {{ $api_report->sdci_targeted_time->format('H:i a, d-M-Y') }}
            @endif
        </td>
        <td class="text-center">{{ $api_report->sdci_total_submitted }}</td>
        <td class="text-center">{{ $api_report->sdci_total_submitted }}</td>
        <td class="text-right">BDT {{ number_format($api_report->sdci_total_cost, 2) }}</td>
        <td>
            <label>
                <a href="#my-modal" onclick="show_archived_details('{{$api_report->id}}')"
                    role="button" data-toggle="modal"
                    class="btn-none-edit CampaignId_one"> View </a>
            </label>
            |
            <label>

                <label>
                    <a href="{{ route('user.reports.download_archived_report', $api_report->id) }}" target="_blank" class="btn-none-download"> Download </a>
                </label>
            </label>
        </td>
    </tr>
    @endforeach
    </tbody>
</table>