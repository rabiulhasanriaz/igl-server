@extends('admin.master')

@section('English_menu_class','open')
@section('route_register', 'active')
@section('page_location')
<ul class="breadcrumb">
	<li>
		<i class="ace-icon fa fa-home home-icon"></i>
		<a href="{{ route('admin.index') }}">Dashboard</a>
	</li>
	<li class="active">Route Register</li>
</ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
<h1>
	Route Register
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

            <form action="{{ route('admin.english.route-register-store') }}" method="post" class="form-horizontal"
                  role="form">

                @csrf
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 col-lg-offset-3 col-md-offset-3">
                    <!-- PAGE CONTENT BEGINS -->
                    <div class="form-group">
                        <label for="form-field-select-3">Route Name</label>

                        <input type="text" name="route_name" value="{{ old('route_name') }}"
                               class="form-control" placeholder="Route Name"
                               maxlength="100" required="">
                        
                    </div>


                    <div class="form-group">
                        <label for="form-field-select-3"> User name</label>

                        <input type="text" name="api_username" value="{{ old('api_username') }}"
                               class="form-control" placeholder="Api user name"
                               maxlength="100" required="">
                        
                    </div>

                    <div class="form-group">
                        <label for="form-field-select-3"> Password</label>

                        <input type="text" name="api_password" value="{{ old('api_password') }}"
                               class="form-control" placeholder="Api password"
                               maxlength="100" required="">
                        
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
                <th>User Name</th>
                <th>Password</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @php($sl=0)
            @foreach ($routes as $route)
            <tr>
                <td>{{ ++$sl }}</td>
                <td>{{ $route->route_name }}</td>
                <td>{{ $route->user_name }}</td>
                <td>{{ $route->password }}</td>
                <td>
                    @if ($route->status == 1)
                        <span class="text-success">Active</span>
                    @else
                        <span class="text-danger">In-Active</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('admin.english.route-edit',$route->id) }}" class="btn btn-xs btn-info">Edit</a>
                    <a href="{{ route('admin.english.route-delete',$route->id) }}" onclick="return confirm('Are you sure to delete this route?')" class="btn btn-xs btn-danger">Delete</a>
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
	<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.6/css/responsive.dataTables.min.css" />
@endsection

@section('custom_script')
	<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
	<script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
	<script src="https://cdn.datatables.net/rowreorder/1.2.7/js/dataTables.rowReorder.min.js"></script>
	<script src="https://cdn.datatables.net/responsive/2.2.6/js/dataTables.responsive.min.js"></script>
	<script type="text/javascript">
       $(document).ready(function() {
            var table = $('#example').DataTable( {
                rowReorder: {
                    selector: 'td:nth-child(2)'
                },
                responsive: true
            } );
        } );
	</script>
	@include('admin.ajax.check_sender_id_existence')
@endsection