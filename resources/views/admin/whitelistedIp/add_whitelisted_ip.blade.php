@extends('admin.master')

@section('whitelisted_ip_menu_class','open')
@section('add_whitelisted_ip_menu_class', 'active')

@section('page_location')
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="{{ route('admin.index') }}">Dashboard</a>
        </li>
        <li>
            <a href="{{ route('admin.whitelistedIp.index') }}">Whitelisted IP</a>
        </li>
        <li class="active">Add Whitelisted IP</li>
    </ul>
@endsection

@section('page_header')
    <h1>
        Whitelisted IP
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            Add
        </small>
    </h1>
@endsection

@section('main_content')
    <div class="space-6"></div>

    @include('admin.partials.session_messages')

    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="widget-box">
                <div class="widget-header widget-header-blue widget-header-flat">
                    <h4 class="widget-title lighter">
                        <i class="ace-icon fa fa-shield"></i>
                        Add Whitelisted IP
                    </h4>
                </div>

                <div class="widget-body">
                    <div class="widget-main">
                        <form action="{{ route('admin.whitelistedIp.store') }}" method="POST">
                            @csrf
                            
                            <div class="form-group">
                                <label for="user_id">Select User <span class="text-danger">*</span></label>
                                <select name="user_id" id="user_id" class="form-control select2" required>
                                    <option value="">-- Select User --</option>
                                    @foreach($users as $user)
                                    <option value="{{ $user->id }}">
                                        {{ $user->cellphone }} - {{ $user->company_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="white_listed_ip">Whitelisted IP(s)</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="white_listed_ip" 
                                       name="white_listed_ip" 
                                       placeholder="e.g., 192.168.1.1 or 103.86.193.27,59.152.5.62">
                                <small class="text-muted">
                                    <i class="ace-icon fa fa-info-circle"></i>
                                    Single IP, multiple IPs (comma separated), CIDR (192.168.1.0/24), or wildcard (192.168.1.*)
                                </small>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ace-icon fa fa-save"></i> Save
                                </button>
                                <a href="{{ route('admin.whitelistedIp.index') }}" class="btn btn-default">
                                    <i class="ace-icon fa fa-times"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('custom_style')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container .select2-selection--single {
        height: 34px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 32px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 32px;
    }
    .col-md-offset-3 {
        margin-left: 25%;
    }
</style>
@endsection

@section('custom_script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Select a user",
            allowClear: true
        });
    });
</script>
@endsection
