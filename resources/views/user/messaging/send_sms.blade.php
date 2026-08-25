@php($permission = explode(',',Auth::user()->permission))
@if (in_array(2,$permission))
    @php($flexi_permission = true)
@else
    @php($flexi_permission = false)
@endif
@extends('user.master')

@section('messaging_menu_class','open')
@section('send_sms_menu_class','active')
@section('page_location')
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="{{ route('user.index') }}">Dashboard</a>
        </li>
        <li class="active">SMS</li>
    </ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
    <h1>
        SMS
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            Send
        </small>
    </h1>
@endsection


@section('main_content')

    <div class="row">
        <div class="col-xs-12">
            <!-- PAGE CONTENT BEGINS -->
            <div class="tabbable">

                @include('user.partials.session_messages')
                @include('user.partials.all_error_messages')
                <div class="ajax_error" style="display: none">
                    <div class="alert alert-danger" id="report-alert">
                        <button type="button" class="close"><span style="font-size: 20px;">x</span>
                        </button>
                        <span class="error_messages"></span>
                    </div>
                </div>
                <div class="ajax_success" style="display: none">
                    <div class="alert alert-success" id="report-alert">
                        <button type="button" class="close"><span style="font-size: 20px;">x</span>
                        </button>
                        <span class="success_messages"></span>
                    </div>
                </div>

                <ul class="nav nav-tabs padding-18 tab-size-bigger" id="myTab">

                    <li class="@if((old('upload_file')==false) && (old('upload_')==false) && (old('upload_fi')==false))active @endif">
                        <a onclick="$('#get_message_template_input_id').val('send-sms-content')" data-toggle="tab"
                           href="#send-sms-content">
                            <i class="green ace-icon fa fa-envelope bigger-120"></i>
                            Send SMS
                        </a>
                    </li>
                    <li class="@if(old('upload_file')=='upload')active @endif">
                        <a onclick="$('#get_message_template_input_id').val('upload-file-content')" data-toggle="tab"
                           href="#upload-file-content">
                            <i class="orange ace-icon fa fa-arrow-circle-o-down bigger-120"></i>
                            Upload File
                        </a>
                    </li>
                    <li>
                        <a onclick="$('#get_message_template_input_id').val('group-contact-content')" data-toggle="tab"
                           href="#group-contact-content">
                            <i class="orange ace-icon fa  fa-cogs bigger-120"></i>
                            Group Contact
                        </a>
                    </li>
                    <li>
                        <a onclick="$('#get_message_template_input_id').val('dynamic-sms-content')" data-toggle="tab"
                           href="#dynamic-sms-content">
                            <i class="orange ace-icon fa fa-bullseye bigger-120"></i>
                            Dynamic SMS
                        </a>
                    </li>
                    @if($flexi_permission)
                    <li>
                        <a onclick="$('#get_message_template_input_id').val('employee-sms-content')" data-toggle="tab"
                           href="#employee-sms-content">
                            <i class="orange ace-icon fa fa-bullseye bigger-120"></i>
                            Employee
                        </a>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
    <div class="space-6"></div>

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 padding">
                <!-- PAGE CONTENT BEGINSInclude('user.messaging.smspart.send_sms_content') -->
                <div class="tab-content">
                    <div id="send-sms-content"
                         class="tab-pane fade @if((old('upload_file')==false) && (old('upload_')==false) && (old('upload_fi')==false))in active @endif">

                         <i class="fa fa-arrow-right"></i>
                         <a href="https://youtu.be/6Yg50-9RxFQ" target="_blank" class="text-uppercase text-danger">
    click to see sms sending details in tutorial
