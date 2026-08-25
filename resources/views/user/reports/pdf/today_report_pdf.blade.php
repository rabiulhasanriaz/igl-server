<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Today SMS Report</title>
    <style>
        @font-face {
            font-family: 'Nirmala UI';
            font-style: normal;
            font-weight: normal;
            src: url('{{ public_path('fonts/Nirmala.ttf') }}') format('truetype');
        }
        @page { margin: 90px 35px 55px; }
        body { font-family: 'Nirmala UI', DejaVu Sans, sans-serif; font-size: 10px; color: #222; }
        #header { position: fixed; top: -72px; left: 0; right: 0; text-align: center; }
        #header img { max-height: 45px; }
        #footer { position: fixed; bottom: -38px; left: 0; right: 0; text-align: center; color: #777; }
        #footer .page:after { content: counter(page); }
        h1 { margin: 0 0 4px; text-align: center; font-size: 18px; }
        .subtitle { text-align: center; margin-bottom: 12px; color: #555; }
        .summary { width: 100%; margin-bottom: 14px; background: #f5f5f5; }
        .summary td { padding: 6px; border: 1px solid #ddd; font-weight: bold; }
        .section { margin: 12px 0 5px; padding: 5px 7px; background: #2c3e50; color: #fff; font-weight: bold; }
        table.report { width: 100%; border-collapse: collapse; }
        table.report th, table.report td { border: 1px solid #ccc; padding: 4px; }
        table.report th { background: #eee; text-align: center; }
        .center { text-align: center; }
        .right { text-align: right; }
        .message { font-size: 9px; word-wrap: break-word; }
        .empty { padding: 12px; text-align: center; color: #777; }
    </style>
</head>
<body>
    <div id="header">
        <img src="{{ OtherHelpers::website_logo() }}" alt="Logo">
    </div>

    <div id="footer">Generated {{ $generated_at->format('d M Y h:i:s A') }} | Page <span class="page"></span></div>

    <h1>Today SMS Report</h1>
    <div class="subtitle">{{ $report_date->format('d M Y') }}</div>

    <table class="summary">
        <tr>
            <td>Campaigns: {{ number_format($regular_campaigns->count() + $api_campaigns->count()) }}</td>
            <td>SMS Count: {{ number_format($total_sms_count) }}</td>
            <td>Total sent: {{ number_format($total_sent) }}</td>
            <td class="right">Cost: BDT {{ number_format($total_cost, 2) }}</td>
        </tr>
    </table>

    @php
        $sections = [
            'Regular Campaigns' => $regular_campaigns,
            'API Campaigns' => $api_campaigns,
        ];
    @endphp

    @foreach($sections as $sectionTitle => $campaigns)
        <div class="section">{{ $sectionTitle }}</div>

        @if($campaigns->isEmpty())
            <div class="empty">No campaigns found.</div>
        @else
            <table class="report">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Campaign</th>
                        <th>Sender</th>
                        <th>SMS Count</th>
                        <th>Total sent</th>
                        <th>Cost</th>
                        <th>Submit time</th>
                        <th>Message</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($campaigns as $index => $campaign)
                        <tr>
                            <td class="center">{{ $index + 1 }}</td>
                            <td>{{ $campaign->sci_campaign_title ?? $campaign->sci_campaign_id }}</td>
                            <td class="center">{{ optional($campaign->sender)->sir_sender_id ?? 'N/A' }}</td>
                            <td class="center">{{ number_format($campaign->report_sms_count) }}</td>
                            <td class="center">{{ number_format($campaign->report_recipient_count) }}</td>
                            <td class="right">{{ number_format($campaign->sci_total_cost, 2) }}</td>
                            <td class="center">{{ optional($campaign->sci_targeted_time)->format('d M Y h:i A') }}</td>
                            <td class="message">{{ mb_strimwidth($campaign->message_content, 0, 90, '...') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endforeach
</body>
</html>
