<form action="<?php echo e(route('user.sms.storeSingleSms')); ?>" method="post" id="single_sms_send" class="form-horizontal" role="form" enctype="multipart/form-data" style="margin-top: 10px;">
    <?php echo csrf_field(); ?>
    <div class="form-group">
        <label for="form-field-select-3"> Sender ID
            <span style="color: red;">*</span>
            <?php if($errors->has('sender_id')): ?>
                &nbsp;&nbsp;<span class="text-danger text-lg-left pull-right"><?php echo e($errors->first('sender_id')); ?></span>
            <?php endif; ?>
        </label>
        <br/>
        <select class="chosen-select form-control" id="form-field-select-3" data-placeholder="Select Sender ID.."
                name="sender_id" required="">
            <option value=""></option>
            <?php $__currentLoopData = Auth::user()->senderIds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $senderId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($senderId->sender->id); ?>" <?php if(old('sender_id')==true): ?> <?php echo e((@old('sender_id')==@$senderId->sender->id)? 'selected':''); ?><?php else: ?><?php echo e((@$defaultSenderId->sender_id==@$senderId->sender->id)? 'selected':''); ?><?php endif; ?>><?php echo e($senderId->sender->sir_sender_id); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <?php if($errors->has('sender_id')): ?>
            <span class="text-danger"><?php echo e($errors->sender_id); ?></span>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="cphone"> Cell phone <span style="color: red;">*</span>
            <?php if($errors->has('cell_phone')): ?>
                &nbsp;&nbsp;<span class="text-danger text-lg-left pull-right"><?php echo e($errors->first('cell_phone')); ?></span>
            <?php endif; ?>
        </label>
        <div id="send-sms-content" class="tab-pane fade">
            <textarea name="cell_phone" id="cphone" class="form-control"
                      placeholder="8801800000000&#10;8801700000000&#10;8801900000000&#10;8801600000000&#10;8801500000000"
                      style="min-height: 120px;" required=""><?php echo e(old('cell_phone')); ?></textarea>
        </div>
        <span class="text-danger">New line separated</span>
    </div>

    <div class="form-group">
        <label>Select SMS Type
            <span class="required"> * </span>
        </label>
        <div class="mt-radio-inline">
            <label class="mt-radio">
                <input type="radio" name="recipientsmsRadios" id="recipientsmsRadiosText" class="ace" value="text"
                       checked="">
                <span class="lbl"></span> Text
            </label> &nbsp;
            <label class="mt-radio">
                <input type="radio" name="recipientsmsRadios" id="recipientsmsRadiosFlash" class="ace" value="flash" disabled>
                <span class="lbl"></span> Flash
            </label> &nbsp;
            <label class="mt-radio">
                <input type="radio" name="recipientsmsRadios" id="recipientsmsRadiosUnicodeFlash" class="ace"
                       value="flashunicode" disabled>
                <span class="lbl"></span> Flash Unicode
            </label> &nbsp;
            <label class="mt-radio">
                <input type="radio" name="recipientsmsRadios" id="recipientsmsRadiosUnicode" class="ace"
                       value="unicode">
                <span class="lbl"></span> Unicode
            </label>
        </div>
    </div>
    <div class="form-group">
        <label for="message">Enter SMS Content
            <span class="required" style="color: red;"> * </span>
            &nbsp; <a href="#send_sms_template_modal" role="button" data-toggle="modal" class=""> <span
                        class="text-danger"><b>Use Template</b></span></a><a href="http://unicodeconverter.info/avro-type.php?pgn=2.1" class="" style="margin-left: 5px" target="_blank">(বাংলা লিখতে এখানে ক্লিক করুন)</a>
            <?php if($errors->has('message')): ?>
                &nbsp;&nbsp;<span class="text-danger text-lg-left pull-right"><?php echo e($errors->first('message')); ?></span>
            <?php endif; ?>
        </label>
        <textarea class="count_me form-control" name="message" id="message" required=""
                  style="min-height: 120px;"><?php echo e(old('message')); ?></textarea>

        <div class="row">
            <div class="col-md-5"><span>CHECK YOUR SMS COUNT</span><span style="color: red;"> (for masking unicode/bangla sms(max character=315))</span></div>
            <div class="col-md-7">
                <div style="float: right"><span class="charleft contacts-count">&nbsp;</span><span
                            class="parts-count"></span></div>
            </div>
        </div>

    </div>


    <div class="form-group">
        <label for="target"> Schedule SMS <span style="color: red;">*</span> </label><br>
        <span>
			<input type="radio" class="ace send_now_checkbox" name="schedule" id="send_now_checkbox" value="1"
                   onchange="hide_show_target_time('#send-sms-content')" <?php if(old('schedule') && old('schedule')=='1'): ?> checked <?php elseif(old('schedule')==false): ?> checked <?php endif; ?>>
			<label class="lbl" for="send_now_checkbox">  Send Now  </label>
		</span> &nbsp;&nbsp;
        <span>
			<input type="radio" class="ace send_later_checkbox" name="schedule" id="send_later_checkbox" value="2"
                   onchange="hide_show_target_time('#send-sms-content')"  <?php if(old('schedule') && old('schedule')=='2'): ?> checked <?php endif; ?>>
			<label class="lbl" for="send_later_checkbox"> Send Later </label>
		</span>
    </div>

    <div class="form-group target_time" id="target_time"   style="<?php if(old('schedule') && old('schedule')=='2'): ?> '' <?php else: ?> display: none; <?php endif; ?>" >
        <label for="target"> Target time </label>
        <?php if($errors->has('target_time')): ?>
            &nbsp;&nbsp;<span class="text-danger text-lg-left pull-right"><?php echo e($errors->first('target_time')); ?></span>
        <?php endif; ?>
        <div class='input-group date' id='datetimepicker2'>
            <input type="text" name="target_time" id="date-timepicker" class="form-control date-timepicker" placeholder="MM/DD/YYYY h:mm:ss" value="<?php echo e(old('target_time')); ?>">
            <span class="input-group-addon">
                <span class="glyphicon glyphicon-calendar"></span>
            </span>
        </div>
    </div>

    <div class="form-group">
        <label for="c_tittle"> Campaign Title
            
            <?php if($errors->has('campaign_title')): ?>
                &nbsp;&nbsp;<span class="text-danger text-lg-left pull-right"><?php echo e($errors->first('campaign_title')); ?></span>
            <?php endif; ?>
        </label>
        <input type="text" id="c_title" value="" class="form-control input-sm" name="campaign_title" placeholder="Campaign Name">
    </div>

    <div class="clearfix form-group" id="submit_group">
        
        <input type="submit" class="btn btn-info" value="Send SMS">
        &nbsp; &nbsp; &nbsp;
        <button class="btn btn-danger" type="reset">
            <i class="ace-icon fa fa-undo bigger-110"></i>
            Reset
        </button>
    </div>
</form>

