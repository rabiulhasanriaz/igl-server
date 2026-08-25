@extends('user.master')

@section('load_menu_class','open')
@section('submenu_load','open')
@section('bulk_flexiload_make_menu_class', 'active')

@section('page_location')
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="{{ route('user.index') }}">Dashboard</a>
        </li>
        <li>
            <a href="{{ route('user.create') }}">Flexiload</a>
        </li>
        <li class="active">Load</li>
    </ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
    <h1>
        Flexiload
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            Bulk Load
        </small>
    </h1>
@endsection

@section('main_content')

    <div class="space-6"></div>
    <ul>
        <li style="font-size: 15px;"><kbd style="background-color: #cc3737;">The Limit of Balance for Robi/Airtel Number is not more than Tk 5000</kbd></li>
    </ul>

    <div class="row">
        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7">
            @include('admin.partials.session_messages')
            <div class="" style="">
                
                <form action="{{ route('user.flexiload.bulkLoadForm') }}" id="dynamic_sms_send1" method="post" class="form-horizontal"
                      role="form" enctype="multipart/form-data" onsubmit="return checkForm(this);">
                    @csrf
                    
                    <div class="form-group">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding">
                            <label for="cphone"> Select File ( <strong style="color: red;">xlsx file only</strong> )</label>
                            <input type="file" id="id-input-file-2a" class=""
                                   style="width: 100%; height: 35px;border: 1px solid lightgray; border-radius: 3px;" name="sms_file"
                                   required=""/>
                            <a href="javascript:;" style="position: relative;bottom: 40px;left: 390px;" id="click1"
                               class="btn btn-primary">Upload</a>
                        </div>
                    </div>
                        


                    <div class="dynamic_msg" style="display: none;">

                        <div class="form-group">
                            <label for="campaign_name">Campaign name</label>
                            <input type="text" name="campaign_name" class="form-control input-sm" placeholder="Campaign name">
                        </div>


                        <div class="form-group">
                            <label for="dynamic_name_column"> Select Your Name <span style="color: red;">*</span> </label>
                            <br/>
                            <select class="chosen-selecta form-control" id="dynamic_name_column" data-placeholder="Select Your Name.."
                                    name="dynamic_name_column" required="">

                            </select>
                        </div>


                        <div class="form-group">
                            <label for="dynamic_number_column"> Select Your Number Column <span style="color: red;">*</span> </label>
                            <br/>
                            <select class="chosen-selecta form-control" id="dynamic_number_column" data-placeholder="Select Sender ID.."
                                    name="dynamic_number_column" required="">

                            </select>
                        </div>

                        <div class="form-group">
                            <label for="dynamic_amount_column"> Select Your Flexiload Amount Column <span style="color: red;">*</span> </label>
                            <br/>
                            <select class="chosen-selecta form-control" id="dynamic_amount_column" data-placeholder=".."
                                    name="dynamic_amount_column" required="">

                            </select>
                        </div>

                        <div class="form-group">
                            <label for="dynamic_amount_column"> Select number type <span style="color: red;">*</span> </label>
                            <br/>
                            <select class="chosen-selecta form-control" id="dynamic_number_type_column" data-placeholder="Select number type .."
                                    name="dynamic_number_type_column" required="">
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="dynamic_amount_column"> Select number operator <span style="color: red;">*</span> </label>
                            <br/>
                            <select class="chosen-selecta form-control" id="dynamic_number_operator_column" data-placeholder="Select number type .."
                                    name="dynamic_number_operator_column" required="">
                                    
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="remarks"> Remarks <span style="color: red;"></span> </label>
                            <br/>
                            <input type="text" class="form-control" name="remarks" >
                        </div>




                        <div class="form-group">
                            <label for="flexipin">Your Secret Flexipin number<span style="color: red;">*</span></label>
                            <input type="password" name="flexipin" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for=""></label>
                            <input type="submit" name="myButton" class="form-control btn btn-primary">
                        </div>

                    </div>
                </form>

            </div>
        </div><!-- /.col -->

        <div class="col-sm-5 col-md-5">
            <table class="table table-bordered table-striped">
                <div>
                    <ul style="font-weight: bold; color: green">
                        <li>Amount must be greater than 10 and less than 1000</li>
                        <li>Number type should be only 1,2 or 3. 1 for prepaid, 2 for postpaid and 3 for Skitto.</li>
                    </ul>    
                </div> 

                <caption class="h4">Demo Excel sheet</caption>
                <thead>
                    <tr>
                        <th>Number</th>
                        <th>Amount</th>
                        <th>number type</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td>013xxxxxxxx</td>
                        <td>10</td>
                        <td>3(Skitto)</td>
                    </tr>
                    <tr>
                        <td>014xxxxxxxx</td>
                        <td>15</td>
                        <td>1 (Prepaid)</td>
                    </tr>
                    <tr>
                        <td>015xxxxxxxx</td>
                        <td>10</td>
                        <td>2 (Postpaid)</td>
                    </tr>
                    <tr>
                        <td>016xxxxxxxx</td>
                        <td>15</td>
                        <td>1 (Prepaid)</td>
                    </tr>
                    <tr>
                        <td>017xxxxxxxx</td>
                        <td>10</td>
                        <td>1 (Prepaid)</td>
                    </tr>
                    <tr>
                        <td>018xxxxxxxx</td>
                        <td>199</td>
                        <td>2 (Postpaid)</td>
                    </tr>
                    <tr>
                        <td>019xxxxxxxx</td>
                        <td>280</td>
                        <td>1 (Prepaid)</td>
                    </tr>
                </tbody>
            </table>
            <p style="color: green;">Download a <a style="font-weight: bold;" href="{{ asset('assets/bulkLoadDemo.xlsx') }}"> Demo Excel</a></p>
        </div>
    </div><!-- /.row -->
