@extends('admin.master')

@section('dynamic_comission_class','active')
@section('page_location')
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="{{ route('admin.index') }}">Dashboard</a>
        </li>
        <li class="active">Dynamic Permission Set</li>
    </ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session()->get('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
    @endif
    @if (session()->has('suspend'))
    <div class="alert alert-danger alert-dismissible" role="alert">
        {{ session()->get('suspend') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
@endif
    <h1>
        API Permission
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            Set
        </small>
    </h1>
@endsection

@section('main_content')

    <div class="space-6"></div>


    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

            @include('admin.partials.session_messages')
            @include('admin.partials.all_error_messages')
            
            <form action="{{ route('admin.flexiload.reload-load-all') }}" method="get">
                @csrf
                
            <table class="table table-striped table-bordered table-hover" id="reseller_list">
                <thead>
                <tr>
                    <th>SL</th>
                    <th>User name</th>
                    <th>Company Name</th>
                    <th>Cellphone</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @php
                ($sl=0)
                @endphp
                @foreach ($api_user as $user)
                <tr>
                    <td>{{ ++$sl }}</td>
                    <td>{{ $user->userDetail->name }}</td>
                    <td>{{ $user->company_name }}</td>
                    <td>{{ $user->cellphone }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @if ($user->userDetail->dynamic_permission == 1)
                            <span class="text-success">Active</span>
                        @elseif($user->userDetail->dynamic_permission == 0)
                        <span class="text-danger">Suspend</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.dynamic-permission-active', $user->id) }}" onclick="return confirm('Are you sure?')" style="color: green;">Active</a>
                        |
                        <a href="{{ route('admin.dynamic-permission-suspend', $user->id) }}" onclick="return confirm('Are you sure?')" style="color: red;">Suspend</a>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>

        </div><!-- /.col -->
    </div><!-- /.row -->


@endsection


@section('custom_script')
    <script src="{{ asset('assets') }}/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('assets') }}/js/jquery.dataTables.bootstrap.min.js"></script>
    <script type="text/javascript">
        $('#reseller_list').DataTable();
        function submitLimitForm(formName){
            if(confirm('Are you Sure')) {
                $("#" + formName).submit();
            }
        }

        function copy(that){
        var inp =document.createElement('input');
        document.body.appendChild(inp);
        inp.value =that;
        inp.select();
        document.execCommand('copy',false);
        inp.remove();
        }


        $(document).ready(function(){
            $('#select-all').click(function(event) {   
                if(this.checked) {
                    // Iterate each checkbox
                    $(':checkbox').each(function() {
                        this.checked = true;                        
                    });
                } else {
                    $(':checkbox').each(function() {
                        this.checked = false;                       
                    });
                }
            });
        })
        // $(document).ready(function(){
        //      $('#check_all').click(function () {    
        //          var id = $(':checkbox').prop('checked', this.checked).val();
                   
        // });
        

        var checker = document.getElementById('select-all');
        var sendbtn = document.getElementById('sendNewSms');
        // when unchecked or checked, run the function
        checker.onchange = function(){
        if(this.checked){
            sendbtn.disabled = false;
        } else {
            sendbtn.disabled = true;
        }

        }
        // if (document.getElementById('checkme') != null) {
        //     str = document.getElementById("checkme").value;
        //     console.log(str);
        // }else{
        //     console.log('a');
        // }
        
    </script>

@endsection
