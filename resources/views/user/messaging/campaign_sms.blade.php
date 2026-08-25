@extends('user.master')

@section('messaging_menu_class','open')
@section('campaign_menu_class','active')
@section('page_location')
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="{{ route('user.index') }}">Dashboard</a>
        </li>
        <li class="active">Campaign SMS</li>
    </ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
    <h1>
        Campaign SMS
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            Send
        </small>
    </h1>
@endsection


@section('main_content')


    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 padding">
                <!-- PAGE CONTENT BEGINS -->

                <div class="ajax_error" style="display: none">
                    <div class="alert alert-danger" id="report-alert">
                        <button type="button" class="close" data-dismiss="alert"><span style="font-size: 20px;">x</span>
                        </button>
                        <span class="error_messages"></span>
                    </div>
                </div>
                <div class="ajax_success" style="display: none">
                    <div class="alert alert-success" id="report-alert">
                        <button type="button" class="close" data-dismiss="alert"><span style="font-size: 20px;">x</span>
                        </button>
                        <span class="success_messages"></span>
                    </div>
                </div>


                <div class="tab-pane fade active in" id="campaign_sms_send">
                    <form action="{{ route('user.sms.storeCampaignSms') }}" id="campaign_sms_send_form" method="post" class="form-horizontal" role="form" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <table id = "all_category" class="table table-striped table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th>SL</th>
                                    <th> Select Category</th>
                                    <th> Numbers</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($phonebookCampaignCategories as $category)
                                    <tr>
                                        <td>{{ $loop->index+1 }}</td>
                                        <td>
										<span>
											<input type="radio" class="ace" id="select_{{$loop->index}}" name="group_name" value="{{ $category->id }}" required="">
											<label class="lbl" for="select_{{$loop->index}}">&nbsp; {{ $category->name }}</label>
										</span>
                                        </td>

                                        <td style="text-align: right;">{{ count($category->CampaignContacts) }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>


                        <div class="form-group">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding">
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 padding">
                                    <label for="form-field-select-3"> From </label>
                                    <input type="number" name="sl_from_number" class="form-control no-spin" min="1">
                                </div>

                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 padding">
                                    <label for="form-field-select-3"> To </label>
                                    <input type="number" name="sl_to_number" class="no-spin form-control" min="1">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="form-field-select-3"> Sender ID <span style="color: red;">*</span></label>
                            <br/>
                            <select class="chosen-select form-control" id="form-field-select-3"
                                    data-placeholder="Select Sender ID.." name="sender_id" required="">
                                <option value=""></option>
                                @foreach(Auth::user()->senderIds as $senderId)
                                    <option value="{{$senderId->sender->id}}" @if(old('sender_id')==true) {{  (@old('sender_id')==@$senderId->sender->id)? 'selected':'' }}@else{{  (@$defaultSenderId->sender_id==@$senderId->sender->id)? 'selected':'' }}@endif>{{$senderId->sender->sir_sender_id}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Select SMS Type
                                <span class="required"> * </span>
                            </label>
                            <div class="mt-radio-inline">
                                <label class="mt-radio">
                                    <input type="radio" name="recipientsmsRadios" id="recipientsmsRadiosText"
                                           class="ace" value="text" checked="">
                                    <span class="lbl"></span> Text
                                </label> &nbsp;
                                <label class="mt-radio">
                                    <input type="radio" name="recipientsmsRadios" id="recipientsmsRadiosFlash"
                                           class="ace" value="flash" disabled>
                                    <span class="lbl"></span> Flash
                                </label> &nbsp;
                                <label class="mt-radio">
                                    <input type="radio" name="recipientsmsRadios" id="recipientsmsRadiosUnicodeFlash"
                                           class="ace" value="flashunicode" disabled>
                                    <span class="lbl"></span> Flash Unicode
                                </label> &nbsp;
                                <label class="mt-radio">
                                    <input type="radio" name="recipientsmsRadios" id="recipientsmsRadiosUnicode"
                                           class="ace" value="unicode">
                                    <span class="lbl"></span> Unicode
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Enter SMS Content
                                <span class="required" style="color: red;"> * </span>
                                &nbsp;
                                <a href="#send_sms_template_modal" role="button" data-toggle="modal" class="pull-right">
                                    <span class="text-danger"><b>Use Template</b></span>
                                </a>
                            </label>
                            <textarea class="count_me form-control" name="message" id="message" class="bookId"
                                      required="" style="min-height: 120px;"></textarea>
                            <div class="row">
                                <div class="col-md-5"><span>CHECK YOUR SMS COUNT</span></div>
                                <div class="col-md-7">
                                    <div style="float: right"><span class="charleft contacts-count">&nbsp;</span><span
                                                class="parts-count"></span></div>
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            <label for="target"> Schedule SMS <span style="color: red;">*</span> </label><br>
                            <span>
							<input type="radio" class="ace send_now_checkbox" id="dynamic_send_now" name="schedule"
                                   onchange="hide_show_target_time('#campaign_sms_send')" value="1" checked="">
							<label class="lbl" for="dynamic_send_now">  Send Now  </label>
						</span> &nbsp;&nbsp;

                            <span>
							<input type="radio" class="ace send_later_checkbox" id="dynamic_send_later" name="schedule"
                                   onchange="hide_show_target_time('#campaign_sms_send')" value="2">
							<label class="lbl" for="dynamic_send_later"> Send Later </label>
						</span>
                        </div>

                        <div class="form-group target_time" id="target_time" style="display: none;">
                            <label for="target"> Target time </label>
                            <div class='input-group date' id='datetimepicker2'>
                                <input type="text" name="target_time" id="date-timepicker" type="text"
                                       class="form-control date-timepicker" placeholder="d-m-yyyyy">
                                <span class="input-group-addon">
								<span class="glyphicon glyphicon-calendar"></span>
							</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="c_tittle"> Campaign Title
                                {{--            <span style="color: red;"></span>--}}
                                @if($errors->has('campaign_title'))
                                    &nbsp;&nbsp;<span class="text-danger text-lg-left pull-right">{{$errors->first('campaign_title')}}</span>
                                @endif
                            </label>
                            <input type="text" id="c_title" value="" class="form-control input-sm" name="campaign_title" placeholder="Campaign Name">
                        </div>

                        <div class="clearfix form-group">
                            <input type="submit" class="btn btn-info" value="Send SMS">
                            &nbsp; &nbsp; &nbsp;
                            <button class="btn btn-danger" type="reset">
                                <i class="ace-icon fa fa-undo bigger-110"></i>
                                Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="background: #f9f9f9;">
                        <h3>Number format</h3>
                        <p>8801700000000</p>
                        <p>01700000000</p>
                        <p>1700000000</p>
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
                            <li> 160 Characters are counted as 1 SMS in case of English language & 70 in other language.
                            </li>
                            <li> One simple text message containing extended GSM character set (~^{}[]\|) is of 140
                                characters long. Check your SMS count before pushing SMS.
                            </li>
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
                                                       value="campaign_sms_send">
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
    </style>
@endsection

@section('custom_script')
    <script src="{{ asset('assets') }}/js/chosen.jquery.min.js"></script>
    <script src="{{ asset('assets') }}/js/jquery.textareaCounter.plugin.js"></script>
    <script src="{{ asset('assets') }}/js/text-area-counter.js"></script>
    <script src="{{ asset('assets') }}/js/moment.min.js"></script>
    <script src="{{ asset('assets') }}/js/bootstrap-datetimepicker.min.js"></script>

    <script src="{{ asset('assets') }}/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('assets') }}/js/jquery.dataTables.bootstrap.min.js"></script>

    <script type="text/javascript">
        $('#all_category').DataTable();

        $('.chosen-select').chosen({allow_single_deselect: true});
        $(document).ready(function () {
            count_textarea('#campaign_sms_send');
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


        /*sms send form submit*/
        function sms_send_form_submit(form_id, form_route) {
            $(function () {

                $('#'+form_id).on('submit', function (e) {
                    var formData = new FormData($('#'+form_id)[0]);
                    // formData.append("X-CSRF-Token", $('input[name="_token"]').val());

                    e.preventDefault();
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: 'post',
                        url: form_route,
                        data: formData,
                        dataType: 'JSON',
                        contentType: false,
                        processData: false,
                        beforeSend: function () {
                            $("#loading").show();
                        },
                        success: function (data) {

                            if (data.errors) {
                                $('.error_messages').empty();
                                $('.success_messages').empty();
                                $('.ajax_success').hide();
                                $('.ajax_error').show();
                                $.each(data.errors, function (key, value) {
                                    $('.error_messages').append('<p>' + value + '</p>');
                                });
                                $("html, body").animate({scrollTop: 0}, 1000);
                            }
                            if (data.success) {
                                $('.success_messages').empty();
                                $('.error_messages').empty();
                                $('.ajax_error').hide();
                                // $('#'+form_id)[0].reset();
                                $(".count_me.form-control").val('');
                                $('.ajax_success').show();
                                $('.success_messages').append('<p>' + data.success + '</p>');
                                $("html, body").animate({scrollTop: 0}, 1000);
                            }
                            if (data.error) {
                                $('.success_messages').empty();
                                $('.error_messages').empty();
                                $('.ajax_success').hide();
                                $('.ajax_error').show();
                                $('.error_messages').append('<p>' + data.error + '</p>');
                                $("html, body").animate({scrollTop: 0}, 1000);
                            }
                        },
                        complete: function () {
                            $("#loading").hide();
                        },
                    });

                });

            });
        }

        sms_send_form_submit('campaign_sms_send_form', '{{ route('user.sms.storeCampaignSms') }}');
    </script>
@endsection
