<form action="{{ route('user.dynamic-sms.storeDynamicSmsModem') }}" id="dynamic_sms_send" method="post" class="form-horizontal"
      role="form" enctype="multipart/form-data">
    @csrf
    

    <div class="form-group">
        <label>Select SMS Type
            <span class="required"> * </span>
        </label>
        <div class="mt-radio-inline">
            <label class="mt-radio">
                <input type="radio" id="recipientsmsRadiosText" name="recipientsmsRadios" class="ace" value="text"
                       checked="">
                <span class="lbl"></span> Text
            </label> &nbsp;

            <label class="mt-radio">
                <input type="radio" id="recipientsmsRadiosFlash" name="recipientsmsRadios" class="ace" value="flash"
                       disabled>
                <span class="lbl"></span> Flash
            </label> &nbsp;

            <label class="mt-radio">
                <input type="radio" id="recipientsmsRadiosUnicodeFlash" name="recipientsmsRadios" class="ace"
                       value="flashunicode" disabled>
                <span class="lbl"></span> Flash Unicode
            </label> &nbsp;

            <label class="mt-radio">
                <input type="radio" id="recipientsmsRadiosUnicode" name="recipientsmsRadios" class="ace"
                       value="unicode">
                <span class="lbl"></span> Unicode
            </label>
        </div>
    </div>

    <div class="form-group">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding">
            <label for="cphone"> Select File ( <strong style="color: red;">xlsx file only</strong> )- Must select
                Unicode if message contains Bangla language </label>
            <input type="file" id="id-input-file-2a" class=""
                   style="width: 100%; height: 35px;border: 1px solid lightgray; border-radius: 3px;" name="sms_file"
                   required=""/>
            <a href="javascript:;" style="position: relative;bottom: 40px;left: 390px;" id="click1"
               class="btn btn-primary">Upload</a>
        </div>
    </div>

    <div class="dynamic_msg" style="display: none;">
        <div class="form-group">
            <label for="dynamic_number_column"> Select Your Number Column <span style="color: red;">*</span> </label>
            <br/>
            <select class="chosen-selecta form-control" id="dynamic_number_column" data-placeholder="Select Sender ID.."
                    name="dynamic_number_column" required="">

            </select>
        </div>

        <br><br>
        <div class="form-group">
            <label for="message">Enter SMS Content
                <span class="required" style="color: red;"> * </span>
                &nbsp; <a href="#send_sms_template_modal" role="button" data-toggle="modal" class=""> <span
                        class="text-danger"><b>Use Template</b></span></a>
            </label>


            <div class="text-right pull-right" id="columnNames">

            </div>


            <textarea class="count_me form-control" name="message" id="message" required=""
                      style="min-height: 120px;"></textarea>

            <div class="row">
                <div class="col-md-5"><span>CHECK YOUR SMS COUNT</span><span style="color: red;"> (for masking unicode/bangla sms(max character=315))</span>
                </div>
                <div class="col-md-7">
                    <div style="float: right"><span class="charleft contacts-count">&nbsp;</span><span
                            class="parts-count"></span></div>
                </div>
            </div>
        </div>

    </div>


    <div class="form-group">
        <label for="target"> Schedule SMS <span style="color: red;">*</span> </label><br>
        <span>
			<input type="radio" class="ace send_now_checkbox" id="dynamic_send_now_checkbox" name="schedule"
                   onchange="hide_show_target_time('#dynamic-sms-content')" value="1" checked="">
			<label class="lbl" for="dynamic_send_now_checkbox">  Send Now  </label>
		</span> &nbsp;&nbsp;

        <span>
			<input type="radio" class="ace send_later_checkbox" id="dynamic_send_later_checkbox" name="schedule"
                   onchange="hide_show_target_time('#dynamic-sms-content')" value="2">
			<label class="lbl" for="dynamic_send_later_checkbox"> Send Later </label>
		</span>
    </div>

    <div class="form-group target_time" id="target_time" style="display:none;">
        <label for="target"> Target time </label>
        <div class='input-group date' id='datetimepicker2'>
            <input type="text" name="target_time" id="date-timepicker" class="form-control date-timepicker"
                   placeholder="m-d-yyyyy">
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
        <input type="submit" class="btn btn-info" value="Send SMS" onclick="checkUploadedFile()">
        &nbsp; &nbsp; &nbsp;
        <button class="btn btn-danger" type="reset">
            <i class="ace-icon fa fa-undo bigger-110"></i>
            Reset
        </button>
    </div>
</form>


