@extends('admin.master')

@section('reseller_menu_class','open')
@section('reseller_list_menu_class', 'active')
@section('page_location')
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="{{ route('admin.index') }}">Dashboard</a>
        </li>
        <li>
            <a href="{{ route('admin.reseller.index') }}">Reseller List</a>
        </li>
        <li class="active">Price</li>
    </ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
    <h1>
        Price
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            Update
        </small>
    </h1>
@endsection

@section('main_content')

    <div class="space-6"></div>



    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

            <div class="col-lg-12 col-md-12 widget-container-col ui-sortable" id="widget-container-col-13">
                <div class="widget-box transparent ui-sortable-handle" id="widget-box-13">
                    <div class="widget-header">
                        <h4 class="widget-title lighter"> {{ $user->userDetail->company_name }}</h4>

                        @include('admin.partials.all_error_messages')
                        @include('admin.partials.session_messages')

                        <div class="widget-toolbar no-border">
                            <ul class="nav nav-tabs" id="myTab2">
                                <li class="active">
                                    <a data-toggle="tab" href="#profile2" aria-expanded="true">Operator rate</a>
                                </li>
                                <li class="">
                                    <a data-toggle="tab" href="#home2" aria-expanded="false">Profile</a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="widget-body">
                        <div class="widget-main padding-12 no-padding-left no-padding-left">
                            <div class="tab-content padding-4">
                                <div id="profile2" class="tab-pane active">
                                    <div class="scroll-track" style="display: none;">
                                        <div class="scroll-bar"></div>
                                    </div>
                                    <div class="scroll-content">
                                        <div class="col-lg-10 col-md-10">

                                            <table class="table table-bordered table-responsive">
                                                <thead>
                                                <tr>
                                                    <th>Country</th>
                                                    <th>Operator</th>
                                                    <th>Prefix</th>
                                                    <th>Masking Price/Nonmasking Price</th>

                                                    <th> Selling Price Masking / Nonmasking</th>
                                                    <th> Active</th>
                                                </tr>
                                                </thead>

                                                <tbody>
                                                @foreach($smsRates as $smsRate)
                                                    <tr>
                                                        <td> {{ $smsRate->country->country_name }}</td>
                                                        <td> {{ $smsRate->operator->ope_operator_name }}</td>
                                                        <td> {{ $smsRate->operator->ope_number }}</td>
                                                        <td>BDT :{{ $smsRate->asr_masking }}
                                                            / {{ $smsRate->asr_nonmasking }}</td>
                                                        <td>
                                                            <form action="{{ route('admin.reseller.priceView.update', $smsRate->id) }}"
                                                                  method="post" id="form_{{$smsRate->id}}">
                                                                @csrf
                                                                <input type="text" name="masking_price" class="col-md-3"
                                                                       value="{{ $smsRate->asr_masking }}" required>
                                                                <span class="pull-left"
                                                                      style="margin-top: 5px;">/</span>
                                                                <input type="text" name="non_masking_price"
                                                                       class="col-md-3"
                                                                       value="{{ $smsRate->asr_nonmasking }}" required>
                                                            </form>
                                                        </td>
                                                        <td><input type="button"
                                                                   onclick="submitRateForm('form_{{$smsRate->id}}')"
                                                                   value="Save" class="btn btn-sm btn-primary"></td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>

                                        </div>
                                    </div>
                                </div>

                                <div id="home2" class="tab-pane">
                                    <div class="scrollable-horizontal ace-scroll" data-size="800"
                                         style="position: relative; padding-top: 12px;">
                                        <div style="width: 800px;">

                                            <div class="col-lg-10 col-md-10">
                                                <table class="table table-bordered">
                                                    <tr>
                                                        <th class="col-md-3">Company name</th>
                                                        <td> {{ $user->userDetail->company_name }}</td>
                                                    </tr>

                                                    <tr>
                                                        <th class="col-md-3">Name</th>
                                                        <td> {{ $user->name }}</td>
                                                    </tr>

                                                    <tr>
                                                        <th class="col-md-3">Email</th>
                                                        <td> {{ $user->email }}</td>
                                                    </tr>

                                                    <tr>
                                                        <th class="col-md-3">Cell Phone</th>
                                                        <td> {{ $user->cellphone }}</td>
                                                    </tr>

                                                    <tr>
                                                        <th class="col-md-3">Desgination</th>
                                                        <td> {{ $user->userDetail->designation }}</td>
                                                    </tr>

                                                    <tr>
                                                        <th class="col-md-3">Address</th>
                                                        <td> {{ $user->userDetail->address }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
        $('#reseller-list-table').DataTable();

        function submitRateForm(formName) {
            $("#" + formName).submit();
        }
    </script>

@endsection
