@extends('user.master')

@section('support_menu_class', 'open')
@section('support_ticket', 'active')

@section('page_location')
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="">Dashboard</a>
        </li>
        <li>
            <a href="{{ route('user.support.tickets') }}">Support Center</a>
        </li>
        @yield('support_breadcrumb')
    </ul>
@endsection

@section('page_header')
    <h1>
        @yield('support_title')
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            @yield('support_subtitle')
        </small>
    </h1>
@endsection

@section('main_content')
    <div class="row">
        <div class="col-xs-12">
            @if(session('success'))
                <div class="alert alert-success">
                    <button type="button" class="close" data-dismiss="alert">
                        <i class="ace-icon fa fa-times"></i>
                    </button>
                    <strong>
                        <i class="ace-icon fa fa-check"></i>
                        Success!
                    </strong>
                    {{ session('success') }}
                    <br>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    <button type="button" class="close" data-dismiss="alert">
                        <i class="ace-icon fa fa-times"></i>
                    </button>
                    <strong>
                        <i class="ace-icon fa fa-times"></i>
                        Error!
                    </strong>
                    {{ session('error') }}
                    <br>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info">
                    <button type="button" class="close" data-dismiss="alert">
                        <i class="ace-icon fa fa-times"></i>
                    </button>
                    <strong>
                        <i class="ace-icon fa fa-info-circle"></i>
                        Info!
                    </strong>
                    {{ session('info') }}
                    <br>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <button type="button" class="close" data-dismiss="alert">
                        <i class="ace-icon fa fa-times"></i>
                    </button>
                    <strong>
                        <i class="ace-icon fa fa-times"></i>
                        Please fix the following errors:
                    </strong>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('support_content')
        </div>
    </div>
@endsection
