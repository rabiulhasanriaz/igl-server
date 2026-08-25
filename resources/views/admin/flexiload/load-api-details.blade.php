@extends('admin.master')

@section('flexiload_menu_class','open')
@section('flexiload_api_view_class','open')
@section('flexiload_load_api_all_class', 'active')
@section('page_location')

<ul class="breadcrumb">
    <li>
        <i class="ace-icon fa fa-home home-icon"></i>
        <a href="{{ route('admin.index') }}">Dashboard</a>
    </li>
    <li>
        <i class="ace-icon fa fa-barcode barcode-icon"></i>
        <a href="{{ route('admin.flexiload.load-api') }}">API List</a>
    </li>
    <li class="active">Port Details</li>
</ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
<h1>
    API List
    <small>
        <i class="ace-icon fa fa-angle-double-right"></i>
        Port Details
    </small>
</h1>
@endsection

@section('main_content')

<div class="space-6"></div>


<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

        @include('admin.partials.session_messages')
{{-- ===================== --}}
<section style="background-color: #eee;">
        <div class="col-lg-8">
          <div class="card mb-4">
            <div class="card-body">
              <div class="row">
                <div class="col-sm-3">
                  <p class="mb-0">Operator Name:</p>
                </div>
                <div class="col-sm-9">
                  <p class="text-muted mb-0">Robi</p>
                </div>
              </div>
              <hr>
              <div class="row">
                <div class="col-sm-3">
                  <p class="mb-0">Operator Sub-Name:</p>
                </div>
                <div class="col-sm-9">
                  <p class="text-muted mb-0">example</p>
                </div>
              </div>
              <hr>
              <div class="row">
                <div class="col-sm-3">
                  <p class="mb-0">Port Operator Balance:</p>
                </div>
                <div class="col-sm-9">
                  <p class="text-muted mb-0" style="color: red; font-weight: bold;">1025.32 Tk</p>
                </div>
              </div>
              <hr>
              <div class="row">
                <div class="col-sm-3">
                  <p class="mb-0">Operator IP:</p>
                </div>
                <div class="col-sm-9">
                  <p class="text-muted mb-0">(097) 234-5678</p>
                </div>
              </div>
              <hr>
              <div class="row">
                <div class="col-sm-3">
                  <p class="mb-0">Operator Port:</p>
                </div>
                <div class="col-sm-9">
                  <p class="text-muted mb-0">(098) 765-4321</p>
                </div>
              </div>
              <hr>
              <div class="row">
                <div class="col-sm-3">
                  <p class="mb-0">Operator Password:</p>
                </div>
                <div class="col-sm-9">
                  <p class="text-muted mb-0">(098) 765-4321</p>
                </div>
              </div>
              <hr>
              <div class="row">
                <div class="col-sm-3">
                  <p class="mb-0">Operator User:</p>
                </div>
                <div class="col-sm-9">
                  <p class="text-muted mb-0">(098) 765-4321</p>
                </div>
              </div>
              <hr>
              <div class="row">
                <div class="col-sm-3">
                  <p class="mb-0">Port Number:</p>
                </div>
                <div class="col-sm-9">
                  <p class="text-muted mb-0">(098) 765-4321</p>
                </div>
              </div>
              <hr>
              
              <div class="row">
                <div class="col-sm-3">
                  <p class="mb-0">Flexipin:</p>
                </div>
                <div class="col-sm-9">
                  <p class="text-muted mb-0">4321</p>
                </div>
              </div>
              <hr>
              <div class="row">
                <div class="col-sm-3">
                  <p class="mb-0">Operator Port:</p>
                </div>
                <div class="col-sm-9">
                  <p class="text-muted mb-0">(098) 765-4321</p>
                </div>
              </div>
              <hr>
              <div class="row">
                <div class="col-sm-3">
                  <p class="mb-0">Operator Port:</p>
                </div>
                <div class="col-sm-9">
                  <p class="text-muted mb-0">(098) 765-4321</p>
                </div>
              </div>
              <hr>      

              <div class="row">
                <div class="col-sm-3">
                  <p class="mb-0">Operator USSD:</p>
                </div>
                <div class="col-sm-9">
                  <p class="text-muted mb-0">Bay Area, San Francisco, CA</p>
                </div>
              </div>
            </div>
        </div>
        
<br>
<br>
            <div class="col-md-9">
                <button class="btn btn-info" href="#" type="button">
                    Edit Port
                </button>
                &nbsp; &nbsp; &nbsp;
                <button class="btn btn-danger" type="button">
                  Deactive Port
              </button>
            </div>

        </div>
  </section>
{{-- ===================== --}}
@endsection

@section('custom_style')
<link href="{{ asset('assets/datatable/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('assets/datatable/rowReorder.dataTables.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('assets/datatable/responsive.dataTables.min.css') }}" rel="stylesheet" type="text/css">
<style>
    @media(max-width:575px){
        .abcd{
            width: 130px;
        }
    }
    
    </style>
@endsection
@section('custom_script')
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/rowreorder/1.2.8/js/dataTables.rowReorder.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script type="text/javascript">
    // $('#user-list-table').DataTable();

    $(document).ready(function() {
    var table = $('#api-list-table').DataTable( {
        responsive: true,
        
    } );
} );
</script>
@endsection