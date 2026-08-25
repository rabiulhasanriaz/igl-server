@extends('admin.master')

@section('flexiload_menu_class','open')
@section('flexiload_trx_class', 'active')
@section('page_location')
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="{{ route('admin.index') }}">Dashboard</a>
        </li>
        <li class="active">Transaction ID Set</li>
    </ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
    @if (session()->has('update'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session()->get('update') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
    @endif
    <h1>
        Transaction ID
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
            <h4>Balance</h4>
            <ul class="list-group list-inline">
                <li class="list-group-item list-group-item-warning">Airtel =>&nbsp;
                    <span class="badge badge-primary badge-pill">{{ $airtel }}</span>
                </li>
                <li class="list-group-item list-group-item-warning">Banglalink =>&nbsp;
                    <span class="badge badge-primary badge-pill">{{ $bl }}</span>
                </li>
                <li class="list-group-item list-group-item-warning">GrameenPhone =>&nbsp;
                    <span class="badge badge-primary badge-pill">{{ $gp }}</span>
                </li>
                <li class="list-group-item list-group-item-warning">Robi =>&nbsp;
                    <span class="badge badge-primary badge-pill">{{ $robi }}</span>
                </li>
                <li class="list-group-item list-group-item-warning">Teletalk =>&nbsp;
                    <span class="badge badge-primary badge-pill">{{ $tt }}</span>
                </li>
                <li class="list-group-item list-group-item-danger">Total =>&nbsp;
                    <span class="badge badge-danger badge-pill">{{ $total }}</span>
                </li>
            </ul>
            <form action="{{ route('admin.flexiload.reload-load-all') }}" method="get">
                @csrf
                <div class="form-check">
                    <button type="submit"  class="btn btn-danger btn-sm" id="sendNewSms" onclick="return confirm('Are you sure to execute this load again?')">
                        <i class="fa fa-refresh"></i>
                    </button>
                </div>
            <table class="table table-striped table-bordered table-hover" id="reseller_list">
                <thead>
                <tr>
                    <th>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="select-all">
                            <label class="form-check-label" for="defaultCheck1">
                                All
                            </label>
                        </div>
                    </th>
                    <th>SL</th>
                    <th>User name</th>
                    <th>Date</th>
                    <th>Number</th>
                    <th>Amount</th>
                    <th>TrxID</th>
                    <th>Reload</th>
                </tr>
                </thead>
                <tbody>
                @php($serial=1)
                @foreach($load_trx as $trx)
                    <tr>
                        <td>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="trx_pending[]" value="{{ $trx->id }}" id="">
                                <label class="form-check-label" for="defaultCheck1">
                                </label>
                            </div>
                        </form>
                        </td>
                        <td>{{ $serial++ }}</td>
                        <td>
                            {{ $trx->trx_user['company_name'] }}
                        </td>
                        <td>{{ $trx->created_at }}</td>
                        <td onclick="copy('{{ substr($trx->targeted_number,3,10) }}')">
                            {{ substr_replace($trx->targeted_number,' ',3,-strlen($trx->targeted_number)) }}
                        </td>
                        <td class="text-right">{{ number_format($trx->campaign_price,2) }}</td>
                        <td>
                            <form action="{{ route('admin.flexiload.update-trx-id',$trx->id) }}" method="post" style="display: inline;">
                                @csrf()
                                
                               <input type="text" name="trx_id_set" class="form-control" required>
                               <button type="submit" class="btn btn-info btn-sm"><i class="fa fa-check"></i></button>
                            </form>
                            {{--<a href="{{ route('admin.flexiload.reload-load', $trx->id) }}" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure to execute this load again?')"><i class="fa fa-refresh"></i></a>--}}
                       </td>
                       <td>
                           <form action="{{ route('admin.flexiload.reload-load', $trx->id) }}" method="get">
                            @csrf  
                            <label class="radio-inline">
                                <input type="radio" name="num_type" id="type" value="1" {{ ($trx->number_type == 1)? 'checked' : '' }}>Prepaid
                              </label>
                              <label class="radio-inline">
                                <input type="radio" name="num_type" id="type" value="2" {{ ($trx->number_type == 2)? 'checked' : '' }}>Postpaid
                              </label>
                              <label class="radio-inline">
                                <input type="radio" name="num_type" id="type" value="3" {{ ($trx->number_type == 3)? 'checked' : '' }}>Skitto
                              </label>
                              <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure to execute this load again?')"><i class="fa fa-refresh"></i></button>
                        </form>

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
