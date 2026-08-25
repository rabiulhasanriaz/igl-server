@extends('admin.master')

@section('English_menu_class','open')
@section('assign_route', 'active')
@section('page_location')
<ul class="breadcrumb">
	<li>
		<i class="ace-icon fa fa-home home-icon"></i>
		<a href="{{ route('admin.index') }}">Dashboard</a>
	</li>
	<li class="active">Assign Route</li>
</ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
<h1>
	Assign Route
	<small>
		<i class="ace-icon fa fa-angle-double-right"></i>
		 Add
	</small>
</h1>
@endsection

@section('main_content')

<div class="space-6"></div>
@include('admin.partials.session_messages')
@include('admin.partials.all_error_messages')

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-container">

            <form action="{{ route('admin.english.assign-route-store') }}" method="post" class="form-horizontal"
                  role="form">

                @csrf
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 col-lg-offset-3 col-md-offset-3">
                    <!-- PAGE CONTENT BEGINS -->
                    <div class="form-group">
                        <label for="form-field-select-3"> Route </label>
                            <br/>
                            <select class="chosen-select form-control" id="form-field-select-3"
                                    data-placeholder="Operator name.." name="route_name" required="">
                                <option value=""></option>
                                @foreach ($routes as $route)
                                    <option value="{{ $route->id }}">{{ $route->route_name }}</option>
                                @endforeach
                            </select>
                        
                    </div>


                    <div class="form-group">
                        <label for="form-field-select-3"> User name</label>
                        <br/>
                        <select class="select2 form-control" id="form-field-select-3"
                                data-placeholder="Operator name.." name="user_name" required="">
                            <option value=""></option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->company_name }}</option>
                            @endforeach
                        </select>
                        
                    </div>


                    <div class="clearfix form-group">

                        <input type="submit" class="btn btn-info" value="Submit">
                        &nbsp; &nbsp; &nbsp;
                        <button class="btn btn-danger" type="reset">
                            <i class="ace-icon fa fa-undo bigger-110"></i>
                            Reset
                        </button>
                    </div>
                </div>

            </form>
        </div><!-- end bg-container-->
    </div>
</div>
<div class="row">
    <table id="example" class="display nowrap" style="width:100%">
        <thead>
            <tr>
                <th>SL</th>
                <th>Route Name</th>
                <th>User</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @php($sl=0)
            @foreach ($assinged_routes as $route)
            <tr>
                <td>{{ ++$sl }}</td>
                <td>{{ $route->routeDetail->route_name }}</td>
                <td>{{ $route->userDetail->company_name ?? "" }}</td>
                <td>
                    @if ($route->status == 1)
                        <span class="text-success">Active</span>
                    @else
                        <span class="text-danger">In-Active</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('admin.english.assign-route-edit',$route->id) }}" class="btn btn-xs btn-info">Edit</a>
                    <a href="{{ route('admin.english.assign-route-delete',$route->id) }}" onclick="return confirm('Are you sure to delete this Assigned route?')" class="btn btn-xs btn-danger">Delete</a>
                </td>
            </tr> 
            @endforeach
        </tbody>
    </table>
</div><!-- /.row -->


@endsection




@section('custom_style')
	<link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/rowreorder/1.2.7/css/rowReorder.dataTables.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
	<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.6/css/responsive.dataTables.min.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/css/chosen.min.css"/>
@endsection

@section('custom_script')
	<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
	<script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
	<script src="https://cdn.datatables.net/rowreorder/1.2.7/js/dataTables.rowReorder.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.6/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
	<script type="text/javascript">
       $(document).ready(function() {
            var table = $('#example').DataTable( {
                rowReorder: {
                    selector: 'td:nth-child(2)'
                },
                responsive: true
            } );
        } );

        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
    <script src="{{ asset('assets') }}/js/chosen.jquery.min.js"></script>
    <script type="text/javascript">
        $('.chosen-select').chosen({allow_single_deselect: true});
    </script>
	
@endsection