</a>
                        @include('user.messaging.smspart.send_sms_content')
                    </div>
                    <div id="upload-file-content"
                         class="tab-pane fade @if(old('upload_file')=='upload')in active @endif">

                         <i class="fa fa-arrow-right"></i>
                         <a href="https://youtu.be/Se9uhresM-I" target="_blank" class="text-uppercase text-danger" >click to see sms sending details in tutorial</a>
                        @include('user.messaging.smspart.upload_file_content')
                    </div>
                    <div id="group-contact-content" class="tab-pane fade">

                        <i class="fa fa-arrow-right"></i>
                         <a href="https://youtu.be/At_8iePn76E?list=PL5yt3Rf_mT7xOX31x2B5JEw1UMWgRcUyt" target="_blank" class="text-uppercase text-danger" >click to see sms sending details in tutorial</a>
                        @include('user.messaging.smspart.group_contact_content')
                    </div>
                    <div id="dynamic-sms-content" class="tab-pane fade">

                        <i class="fa fa-arrow-right"></i>
                         <a href="https://youtu.be/5jAI3XCGGNE?list=PL5yt3Rf_mT7xOX31x2B5JEw1UMWgRcUyt" target="_blank"  class="text-uppercase text-danger" >click to see sms sending details in tutorial</a>
                        @include('user.messaging.smspart.dynamic_sms_content')
                    </div>
                    <div id="employee-sms-content" class="tab-pane fade">

                        <i class="fa fa-arrow-right"></i>
                         <a href="#" class="text-uppercase text-danger" >click to see sms sending details in tutorial</a>
                        @include('user.messaging.smspart.employee_sms_content')
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="background: #f9f9f9; color: red;">
                        <h3 class="text-danger">Note for Masking SMS</h3>
                        <ul>
                            <li> For Bangla/Unicode: Max 315 Character...
                            </li>
                            <li> Text with Emoji Will count as Unicode Content
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="background: #f9f9f9;">
                        <h3>Number format</h3>
                        <p>88017xxxxxxxx</p>
                        <p>017xxxxxxxx</p>
                        <p>17xxxxxxxx</p>
                    </div>

                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="background: #f9f9f9;">
                        <h3 class="text-primary">SMS Recipient</h3>
                        <p align="justify">Before doing any campaign we recommend you to do a testing with the sender id
                            to your number to ensure the sender id is working fine.</p>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="background: #f9f9f9;">
                        <h3 class="text-primary">SMS Content</h3>
                        <p align="justify">
                        <ul>
                            <li> 1 Text (English: 160 and Bangla: 70 Character)</li>
                            <li> 2 or more Text (English: 153 X n and Bangla: 67 X n Character)</li>
                        </ul>
                        </p>
                    </div>
                </div>
            </div>


            <div class="col-lg-12 col-md-12 col-sm-12 col-lg-12">
                <div id="send_sms_template_modal" class="modal fade" tabindex="-1" style="display: none;">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <h3 class="smaller lighter blue no-margin text-primary"> SMS Template </h3>
                            </div>
                            <div class="modal-body">
                                <table class="table table-responsive table-bordered">
                                    <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>Title</th>
                                        <th>Message</th>
                                        <th>Quantity</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php($serial=1)
                                    @foreach(Auth::user()->templates as $template)
                                        <tr>
                                            <td>{{ $serial++ }}</td>
                                            <td>{{ $template->st_name }}</td>
                                            <td class="nr"
                                                id="get_sms_template_{{$template->id}}">{{ $template->st_content }}</td>
                                            <td class="smsQuantity">{{ $template->st_total_sms }}</td>
                                            <td>
                                                <input type="hidden" id="get_message_template_input_id"
                                                       value="send-sms-content">
                                                <button class="use-address btn btn-xs btn-primary" data-dismiss="modal"
                                                        onclick="get_sms_template('{{$template->id}}', $('#get_message_template_input_id').val())"
                                                        aria-hidden="true">Insert
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div><!-- /.modal-content -->
                        <div id="aside-inside-modal"
                             class="modal aside aside-contained aside-bottom aside-hz aside-dark aside-hidden no-backdrop"
                             data-placement="bottom" data-background="true" data-backdrop="false" tabindex="-1">
                        </div>
                    </div><!-- /.modal-dialog -->
                </div>
            </div>
        </div><!-- /.col -->
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
    <script src="{{ asset('assets') }}/js/jquery.textareaCounter.plugin.js?v=1.1.1"></script>
    <script src="{{ asset('assets') }}/js/text-area-counter.js"></script>
    <script src="{{ asset('assets') }}/js/moment.min.js"></script>
    <script src="{{ asset('assets') }}/js/bootstrap-datetimepicker.min.js"></script>
    <script src="{{ asset('assets') }}/js/ajax_send_sms.js?v=1.0.0"></script>

    <!--suppress LocalVariableNamingConventionJS -->
    <script type="text/javascript">
    $("#file_preview_content").hide();
        $('.chosen-select').chosen({allow_single_deselect: true});
        $(document).ready(function () {
            count_textarea('#send-sms-content');
            count_textarea('#upload-file-content');
            count_textarea('#group-contact-content');
            count_textarea('#dynamic-sms-content');
            count_textarea('#employee-sms-content');

            $('.date-timepicker').datetimepicker();
        });

        function hide_show_target_time(check_id) {
            if ($(check_id + ' .send_now_checkbox').is(":checked")) {
                $(check_id + " .target_time").hide();
            } else if ($(check_id + ' .send_later_checkbox').is(":checked")) {
                $(check_id + " .target_time").show();
            }
        }

        function get_sms_template(id, parent_id) {
            var sms_content = $('#get_sms_template_' + id).html();
            $('#' + parent_id + ' #message').val(sms_content);
        }

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
            valid_dynamic_sms_file('dynamic_sms_send', '{{ route('user.sms.checkDynamicFile') }}');
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

        $('#upload_file_preview').click(function () {
            check_upload_file('upload_file_send_sms', '{{ route('user.sms.checkUploadFile') }}');
        });
        




        /*sms send form submit*/
        sms_send_form_submit('upload_file_send_sms', '{{ route('user.sms.storeUploadFileSms') }}');
        sms_send_form_submit('single_sms_send', '{{ route('user.sms.storeSingleSms') }}');
        sms_send_form_submit('group_contact_sms_send', '{{ route('user.sms.storeGroupContactSms') }}');
        sms_send_form_submit('dynamic_sms_send', '{{ route('user.sms.storeDynamicSms') }}');
        sms_send_form_submit('employee_group_contact_sms_send', '{{ route('user.sms.storeEmployeeGroupContactSms') }}');
    </script>
@endsection
