@extends('admin.master')

@section('campaign_reshedule_class','active')
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
                        @if ($user->userDetail->campaign_reschedule == 1)
                        <style>
                            input[type=checkbox].ace.ace-switch.ace-switch-4:checked+.lbl::before, input[type=checkbox].ace.ace-switch.ace-switch-5:checked+.lbl::before {
                                background-color: #25af56;
                            }
                        </style>
                        
                        @else
                        <style>
                            input[type=checkbox].ace.ace-switch.ace-switch-4+.lbl::before, input[type=checkbox].ace.ace-switch.ace-switch-5+.lbl::before {
                                background-color: #e41d1d;
                                border: 1px solid #ce2b42;
                                
                            }
                        </style>
                        @endif
                        {{-- <input type="hidden" class="numberStatus" value="{{ $contact->id }}" name="contactId"> --}}
                        {{-- <input id="contactID" name="switch-field-1" {{ ($contact->status=='1')?'checked' : '' }} onchange="updateStatus('{{ $contact->id }}')" value="{{ $contact->id }}" class="ace ace-switch" type="checkbox"/>
                        <span class="lbl"></span> --}}
                        <input name="switch-field-1" id="contactID" {{ ($user->userDetail->campaign_reschedule == 1)?'checked' : '' }} onchange="updateStatus('{{ $user->id }}')" value="{{ $user->id }}" class="ace ace-switch ace-switch-5" type="checkbox" />
                        <span class="lbl"></span>
                        {{-- <input name="switch-field-1" id="contactID" {{ ($contact->status=='1')?'checked' : '' }} onchange="updateStatus('{{ $contact->id }}')" value="{{ $contact->id }}" class="ace ace-switch ace-switch-6" type="checkbox" />
                        <span class="lbl"></span> --}}
                        {{-- <input type="checkbox" class="ace" value="1" {{  }}>
                        <input type="checkbox" class="ace" value="2" {{ $contact->status == 1 ? 'checked' : '' }}> --}}
                        {{-- <label>
                            
                            <input type="checkbox" class="ace supplierClass" id="contactID" name="status" onchange="updateStatus('{{ $contact->id }}')" value="{{ $contact->id }}" required="" {{ $contact->status == 1 ? 'checked' : '' }}>
                            <span class="lbl"> Ac </span>
                        </label> --}}
                        {{-- <label>
                            <input type="checkbox" class="ace supplierClass" id="contactID" name="status" onchange="updateStatus('{{ $contact->id }}')" value="{{ $contact->id }}" required="" {{ ($contact->status == 2) ? 'checked' : '' }}>
                            <span class="lbl"> IN </span>
                        </label> --}}
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
    </script>

<script>
    $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});




 function updateStatus(statusValue){

   $.ajax({
    url:"{{ route('admin.campaign-reshedule-update') }}",
    method:"POST",
    data: {statusValue:statusValue},
    
    success:function(data)
    {
        
    }
   });
  
 }



    
</script>

@endsection
