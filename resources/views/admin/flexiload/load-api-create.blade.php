@extends('admin.master')

@section('flexiload_menu_class','open')
@section('flexiload_api_view_class','open')
@section('flexiload_load_api_class', 'active')
@section('page_location')


<ul class="breadcrumb">
    <li>
        <i class="ace-icon fa fa-home home-icon"></i>
        <a href="{{ route('admin.index') }}">Dashboard</a>
    </li>
    <li class="active">API Registration</li>
</ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
<h1>
    API Registration
    <small>
        <i class="ace-icon fa fa-angle-double-right"></i>
        Register
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

            <form action="{{ route('admin.flexiload.load-api-store') }}" method="post" class="form-horizontal" role="form" enctype="multipart/form-data">

              @csrf

            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 col-lg-offset-3 col-md-offset-3">
                
              <div class="form-group">
                <label class="col-sm-4 control-label no-padding-right" for="form-operator-subname"> Operator Name: </label>

                <div class="col-sm-5">
                    <select name="operator_name" class="form-control">
                        <option value="">---Select Operator---</option>
                        @foreach ($operators as $item)
                            <option value="{{ $item->ope_operator_name }}">{{ $item->ope_operator_name }}</option>
                        @endforeach
                        </select>
                </div>
            </div>


            <div class="form-group">
                <label class="col-sm-4 control-label no-padding-right" for="form-operator-subname"> Operator Sub-Name: </label>

                <div class="col-sm-8">
                    <input type="text" id="operator_subname" placeholder="Operator Sub-name" name="operator_subname" class="col-xs-10 col-sm-7" maxlength="6" value=""/>
                </div>
            </div>

              <div class="form-group">
                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Operator IP : </label>

                <div class="col-sm-8">
                    <input type="text" id="ipv4" placeholder="Operator IP" name="operator_ip" class="col-xs-10 col-sm-7" required="" value="" maxlength="15" required=""/>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Operator Port : </label>

                <div class="col-sm-8">
                    <input type="number" id="operator_port" oninput="maxLengthCheck(this)" placeholder="Operator Port" name="operator_port"
                           class="col-xs-10 col-sm-7" required="" value="" maxlength="5" required=""/>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-4 control-label no-padding-right" for="form-operator-ussd-prepaid"> USSD (Prepaid): </label>

                <div class="col-sm-8">
                    <input type="number" id="operator_ussd_postpaid" oninput="maxLengthCheck(this)" placeholder="USSD code Prepaid" name="operator_ussd_prepaid" class="col-xs-10 col-sm-10" required="" maxlength="3" value=""/>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-4 control-label no-padding-right" for="form-operator-ussd_postpaid"> USSD (Postpaid): </label>

                <div class="col-sm-8">
                    <input type="number" id="operator_ussd_postpaid" oninput="maxLengthCheck(this)" placeholder="USSD code Postpaid" name="operator_ussd_postpaid" class="col-xs-10 col-sm-10" maxlength="3" required="" value=""/>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-4 control-label no-padding-right" for="form-operator-subname"> Operator User: </label>

                <div class="col-sm-8">
                    <input type="text" id="operator_user" placeholder="Operator User" name="operator_user" class="col-xs-10 col-sm-10" required="" value=""/>
                </div>
            </div>


            <div class="form-group">
                <label class="col-sm-4 control-label no-padding-right" for="form-operator-subname"> Operator Password: </label>

                <div class="col-sm-8">
                    <input type="text" id="operator_password" placeholder="Operator Password" name="operator_password" class="col-xs-10 col-sm-10" required="" value=""/>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-4 control-label no-padding-right" for="form-operator-flexipin"> Flexipin: </label>

                <div class="col-sm-8">
                    <input type="number" oninput="maxLengthCheck(this)" id="operator_flexipin" placeholder="Port Flexipin" name="operator_flexipin" class="col-xs-10 col-sm-5" maxlength= "5" required="" value=""/>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-4 control-label no-padding-right" for="form-operator-flexipin"> User Port: </label>

                <div class="col-sm-8">
                    <input type="number" id="operator_user_port" oninput="maxLengthCheck(this)" placeholder="Operator User Port" name="operator_user_port" class="col-xs-10 col-sm-10" maxlength="5" required="" value=""/>
                </div>
            </div>

                <div class="clearfix form-group">
                  <div class="col-md-offset-3 col-md-9">

                      <input type="submit" class="btn btn-info" value="Submit">

                      &nbsp; &nbsp; &nbsp;
                      <button class="btn btn-danger" type="reset">
                        <i class="ace-icon fa fa-undo bigger-110"></i>
                        Reset
                    </button>
                  </div>
              </div>
            </div>
          </form>
        </div><!-- end bg-container-->
    </div>
</div>


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



        function maxLengthCheck(object)
        {
            if (object.value.length > object.maxLength)
            object.value = object.value.slice(0, object.maxLength)
        }

    </script>



@endsection