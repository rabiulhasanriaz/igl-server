@extends('user.master')

@section('load_menu_class','open')
@section('menu_flexibook', 'active')
@section('menu_create_flexibook', 'active')

@section('page_location')
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="{{ route('user.index') }}">Dashboard</a>
        </li>
        <li>
            <a href="{{ route('user.flexiload.create') }}">Flexiload</a>
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

    <div class="row">
        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7">
            @include('admin.partials.session_messages')
            <div class="" style="">
                
                <form action="{{ route('user.flexiload.flexibook_create') }}" id="dynamic_sms_send1" method="post" class="form-horizontal"
                      role="form" enctype="multipart/form-data">
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
                            <label id="book_name">Flexibook Name</label>
                            <input type="text" name="book_name" class="form-control" required="" placeholder="Give a tittle ">
                        </div>

                        <div class="form-group">
                            <label for="dynamic_name_column"> Select name Column <span style="color: red;">*</span> </label>
                            <br/>
                            <select class="chosen-selecta form-control" id="dynamic_name_column" data-placeholder="Select Sender ID.."
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
                            <label for="dynamic_number_type_column"> Select number type Column <span style="color: red;">*</span> </label>
                            <br/>
                            <select class="chosen-selecta form-control" id="dynamic_number_type_column" data-placeholder="Select number type .."
                                    name="dynamic_number_type_column" required="">
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="remarks">Select Remarks Column<span style="color: red;"></span> </label>
                            <br/>
                            <select class="chosen-selecta form-control" id="dynamic_remarks_column" data-placeholder="Select number type .." name="dynamic_remarks_column" required="">
                                
                            </select>
                        </div>




                        <div class="form-group">
                            <label for="flexipin">Your Secret Flexipin number<span style="color: red;">*</span></label>
                            <input type="text" name="flexipin" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for=""></label>
                            <input type="submit" class="form-control btn btn-primary">
                        </div>

                    </div>
                </form>

            </div>
        </div><!-- /.col -->

        <div class="col-sm-5 col-md-5">
            <table class="table table-bordered table-striped"> 
                <caption class="h4">Demo Excel sheet</caption>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Number</th>
                        <th>Amount</th>
                        <th>number type</th>
                        <th>Remarks</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td>Name 1</td>
                        <td>013xxxxxxxx</td>
                        <td>10</td>
                        <td>1</td>
                        <td>Employee</td>
                    </tr>
                    <tr>
                        <td>Name 2</td>
                        <td>014xxxxxxxx</td>
                        <td>20</td>
                        <td>1</td>
                        <td>Customer</td>
                    </tr>
                    <tr>
                        <td>Name 3</td>
                        <td>013xxxxxxxx</td>
                        <td>200</td>
                        <td>1</td>
                        <td>Customer of xxx company</td>
                    </tr>
                    <tr>
                        <td>Name 4</td>
                        <td>013xxxxxxxx</td>
                        <td>150</td>
                        <td>1</td>
                        <td>Customer of xyz company</td>
                    </tr>
                    <tr>
                        <td>Name 5</td>
                        <td>013xxxxxxxx</td>
                        <td>200</td>
                        <td>1</td>
                        <td>CUstomer of xxx company</td>
                    </tr>
                </tbody>
            </table>
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
    <script src="{{ asset('assets') }}/js/ajax_send_sms.js"></script>

    <!--suppress LocalVariableNamingConventionJS -->
    <script type="text/javascript">
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
            valid_dynamic_flexiBook_file('dynamic_sms_send1', '{{ route('user.sms.checkDynamicFile') }}');
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
