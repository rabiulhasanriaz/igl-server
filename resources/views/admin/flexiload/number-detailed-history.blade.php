@extends('admin.master')

@section('flexiload_menu_class','open')
@section('flexiload_history_menu_class', 'active')
@section('page_location')
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="{{ route('admin.index') }}">Dashboard</a>
        </li>
        <li>
            <a href="{{ route('admin.flexiload.number-wise-history') }}">Flexiload</a>
        </li>
        <li class="active">Number Detailed History</li>
    </ul>
@endsection

@section('page_header')
    <h1>
        Flexiload
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            Detailed History - {{ $phoneNumber }}
        </small>
    </h1>
@endsection

@section('main_content')
    <div class="space-6"></div>

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            @include('admin.partials.session_messages')
            
            <!-- Number Summary -->
            <div class="widget-box transparent">
                <div class="widget-header widget-header-small">
                    <h4 class="widget-title blue smaller">
                        <i class="ace-icon fa fa-phone"></i>
                        Phone Number Summary
                    </h4>
                </div>
                <div class="widget-body">
                    <div class="widget-main padding-8">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="profile-user-info">
                                    <div class="profile-info-row">
                                        <div class="profile-info-name"> Phone Number </div>
                                        <div class="profile-info-value">
                                            <span class="text-primary"><strong>{{ $phoneNumber }}</strong></span>
                                        </div>
                                    </div>
                                    <div class="profile-info-row">
                                        <div class="profile-info-name"> Owner Name </div>
                                        <div class="profile-info-value">
                                            <span>{{ $transactions->first()->owner_name ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="profile-user-info">
                                    <div class="profile-info-row">
                                        <div class="profile-info-name"> Total Transactions </div>
                                        <div class="profile-info-value">
                                            <span class="badge badge-primary">{{ $summary->total_transactions ?? 0 }}</span>
                                        </div>
                                    </div>
                                    <div class="profile-info-row">
                                        <div class="profile-info-name"> Success </div>
                                        <div class="profile-info-value">
                                            <?php
                                                $successCount = $transactions->where('status', 1)->count();
                                            ?>
                                            <span class="badge badge-success">{{ $successCount }}</span>
                                        </div>
                                    </div>
                                    <div class="profile-info-row">
                                        <div class="profile-info-name"> Failed </div>
                                        <div class="profile-info-value">
                                            <?php
                                                $failedCount = $transactions->where('status', '!=', 1)->count();
                                            ?>
                                            <span class="badge badge-danger">{{ $failedCount }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="profile-user-info">
                                    <div class="profile-info-row">
                                        <div class="profile-info-name"> Total Amount </div>
                                        <div class="profile-info-value">
                                            <span class="text-success">৳{{ number_format($summary->total_amount ?? 0, 2) }}</span>
                                        </div>
                                    </div>
                                    <div class="profile-info-row">
                                        <div class="profile-info-name"> Success Amount </div>
                                        <div class="profile-info-value">
                                            <?php
                                                $successAmount = $transactions->where('status', 1)->sum('campaign_price');
                                            ?>
                                            <span class="text-success">৳{{ number_format($successAmount, 2) }}</span>
                                        </div>
                                    </div>
                                    <div class="profile-info-row">
                                        <div class="profile-info-name"> Failed Amount </div>
                                        <div class="profile-info-value">
                                            <?php
                                                $failedAmount = $transactions->where('status', '!=', 1)->sum('campaign_price');
                                            ?>
                                            <span class="text-danger">৳{{ number_format($failedAmount, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="profile-user-info">
                                    <div class="profile-info-row">
                                        <div class="profile-info-name"> Last Transaction </div>
                                        <div class="profile-info-value">
                                            @if($summary->last_transaction_date)
                                                <small>{{ \Carbon\Carbon::parse($summary->last_transaction_date)->format('d M Y h:i A') }}</small>
                                            @else
                                                N/A
                                            @endif
                                        </div>
                                    </div>
                                    <div class="profile-info-row">
                                        <div class="profile-info-name"> Success Rate </div>
                                        <div class="profile-info-value">
                                            <?php
                                                $total = $transactions->count();
                                                $successRate = $total > 0 ? ($successCount / $total) * 100 : 0;
                                            ?>
                                            <span class="badge {{ $successRate >= 80 ? 'badge-success' : ($successRate >= 50 ? 'badge-warning' : 'badge-danger') }}">
                                                {{ number_format($successRate, 1) }}%
                                            </span>
                                        </div>
                                    </div>
                                    <div class="profile-info-row">
                                        <div class="profile-info-name"> SMS Messages </div>
                                        <div class="profile-info-value">
                                            <?php
                                                $messageCount = count($transactionMessages);
                                            ?>
                                            <span class="badge badge-info">{{ $messageCount }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-4"></div>

            <!-- Back Button -->
            <div class="clearfix">
                <a href="{{ route('admin.flexiload.number-wise-history') }}" class="btn btn-sm btn-default pull-right">
                    <i class="ace-icon fa fa-arrow-left"></i>
                    Back to Number List
                </a>
            </div>

            <div class="space-4"></div>

            <!-- Transaction History Table -->
            <table id="number-detailed-history-table" class="table table-striped table-bordered table-hover table-sm">
                <thead>
                    <tr>
                        <th>Sl</th>
                        <th>Transaction ID</th>
                        <th>User</th>
                        <th>Package</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date & Time</th>
                        <th>SMS Messages</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $serial = 1; ?>
                    @foreach($transactions as $transaction)
                    <?php
                        $relatedMessages = isset($transactionMessages[$transaction->transaction_id]) ? $transactionMessages[$transaction->transaction_id] : [];
                    ?>
                    <tr>
                        <td>{{ $serial++ }}</td>
                        <td>
                            @if($transaction->transaction_id)
                                <small class="text-muted">{{ $transaction->transaction_id }}</small>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $transaction->trx_user->company_name ?? ($transaction->trx_user->name ?? 'N/A') }}</strong>
                            <br>
                            <small class="text-muted">
                                ID: {{ $transaction->user_id }} | 
                                Phone: {{ $transaction->trx_user->cellphone ?? 'N/A' }}
                            </small>
                        </td>
                        <td>
                            @if($transaction->package_info)
                                {{ $transaction->package_info->package_details }}
                                <br>
                                <small class="text-muted">৳{{ $transaction->package_info->package_price }}</small>
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="text-success">
                            <strong>৳{{ number_format($transaction->campaign_price, 2) }}</strong>
                        </td>
                        <td>
                            @if($transaction->status == 1)
                                <span class="label label-success">
                                    <i class="ace-icon fa fa-check"></i>
                                    Success
                                </span>
                            @else
                                <span class="label label-danger">
                                    <i class="ace-icon fa fa-times"></i>
                                    Failed
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($transaction->created_at)
                                {{ $transaction->created_at->format('d M Y h:i A') }}
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            @if(count($relatedMessages) > 0)
                                <div class="sms-messages">
                                    @foreach($relatedMessages as $message)
                                    <div class="sms-message" style="margin-bottom: 8px; padding: 5px; background: #f8f9fa; border-radius: 4px; border-left: 3px solid #007bff;">
                                        <div class="sms-header" style="font-size: 11px; color: #666; margin-bottom: 3px;">
                                            <strong>From:</strong> {{ $message->sender ?? 'N/A' }}
                                            <span class="pull-right">
                                                {{ $message->created_at->format('h:i A') }}
                                            </span>
                                        </div>
                                        <div class="sms-body" style="font-size: 12px;">
                                            {{ $message->message }}
                                        </div>
                                        @if($message->operator_company)
                                        <div class="sms-footer" style="font-size: 10px; color: #888; margin-top: 2px;">
                                            <strong>Operator:</strong> {{ $message->operator_company }}
                                        </div>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-muted">No messages</span>
                            @endif
                        </td>
                        <td>
                            <small>{{ $transaction->remarks ?? 'N/A' }}</small>
                        </td>
                    </tr>
                    @endforeach
                    @if($transactions->isEmpty())
                    <tr>
                        <td colspan="9" class="text-center text-danger">No transaction history found for this number</td>
                    </tr>
                    @endif
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pull-right">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
@endsection

@section('custom_script')
    <script src="{{ asset('assets') }}/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('assets') }}/js/jquery.dataTables.bootstrap.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#number-detailed-history-table').DataTable({
                "order": [[ 6, "desc" ]],
                "paging": false,
                "searching": true,
                "language": {
                    "search": "Search in transactions:"
                }
            });
        });
    </script>
@endsection