@endsection

@section('custom_style')
    <link rel="stylesheet" href="{{ asset('assets') }}/css/chosen.min.css"/>
    <link rel="stylesheet" href="{{ asset('assets') }}/css/bootstrap-datetimepicker.min.css"/>

    <style>
        .chosen-container {
            width: 100% !important;
        }

        .tab-content {
            border: none !important;
        }
        .fade {
            opacity: 1 !important;
        }
    </style>
@endsection

@section('custom_script')
    <script src="{{ asset('assets') }}/js/chosen.jquery.min.js"></script>
    <script src="{{ asset('assets') }}/js/jquery.textareaCounter.plugin.js"></script>
    <script src="{{ asset('assets') }}/js/text-area-counter.js"></script>
    <script src="{{ asset('assets') }}/js/moment.min.js"></script>
    <script src="{{ asset('assets') }}/js/bootstrap-datetimepicker.min.js"></script>
    <script src="{{ asset('assets') }}/js/ajax_send_sms.js?v=1.0"></script>

    <!--suppress LocalVariableNamingConventionJS -->
    <script type="text/javascript">
        $('form').submit(function() {
          $(this).find("input[type='submit']").prop('disabled',true);
        });
        $('.chosen-select').chosen({allow_single_deselect: true});
        $(document).ready(function () {
            
        });

        $('#id-input-file-1 , #id-input-file-2').ace_file_input({
            no_file: 'No File ...',
            btn_choose: 'Choose',
            btn_change: 'Change',
            droppable: false,
            onchange: null,
            thumbnail: false //| true | large
            //whitelist:'gif|png|jpg|jpeg'
            //blacklist:'exe|php'
            //onchange:''

        });


        $('#click1').click(function () {
            valid_dynamic_flexiload_file('dynamic_sms_send1', '{{ route('user.flexiload.checkDynamicFile') }}');
        });
        $(".close").click(function () {
            $(".ajax_error").hide();
            $(".ajax_success").hide();

        });
        $("#id-input-file-2a").change(function () {
            $("#dynamic_number_column").empty();
            $(".dynamic_msg").hide();
        });

        /*getSmsField*/
        function getSmsField(string){
            let retVal = '[#'+ string +'#]';
            let preVal = $("#dynamic-sms-content .count_me").val();
            let curVal = preVal+retVal;
            $("#dynamic-sms-content .count_me").val(curVal);

        }

        /*checkUploadedFile*/
        function checkUploadedFile(){
            if(!$('#dynamic_number_column').is(':visible'))
            {
                alert('please upload file first');
            }
        }
    </script>
@endsection
