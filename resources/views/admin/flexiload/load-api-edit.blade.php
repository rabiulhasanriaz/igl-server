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
        <li class="active">Load API Register</li>
    </ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
    <h1>
        Api Register
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            Edit
        </small>
    </h1>
@endsection

@section('main_content')

    <div class="space-6"></div>

    @include('admin.partials.session_messages')

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                <form action="{{ route('admin.flexiload.load-api-update',$apiInfo->operator_id) }}" method="post" class="form-horizontal" role="form" enctype="multipart/form-data">

                    @csrf
      
                  <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 col-lg-offset-3 col-md-offset-3">
                    <div class="form-group">
                      <label class="col-sm-4 control-label no-padding-right" for="form-operator-subname"> Operator Name: </label>
      
                      <div class="col-sm-5">
                        <select name="operator_name" required class="form-control">
                            <option value="">---Select Operator---</option>
                            @foreach ($operators as $item)
                                <option value="{{ $item->ope_operator_name }}" {{ $apiInfo->operator_name == $item->ope_operator_name ? 'selected':'' }}>
                                    {{ $item->ope_operator_name }}
                                </option>
                            @endforeach
                            </select>
                      </div>
                  </div>
      
      
                  <div class="form-group">
                      <label class="col-sm-4 control-label no-padding-right" for="form-operator-subname"> Operator Sub-Name: </label>
      
                      <div class="col-sm-8">
                          <input type="text" id="operator_subname" placeholder="Operator Sub-name" name="operator_subname" class="col-xs-10 col-sm-7" required="" value="{{ $apiInfo->operator_subname }}"/>
                      </div>
                  </div>
      
                    <div class="form-group">
                      <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Operator IP : </label>
      
                      <div class="col-sm-8">
                          <input type="text" id="operator_ip" placeholder="Operator IP" name="operator_ip"
                                 class="col-xs-10 col-sm-7" required="" value="{{ $apiInfo->operator_ip }}" maxlength="15" required=""/>
                      </div>
                  </div>
      
                  <div class="form-group">
                      <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Operator Port : </label>
      
                      <div class="col-sm-8">
                          <input type="text" id="operator_port" placeholder="Operator Port" name="operator_port"
                                 class="col-xs-10 col-sm-7" required="" value="{{ $apiInfo->operator_port }}" maxlength="5" required=""/>
                      </div>
                  </div>
      
                  <div class="form-group">
                      <label class="col-sm-4 control-label no-padding-right" for="form-operator-ussd-prepaid"> USSD (Prepaid): </label>
      
                      <div class="col-sm-8">
                          <input type="text" id="operator_ussd_postpaid" placeholder="USSD code Prepaid" name="operator_ussd_prepaid" class="col-xs-10 col-sm-10" required="" maxlength="3" value="{{ $apiInfo->operator_ussd_prepaid }}"/>
                      </div>
                  </div>
      
                  <div class="form-group">
                      <label class="col-sm-4 control-label no-padding-right" for="form-operator-ussd_postpaid"> USSD (Postpaid): </label>
      
                      <div class="col-sm-8">
                          <input type="text" id="operator_ussd_postpaid" placeholder="USSD code Postpaid" name="operator_ussd_postpaid" class="col-xs-10 col-sm-10" maxlength="3" required="" value="{{ $apiInfo->operator_ussd_postpaid }}"/>
                      </div>
                  </div>
      
                  <div class="form-group">
                      <label class="col-sm-4 control-label no-padding-right" for="form-operator-subname"> Operator User: </label>
      
                      <div class="col-sm-8">
                          <input type="text" id="operator_user" placeholder="Operator User" name="operator_user" class="col-xs-10 col-sm-10" required="" value="{{ $apiInfo->operator_user }}"/>
                      </div>
                  </div>
      
      
                  <div class="form-group">
                      <label class="col-sm-4 control-label no-padding-right" for="form-operator-subname"> Operator Password: </label>
      
                      <div class="col-sm-8">
                          <input type="text" id="operator_password" placeholder="Operator Password" name="operator_password" class="col-xs-10 col-sm-10" required="" value="{{ $apiInfo->operator_password }}"/>
                      </div>
                  </div>
      
                  <div class="form-group">
                      <label class="col-sm-4 control-label no-padding-right" for="form-operator-flexipin"> Flexipin: </label>
      
                      <div class="col-sm-8">
                          <input type="text" id="operator_flexipin" placeholder="Port Flexipin" name="operator_flexipin" class="col-xs-10 col-sm-5" maxlength="5" required="" value="{{ $apiInfo->operator_flexipin }}"/>
                      </div>
                  </div>
      
                  <div class="form-group">
                      <label class="col-sm-4 control-label no-padding-right" for="form-operator-flexipin"> User Port: </label>
      
                      <div class="col-sm-8">
                          <input type="text" id="operator_user_port" placeholder="Operator User Port" name="operator_user_port" class="col-xs-10 col-sm-10" maxlength="5" required="" value="{{ $apiInfo->operator_user_port }}"/>
                      </div>
                  </div>
      
                      <div class="clearfix form-group">
                        <div class="col-md-offset-3 col-md-9">
      
                            <input type="submit" class="btn btn-info" value="Update">
      
                            &nbsp; &nbsp; &nbsp;
                        </div>
                    </div>
                  </div>
                </form>


        </div><!-- /.col -->
    </div><!-- /.row -->


@endsection
@section('custom_style')
<link rel="stylesheet" href="{{ asset('assets') }}/css/bootstrap-datepicker3.min.css"/>
@endsection

@section('custom_script')
    <script src="{{ asset('assets') }}/js/data-mask.js" type="text/javascript"></script>
    <script src="{{ asset('assets') }}/js/bootstrap-datepicker.min.js"></script>
    @include('admin.ajax.check_existence')
    <script>
        // $('#view_archived_report').DataTable();
        $(document).ready(function () {
            $('#start').datepicker({
                autoclose: true,
                todayHighlight: true
            });
            
        });
    </script>
@endsection