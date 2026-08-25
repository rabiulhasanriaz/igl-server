<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Archived SMS Report</title>
    <style type="text/css">
        table {
            border-collapse: collapse;
            width: 100%;
        }
        table, th, td {
            border: 1px solid lightgray;
        }
        th, td {
            padding: 3px;
        }

        @page { 
            margin: 150px 50px; 
        } 

        #header { 
            position: fixed; 
            left: 0px; 
            top: -120px; 
            right: 0px; 
            height: 150px; 
            text-align: center; 
        }
        #footer { 
            position: fixed; 
            left: 0px; 
            bottom: -100px; 
            right: 0px; 
            height: 70px; 
            text-align: center;
        }
        #footer .page:after { 
            content: counter(page, upper-roman); 
        }
        .logo img {
            max-height: 60px;
        }
        .operator-icons img {
            width: 25px;
            height: 25px;
            margin: 0 3px;
        }
        .title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin: 10px 0;
        }
        .sub-title {
            text-align: center;
            font-size: 14px;
            color: #555;
            margin-bottom: 15px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-12 { font-size: 12px; }
        .font-13 { font-size: 13px; }
        .font-bold { font-weight: bold; }
        .summary-box {
            background: #f9f9f9;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .summary-box table {
            border: none;
            width: 100%;
        }
        .summary-box td {
            border: none;
            padding: 4px 10px;
            font-size: 13px;
        }
        .summary-box .label {
            font-weight: bold;
            color: #555;
        }
        .summary-box .value {
            text-align: right;
            font-weight: bold;
            color: #2c3e50;
        }
        .status-delivered {
            color: #27ae60;
            font-weight: bold;
        }
        .status-pending {
            color: #f39c12;
            font-weight: bold;
        }
        .status-failed {
            color: #e74c3c;
            font-weight: bold;
        }
        .no-data {
            text-align: center;
            padding: 20px;
            color: #999;
            font-style: italic;
        }
        .section-title {
            background: #2c3e50;
            color: white;
            padding: 5px 10px;
            margin: 15px 0 10px 0;
            border-radius: 3px;
            font-size: 14px;
            font-weight: bold;
        }
        .api-section {
            margin-top: 15px;
            border-top: 2px dashed #ddd;
            padding-top: 10px;
        }
        .message-content {
            max-width: 200px;
            word-wrap: break-word;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <!-- ===== HEADER ===== -->
    <div id="header">
        <div class="logo">
            <img src="{{ OtherHelpers::website_logo() }}" alt="Logo">
        </div>
        <div id="footer">
            <p style="font-size: 16px; margin: 5px 0;">
                We Are Authorized Aggregator of: 
                <span class="operator-icons">
                    <img src="{{ asset('assets/images/operator_icon') }}/airtel.png" style="width: 25px; height: 25px; margin-right: 5px;">
                    <img src="{{ asset('assets/images/operator_icon') }}/banglalink.png" style="width: 25px; height: 25px; margin-right: 5px;">
                    <img src="{{ asset('assets/images/operator_icon') }}/gp.png" style="width: 25px; height: 25px; margin-right: 5px;">
                    <img src="{{ asset('assets/images/operator_icon') }}/robi.png" style="width: 25px; height: 25px; margin-right: 5px;">
                    <img src="{{ asset('assets/images/operator_icon') }}/teletalk.png" style="width: 25px; height: 25px;">
                </span>
            </p>
        </div>
    </div>

    <!-- ===== TITLE ===== -->
    <div class="title">Archived SMS Report</div>
    <div class="sub-title">
        Date Range: {{ date('d M Y', strtotime($start_date)) }} to {{ date('d M Y', strtotime($end_date)) }}
        <br>
        Generated: {{ $generated_at->format('d M Y h:i:s A') }}
    </div>

    <!-- ===== SUMMARY ===== -->
    <div class="summary-box">
        <table>
            <tr>
                <td class="label">Total Campaigns:</td>
                <td class="value">{{ $regular_campaigns->count() + $api_campaigns->count() }}</td>
                <td class="label">Total Submitted:</td>
                <td class="value">{{ number_format($total_submitted) }}</td>
                <td class="label">Total Cost:</td>
                <td class="value">BDT {{ number_format($total_cost, 2) }}</td>
            </tr>
        </table>
    </div>

    <!-- ===== REGULAR CAMPAIGNS ===== -->
    <div class="section-title">Regular SMS Campaigns</div>

    @if($regular_campaigns->count() > 0)
    <table>
        <thead>
            <tr>
                <th style="text-align: center; width: 4%;">SL</th>
                <th style="text-align: center; width: 12%;">Campaign ID</th>
                <th style="text-align: center; width: 14%;">Campaign Title</th>
                <th style="text-align: center; width: 10%;">Sender ID</th>
                <th style="text-align: center; width: 8%;">Submitted</th>
                <th style="text-align: center; width: 12%;">Total Cost</th>
                <th style="text-align: center; width: 14%;">Submit Time</th>
                <th style="text-align: center; width: 26%;">Message Content</th>
            </tr>
        </thead>
        <tbody>
            @php
                $regular_total_cost = 0;
                $regular_total_submitted = 0;
            @endphp
            @foreach($regular_campaigns as $index => $campaign)
            <tr>
                <td style="text-align: center; font-size: 11px;">{{ $index + 1 }}</td>
                <td style="text-align: center; font-size: 11px;">{{ $campaign->sci_campaign_id }}</td>
                <td style="text-align: center; font-size: 11px;">
                    {{ strlen($campaign->sci_campaign_title) > 25 ? substr($campaign->sci_campaign_title, 0, 25) . '...' : $campaign->sci_campaign_title }}
                </td>
                <td style="text-align: center; font-size: 11px;">{{ $campaign->sender->sir_sender_id ?? 'N/A' }}</td>
                <td style="text-align: center; font-size: 11px;">{{ number_format($campaign->sci_total_submitted) }}</td>
                <td style="text-align: right; font-size: 11px;">BDT {{ number_format($campaign->sci_total_cost, 2) }}</td>
                <td style="text-align: center; font-size: 11px;">
                    {{ $campaign->sci_targeted_time ? $campaign->sci_targeted_time->format('d M Y h:i A') : 'N/A' }}
                </td>
                <td style="text-align: left; font-size: 10px; word-wrap: break-word; max-width: 180px;">
                    {{ isset($campaign->message_content) ? (strlen($campaign->message_content) > 60 ? substr($campaign->message_content, 0, 60) . '...' : $campaign->message_content) : 'No message' }}
                </td>
            </tr>
            @php
                $regular_total_cost += $campaign->sci_total_cost;
                $regular_total_submitted += $campaign->sci_total_submitted;
            @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="text-align: right; font-weight: bold;">Total:</td>
                <td style="text-align: center; font-weight: bold;">{{ number_format($regular_total_submitted) }}</td>
                <td style="text-align: right; font-weight: bold;">BDT {{ number_format($regular_total_cost, 2) }}</td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td colspan="8" style="text-align: left; font-weight: bold;">
                    In Word: {{ \OtherHelpers::number_to_text($regular_total_cost) }}
                </td>
            </tr>
        </tfoot>
    </table>
    @else
    <div class="no-data">No regular campaigns found in this date range.</div>
    @endif

    <!-- ===== API CAMPAIGNS ===== -->
    @if($api_campaigns->count() > 0)
    <div class="api-section">
        <div class="section-title">API Campaigns</div>
        <table>
            <thead>
                <tr>
                    <th style="text-align: center; width: 4%;">SL</th>
                    <th style="text-align: center; width: 12%;">Campaign ID</th>
                    <th style="text-align: center; width: 14%;">Campaign Title</th>
                    <th style="text-align: center; width: 10%;">Sender ID</th>
                    <th style="text-align: center; width: 8%;">Submitted</th>
                    <th style="text-align: center; width: 12%;">Total Cost</th>
                    <th style="text-align: center; width: 14%;">Submit Time</th>
                    <th style="text-align: center; width: 26%;">Message Content</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $api_total_cost = 0;
                    $api_total_submitted = 0;
                @endphp
                @foreach($api_campaigns as $index => $campaign)
                <tr>
                    <td style="text-align: center; font-size: 11px;">{{ $index + 1 }}</td>
                    <td style="text-align: center; font-size: 11px;">{{ $campaign->sci_campaign_id }}</td>
                    <td style="text-align: center; font-size: 11px;">
                        {{ strlen($campaign->sci_campaign_title) > 25 ? substr($campaign->sci_campaign_title, 0, 25) . '...' : $campaign->sci_campaign_title }}
                    </td>
                    <td style="text-align: center; font-size: 11px;">{{ $campaign->sender->sir_sender_id ?? 'API' }}</td>
                    <td style="text-align: center; font-size: 11px;">{{ number_format($campaign->sci_total_submitted) }}</td>
                    <td style="text-align: right; font-size: 11px;">BDT {{ number_format($campaign->sci_total_cost, 2) }}</td>
                    <td style="text-align: center; font-size: 11px;">
                        {{ $campaign->sci_targeted_time ? $campaign->sci_targeted_time->format('d M Y h:i A') : 'N/A' }}
                    </td>
                    <td style="text-align: left; font-size: 10px; word-wrap: break-word; max-width: 180px;">
                        {{ isset($campaign->message_content) ? (strlen($campaign->message_content) > 60 ? substr($campaign->message_content, 0, 60) . '...' : $campaign->message_content) : 'No message' }}
                    </td>
                </tr>
                @php
                    $api_total_cost += $campaign->sci_total_cost;
                    $api_total_submitted += $campaign->sci_total_submitted;
                @endphp
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" style="text-align: right; font-weight: bold;">API Total:</td>
                    <td style="text-align: center; font-weight: bold;">{{ number_format($api_total_submitted) }}</td>
                    <td style="text-align: right; font-weight: bold;">BDT {{ number_format($api_total_cost, 2) }}</td>
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <td colspan="8" style="text-align: left; font-weight: bold;">
                        In Word: {{ \OtherHelpers::number_to_text($api_total_cost) }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif

</body>
</html>