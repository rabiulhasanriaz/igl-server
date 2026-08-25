@extends('reseller.master')

@section('user_list_menu_class','active')
@section('user_menu_class','open')
@section('page_location')
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="{{ route('reseller.index') }}">Dashboard</a>
        </li>
        <li>
            <a href="{{ route('reseller.user.index') }}">User</a>
        </li>
        <li class="active">Transaction History</li>
    </ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
    <h1>
        Transaction History
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            {{ $user->company_name }}
        </small>
    </h1>
@endsection

@section('main_content')

    <div class="space-6"></div>


    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

            <div>
                <div id="user-profile-1" class="user-profile row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <div class="profile-user-info profile-user-info-striped">
                            <div class="col-md-12">
                                <h2 class="text-center text-primary"><span><img
                                                src="{{ OtherHelpers::user_logo($user->userDetail['logo'])  }}"
                                                style="height: 40px;"></span> <span>{{ $user->name }}</span></h2>
                                <h3 class="text-center text-primary"> Transaction History </h3>
                            </div>

                            <div class="col-md-12">
                                <table id="transaction_history"
                                       class="table table-responsive table-bordered table-hover">
                                    <thead>
                                    <tr class="bg-info">
                                        <th>SL</th>
                                        <th>Payment Referance</th>
                                        <th>Submit date</th>
                                        <th>Credit</th>
                                        <th>Debit</th>
                                        <th>Balance</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    @php($available_balance=0)
                                    @php($total_credit=0)
                                    @php($total_debit=0)
                                    @php($serial=1)
                                    @foreach($SmsBalances as $smsBalance)
                                        @php($available_balance= $available_balance+$smsBalance->asb_credit-$smsBalance->asb_debit)
                                        @php($total_credit=$total_credit+$smsBalance->asb_credit)
                                        @php($total_debit=$total_debit+$smsBalance->asb_debit)
                                        <tr>
                                            <td>{{ $serial++ }}</td>
                                            <td>{{ $smsBalance->asb_pay_ref }}</td>
                                            <td>{{ $smsBalance->created_at->format('j M, Y') }}</td>
                                            <td class="text-right">
                                                {{ $smsBalance->asb_pay_mode == 6 ? $smsBalance->asb_credit . ' (Refund for Campaign Rejection) ' : $smsBalance->asb_credit }}
                                                
                                            </td>
                                            <td class="text-right">{{ $smsBalance->asb_debit }}</td>
                                            <td class="text-right">{{ $available_balance }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>


                                    <tfoot>
                                    <tr>
                                        <th colspan="3"></th>
                                        <th class="text-right">={{ $total_credit }}</th>
                                        <th class="text-right">= {{ $total_debit }}</th>
                                        <th class="text-right">= {{ $available_balance }}</th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="space-20"></div>

                    </div>
                </div>
            </div>


        </div><!-- /.col -->
    </div><!-- /.row -->
@endsection


@section('custom_script')
    <script src="{{ asset('assets') }}/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('assets') }}/js/jquery.dataTables.bootstrap.min.js"></script>
    <script type="text/javascript">
        $('#transaction_history').DataTable();
    </script>

@endsection
