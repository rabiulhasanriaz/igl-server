<!DOCTYPE html>
<html>
<head>
    <title>API SMS Report</title>
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
        .text-wrap {
            word-wrap: break-word;
            max-width: 150px;
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
    <div class="title">API SMS Report</div>
    <div class="sub-title">
        Date Range: {{ date('d M Y', strtotime($start_date)) }} to {{ date('d M Y', strtotime($end_date)) }}
        <br>
        Generated: {{ $generated_at->format('d M Y h:i:s A') }}
    </div>

    <!-- ===== SUMMARY ===== -->
    <div class="summary-box">
        <table>
            <tr>
                <td class="label">Total SMS:</td>
                <td class="value">{{ number_format($total_count) }}</td>
                <td class="label">Delivered:</td>
                <td class="value">{{ number_format($delivered_count) }}</td>
                <td class="label">Pending:</td>
                <td class="value">{{ number_format($total_count - $delivered_count) }}</td>
                <td class="label">Total Cost:</td>
                <td class="value">BDT {{ number_format($total_cost, 2) }}</td>
            </tr>
        </table>
    </div>

    <!-- ===== CAMPAIGN SUMMARY ===== -->
    @if($campaign_summary->count() > 0)
    <div class="section-title">📊 Campaign Summary</div>
    <table>
        <thead>
            <tr>
                <th style="text-align: center; width: 5%;">SL</th>
                <th style="text-align: center; width: 18%;">Campaign ID</th>
                <th style="text-align: center; width: 25%;">Campaign Title</th>
                <th style="text-align: center; width: 18%;">Submit Time</th>
                <th style="text-align: center; width: 12%;">Submitted</th>
                <th style="text-align: center; width: 22%;">Total Cost</th>
            </tr>
        </thead>
        <tbody>
            @php($summary_total_cost = 0)
            @php($summary_total_submitted = 0)
            @foreach($campaign_summary as $index => $campaign)
            <tr>
                <td style="text-align: center; font-size: 12px;">{{ $index + 1 }}</td>
                <td style="text-align: center; font-size: 12px;">{{ $campaign->sci_campaign_id }}</td>
                <td style="text-align: center; font-size: 12px;">
                    {{ strlen($campaign->sci_campaign_title) > 30 ? substr($campaign->sci_campaign_title, 0, 30) . '...' : $campaign->sci_campaign_title }}
                </td>
                <td style="text-align: center; font-size: 12px;">
                    {{ $campaign->sci_targeted_time ? $campaign->sci_targeted_time->format('d M Y h:i A') : 'N/A' }}
                </td>
                <td style="text-align: center; font-size: 12px;">{{ number_format($campaign->sci_total_submitted) }}</td>
                <td style="text-align: right; font-size: 12px;">BDT {{ number_format($campaign->sci_total_cost, 2) }}</td>
            </tr>
            @php($summary_total_cost += $campaign->sci_total_cost)
            @php($summary_total_submitted += $campaign->sci_total_submitted)
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="text-align: right; font-weight: bold;">Total:</td>
                <td style="text-align: center; font-weight: bold;">{{ number_format($summary_total_submitted) }}</td>
                <td style="text-align: right; font-weight: bold;">BDT {{ number_format($summary_total_cost, 2) }}</td>
            </tr>
            <tr>
                <td colspan="6" style="text-align: left; font-weight: bold;">
                    In Word:- {{ \OtherHelpers::number_to_text($summary_total_cost) }}
                </td>
            </tr>
        </tfoot>
    </table>
    @endif

    <!-- ===== SMS DETAILS ===== -->
    <div class="section-title">📋 SMS Details</div>

    @if($reports->count() > 0)
    <table>
        <thead>
            <tr>
                <th style="text-align: center; width: 4%;">SL</th>
                <th style="text-align: center; width: 12%;">Sender ID</th>
                <th style="text-align: center; width: 12%;">Mobile</th>
                <th style="text-align: center; width: 28%;">Message</th>
                <th style="text-align: center; width: 12%;">Cost</th>
                <th style="text-align: center; width: 18%;">Submit Time</th>
                <th style="text-align: center; width: 14%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @php($detail_total_cost = 0)
            @foreach($reports as $index => $report)
            <tr>
                <td style="text-align: center; font-size: 11px;">{{ $index + 1 }}</td>
                <td style="text-align: center; font-size: 11px;">
                    {{ $report->sender->sir_sender_id ?? $report->sender_id ?? 'N/A' }}
                </td>
                <td style="text-align: center; font-size: 11px;">
                    {{ substr($report->sc_cell_no ?? 'N/A', 0, 4) . '****' . substr($report->sc_cell_no ?? '', -4) }}
                </td>
                <td style="text-align: left; font-size: 11px; word-wrap: break-word; max-width: 150px;">
                    {{ strlen($report->sc_message ?? 'N/A') > 35 ? substr($report->sc_message, 0, 35) . '...' : ($report->sc_message ?? 'N/A') }}
                </td>
                <td style="text-align: right; font-size: 11px;">BDT {{ number_format($report->sc_sms_cost ?? 0, 2) }}</td>
                <td style="text-align: center; font-size: 11px;">
                    {{ $report->created_at ? $report->created_at->format('d M Y h:i A') : 'N/A' }}
                </td>
                <td style="text-align: center; font-size: 11px;">
                    @if(!empty($report->sc_delivery_report))
                        <span class="status-delivered">✓ Delivered</span>
                    @else
                        <span class="status-pending">⏳ Pending</span>
                    @endif
                </td>
            </tr>
            @php($detail_total_cost += $report->sc_sms_cost ?? 0)
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="text-align: right; font-weight: bold;">Total:</td>
                <td style="text-align: right; font-weight: bold;">BDT {{ number_format($detail_total_cost, 2) }}</td>
                <td colspan="2" style="text-align: center; font-weight: bold;">{{ number_format($total_count) }} SMS</td>
            </tr>
            <tr>
                <td colspan="7" style="text-align: left; font-weight: bold;">
                    In Word:- {{ \OtherHelpers::number_to_text($detail_total_cost) }}
                </td>
            </tr>
        </tfoot>
    </table>
    @else
    <div class="no-data">No API reports found in this date range.</div>
    @endif

</body>
</html>