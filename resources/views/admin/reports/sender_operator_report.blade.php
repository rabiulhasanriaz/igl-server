@extends('admin.master')

@section('sms_flexi_report_class','open')
@section('sender_operator_report_class','active')

@section('page_location')
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="{{ route('admin.index') }}">Dashboard</a>
        </li>
        <li>
            <a href="#">Reports</a>
        </li>
        <li class="active">Sender Operator Report</li>
    </ul><!-- /.breadcrumb -->
@endsection

@section('page_header')
    <h1>
        Sender Operator Report
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            Sender ID & Operator Count Report
        </small>
    </h1>
@endsection

@section('main_content')

    <div class="space-6"></div>

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-container">
            
            <!-- Filter Section -->
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="background: #f8f8f8; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
                <form method="GET" action="{{ route('admin.reports.sender-operator-report') }}" class="form-inline">
                    <div class="row">
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <div class="form-group" style="width: 100%;">
                                <label for="start_date">Start Date</label>
                                <input type="date" name="start_date" id="start_date" 
                                       class="form-control" style="width: 100%;" 
                                       value="{{ isset($start_date) ? $start_date : Carbon\Carbon::now()->subDays(30)->format('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <div class="form-group" style="width: 100%;">
                                <label for="end_date">End Date</label>
                                <input type="date" name="end_date" id="end_date" 
                                       class="form-control" style="width: 100%;" 
                                       value="{{ isset($end_date) ? $end_date : Carbon\Carbon::now()->format('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                            <div class="form-group" style="width: 100%;">
                                <label for="sender_id">Sender ID</label>
                                <select name="sender_id" id="sender_id" class="form-control" style="width: 100%;">
                                    <option value="">All Senders</option>
                                    @if(isset($senders))
                                        @foreach($senders as $sender)
                                            <option value="{{ $sender->id }}" {{ request('sender_id') == $sender->id ? 'selected' : '' }}>
                                                {{ $sender->sir_sender_id }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                            <div class="form-group" style="width: 100%;">
                                <label for="operator_id">Operator</label>
                                <select name="operator_id" id="operator_id" class="form-control" style="width: 100%;">
                                    <option value="">All Operators</option>
                                    @if(isset($operators))
                                        @foreach($operators as $operator)
                                            <option value="{{ $operator->id }}" {{ request('operator_id') == $operator->id ? 'selected' : '' }}>
                                                {{ $operator->ope_operator_name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12" style="padding-top: 22px;">
                            <div class="form-group" style="width: 100%;">
                                <button type="submit" class="btn btn-primary" style="width: 48%;">
                                    <i class="fa fa-search"></i> Filter
                                </button>
                                <a href="{{ route('admin.reports.sender-operator-report-export', request()->all()) }}" 
                                   class="btn btn-success" style="width: 48%;">
                                    <i class="fa fa-file-excel-o"></i> Export
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Summary Cards -->
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <div class="well well-sm text-center" style="background: #e8f5e9; border-color: #4caf50;">
                            <h4><i class="fa fa-envelope"></i> Total Messages</h4>
                            <h3>{{ isset($summary->total_messages) ? number_format($summary->total_messages) : 0 }}</h3>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <div class="well well-sm text-center" style="background: #e3f2fd; border-color: #2196f3;">
                            <h4><i class="fa fa-tag"></i> Total Senders</h4>
                            <h3>{{ isset($summary->total_senders) ? number_format($summary->total_senders) : 0 }}</h3>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <div class="well well-sm text-center" style="background: #fce4ec; border-color: #f44336;">
                            <h4><i class="fa fa-calendar"></i> Date Range</h4>
                            <h5 style="margin-top: 8px;">
                                {{ isset($start_date) ? Carbon\Carbon::parse($start_date)->format('d-M-Y') : Carbon\Carbon::now()->subDays(30)->format('d-M-Y') }} 
                                <i class="fa fa-arrow-right"></i> 
                                {{ isset($end_date) ? Carbon\Carbon::parse($end_date)->format('d-M-Y') : Carbon\Carbon::now()->format('d-M-Y') }}
                            </h5>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Report Table -->
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-top: 15px;">
                <ul class="list-group">
                    <li class="list-group-item active text-center">
                        <i class="fa fa-table"></i> Sender ID Wise Report
                    </li>
                </ul>
                <table id="sender-operator-table" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Sender ID</th>
                            <th>Operator</th>
                            <th>Total Messages</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $sl = 0;
                            $total_messages = 0;
                        @endphp
                        
                        @if(isset($senderReports) && count($senderReports) > 0)
                            @foreach($senderReports as $report)
                                @php
                                    $total_messages += $report->total_messages;
                                @endphp
                                <tr>
                                    <td>{{ ++$sl }}</td>
                                    <td><strong>{{ $report->sender_id }}</strong></td>
                                    <td>
                                        @if($report->operator_name != 'N/A')
                                            <span class="label label-success">{{ $report->operator_name }}</span>
                                        @else
                                            <span class="label label-default">N/A</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-primary">{{ number_format($report->total_messages) }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4" class="text-center text-danger">
                                    <i class="fa fa-exclamation-triangle"></i> No data found
                                </td>
                            </tr>
                        @endif
                    </tbody>
                    <tfoot>
                        <tr style="background: #f5f5f5; font-weight: bold;">
                            <th colspan="3" class="text-right">Grand Total</th>
                            <th class="text-center">{{ number_format($total_messages) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Summary by Operator -->
            @if(isset($senderReports) && count($senderReports) > 0)
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-top: 20px;">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title"><i class="fa fa-pie-chart"></i> Summary by Operator</h4>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            @php
                                $operatorSummary = array();
                                foreach($senderReports as $report) {
                                    if (!isset($operatorSummary[$report->operator_name])) {
                                        $operatorSummary[$report->operator_name] = array(
                                            'total_messages' => 0,
                                            'count' => 0
                                        );
                                    }
                                    $operatorSummary[$report->operator_name]['total_messages'] += $report->total_messages;
                                    $operatorSummary[$report->operator_name]['count']++;
                                }
                            @endphp
                            
                            @foreach($operatorSummary as $operatorName => $data)
                                <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12" style="margin-bottom: 10px;">
                                    <div class="well well-sm" style="margin-bottom: 0; background: #f5f5f5;">
                                        <strong>{{ $operatorName }}</strong>
                                        <div class="progress" style="margin: 5px 0 0 0; height: 20px;">
                                            <div class="progress-bar progress-bar-success" 
                                                 style="width: {{ $total_messages > 0 ? ($data['total_messages'] / $total_messages) * 100 : 0 }}%;">
                                                {{ number_format($data['total_messages']) }}
                                            </div>
                                        </div>
                                        <small>{{ $data['count'] }} Senders</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div><!-- /.col -->
    </div><!-- /.row -->

@endsection

@section('custom_style')
    <link href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" />
    <style>
        .well.well-sm {
            padding: 15px;
            margin-bottom: 0;
        }
        .well.well-sm h4 {
            margin: 0 0 5px 0;
            font-size: 14px;
            color: #555;
        }
        .well.well-sm h3 {
            margin: 5px 0 0 0;
            font-weight: bold;
        }
        .well.well-sm h5 {
            margin: 5px 0 0 0;
        }
        .badge-primary {
            background-color: #2196f3;
            color: #fff;
            padding: 5px 10px;
            border-radius: 10px;
        }
        .label-success {
            background-color: #4caf50;
            padding: 5px 10px;
            border-radius: 3px;
            color: #fff;
        }
        .label-default {
            background-color: #777;
            padding: 5px 10px;
            border-radius: 3px;
            color: #fff;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .text-danger {
            color: #a94442;
        }
        .panel-heading {
            background: #f5f5f5;
            border-bottom: 1px solid #ddd;
        }
        .panel-heading h4 {
            margin: 0;
        }
        .progress {
            background-color: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
        }
        .progress-bar {
            background-color: #4caf50;
            line-height: 20px;
            color: #fff;
            text-align: center;
            font-size: 11px;
        }
        .dataTables_wrapper .dataTables_filter {
            float: right;
        }
        .dataTables_wrapper .dataTables_paginate {
            float: right;
        }
        .table {
            font-size: 13px;
        }
        .table th, .table td {
            vertical-align: middle !important;
        }
    </style>
@endsection

@section('custom_script')
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            var table = $('#sender-operator-table').DataTable({
                responsive: false,
                paging: true,
                pageLength: 50,
                ordering: true,
                order: [[3, 'desc']],
                columnDefs: [
                    { orderable: false, targets: [0] },
                    { className: 'text-center', targets: [3] }
                ],
                language: {
                    search: "Search:",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "Showing 0 to 0 of 0 entries",
                    infoFiltered: "(filtered from _MAX_ total entries)",
                    paginate: {
                        previous: "Previous",
                        next: "Next"
                    }
                }
            });
        });

        // Auto submit filter on change
        $(document).ready(function() {
            $('#sender_id, #operator_id').change(function() {
                $(this).closest('form').submit();
            });
        });
    </script>
@endsection