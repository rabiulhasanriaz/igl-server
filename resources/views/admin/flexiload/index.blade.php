@extends('admin.master')

@section('flexiload_menu_class','open')
@section('flexiload_view_menu_class', 'active')
@section('page_location')
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="{{ route('admin.index') }}">Dashboard</a>
        </li>
        <li class="active">Flexiloadable User Lists</li>
    </ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
    <h1>
        Users
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            List
        </small>
    </h1>
@endsection

@section('main_content')
    
    <!-- Button trigger modal -->
    
    <!-- Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
            <h5 class="modal-title" id="exampleModalLabel">FlexiLoad Details</h5>
          </div>
          <form action="{{ route('admin.flexiload.customize') }}" method="post"> 
          @csrf
              <div class="modal-body">
                    <input type="number" name="user_id" hidden>
                    <div class="form-group">
                        <label for="permission">Permission</label><br />

                        <label class="checkbox-inline ">
                            <input class="load-access" type="checkbox" name="load_access[]" value="1">Single
                        </label>
                        <label class="checkbox-inline">
                            <input class="load-access" type="checkbox"name="load_access[]" value="2" >Package
                        </label><br />
                        <label class="checkbox-inline">
                            <input class="load-access" type="checkbox" name="load_access[]" value="3">Bulk
                        </label>
                        <label class="checkbox-inline">
                            <input class="load-access" type="checkbox" name="load_access[]" value="4">API
                        </label>
                        <label class="checkbox-inline">
                            <input type="checkbox" id="all_load_access">All
                        </label>
                    </div>
                    
                    <div class="form-group">
                        <label for="limit_amount">Limit Amount</label>
                        <input type="number" id="limit_amount" name="limit_amount" placeholder="" value="" class="form-control">
                    </div>
                
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
              </div>
          </form>
        </div>
      </div>
    </div>


    <div class="space-6"></div>

    <div class="row">
        @include('admin.partials.session_messages')
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="row">
                <div class="col-sm-6">
                <p style="color: blue; font-size: 120%;">Eligible Users</p>
                 <table id="flexiload-eligible-user-list-table" class="table table-striped table-bordered table-hover table-sm">
                    <thead>
                    <tr>
                        <th>SL</th>
                        <th>Company Name</th>
                        <th>User Name</th>
                        <th>Limit</th>
                        <th>Action</th>
                    </tr>
                    </thead>

                    <tbody>

                    <!-- reseller lists -->
                    @php($serial=1)
                    @foreach($users as $user)
                        @if ( $user->flexiload_type != '0' )
                                <tr>
                                    <td>{{ $serial++ }}</td>
                                    <td>{{ $user->company_name }}</td>
                                    <td>{{ $user->userDetail['name'] }}</td>
                                    <td>{{ $user->flexiload_limit }}</td>
                                    <td>
                                        <div class="widget-toolbar no-border">
                                            <button class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown"
                                                    aria-expanded="false">
                                                Action
                                                <i class="ace-icon fa fa-chevron-down icon-on-right"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-primary dropdown-menu-right dropdown-caret dropdown-close">
                                                <li>
                                                    <a data-toggle="modal" class="edit-modal-link" onclick="setValueAndPopUpModal('{{ $user->id }}', '{{ $user->flexiload_type }}', '{{ $user->flexiload_limit }}')">
                                                    <i class="ace-icon fa fa-search-plus bigger-130"></i>
                                                    Edit
                                                    </a>
                                                </li>
                                                
                                                <li class="divider"></li>
                                                
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                        @endif
                    @endforeach
                    </tbody>
                 </table>        
                </div>



                <div class="col-sm-6">
                    <p style="color: blue; font-size: 120%;">UnEligible Users</p>
                    <table id="flexiload-non-eligible-user-list-table" class="table table-sm table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Name</th>
                                <th>Company</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @php($serial=1)
                            @foreach($users as $user)
                                @if ( $user->flexiload_type == '0' )
                                    <tr>
                                        <td>{{ $serial++ }}</td>
                                        <td>{{ $user->company_name }}</td>
                                        <td>{{ $user->userDetail['name'] }}</td>
                                        <td>
                                            <div class="widget-toolbar no-border">
                                                <button class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown"
                                                        aria-expanded="false">
                                                    Action
                                                    <i class="ace-icon fa fa-chevron-down icon-on-right"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-primary dropdown-menu-right dropdown-caret dropdown-close">
                                                    <li>
                                                        <a data-toggle="modal" class="edit-modal-link" onclick="setValueAndPopUpModal('{{ $user->id }}', '{{ $user->flexiload_type }}', '{{ $user->flexiload_limit }}')">
                                                        <i class="ace-icon fa fa-search-plus bigger-130"></i>
                                                        Edit
                                                        </a>
                                                    </li>
                                                    <li class="divider"></li>
                                                    
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div><!-- /.col -->
    </div><!-- /.row -->
@endsection


@section('custom_script')
    <script src="{{ asset('assets') }}/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('assets') }}/js/jquery.dataTables.bootstrap.min.js"></script>
    <script type="text/javascript">
        $('#flexiload-eligible-user-list-table').DataTable();
        $('#flexiload-non-eligible-user-list-table').DataTable();

        function setValueAndPopUpModal(id, permission, limit){
            $(".load-access").prop("checked", false);
            $("input[name='user_id']").val(id);
            var res = permission.split("-");
            let i;
            for(i=0; i<res.length; i++) {
                $("input[value="+res[i]+"]").prop("checked", true);
            }
            $("input[name='limit_amount']").val(limit);
            $("#editModal").modal();
        }
        $("#all_load_access").change(function(){
            let all_checked = true;
            $(".load-access").each(function () {
                if(!$(this).is(':checked')) {
                    all_checked = false;
                }
            });
            if(all_checked == true) {
                $(".load-access").prop("checked", false);
            } else {
                $(".load-access").prop("checked", true);
            }
        });
        $(".load-access").change(function () {
            let all_checked = true;
            $(".load-access").each(function () {
                if(!$(this).is(':checked')) {
                    all_checked = false;
                }
            });
             if(all_checked == true) {
                $("#all_load_access").prop("checked", true);
            } else {
                $("#all_load_access").prop("checked", false);
            }
        });
    </script>

@endsection
