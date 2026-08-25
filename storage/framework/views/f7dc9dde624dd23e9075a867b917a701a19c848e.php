<?php $__env->startSection('load_menu_class','open'); ?>
<?php $__env->startSection('menu_flexibook', 'active'); ?>
<?php $__env->startSection('menu_create_flexibook', 'active'); ?>
<?php $__env->startSection('page_location'); ?>
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="<?php echo e(route('user.index')); ?>">Dashboard</a>
        </li>
        <li class="active">Fleibook</li>
    </ul><!-- /.breadcrumb -->
<?php $__env->stopSection(); ?>


<?php $__env->startSection('page_header'); ?>
    <h1>
        <a href="<?php echo e(route('user.phonebook.index')); ?>">Flexibbok</a>
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            Book Name
        </small>
    </h1>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('main_content'); ?>

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="col-lg-12 col-md-12 col-sm-12 col-lg-12 padding">

                
                <?php echo $__env->make('admin.partials.all_error_messages', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                <?php echo $__env->make('admin.partials.session_messages', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
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

                <a href="#import_contact_modal" role="button" data-toggle="modal"
                   class="btn btn-primary btn-sm pull-right">&nbsp; Import Contact &nbsp;</a>
                <a href="#add_single_contact_modal" role="button" data-toggle="modal"
                   class="btn btn-danger btn-sm pull-right" id="updateButtonHide">&nbsp; Add Single Contact &nbsp;</a>
                
                
                <!-- Modal -->
                <div class="modal fade" id="sendLoadModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                        <h5 class="modal-title" id="exampleModalLabel">Load to this number</h5>
                      </div>
                      <div class="modal-body">
                        
                        <form action="<?php echo e(route('user.flexiload.flexiloadForm')); ?>" method="post">
                            <?php echo csrf_field(); ?>
                            

                            <input type="hidden" name="owner_name" value="">
                            <input type="hidden" name="number_type" value="">
                            <input type="text" hidden name="operator" value="">

                            <div class="form-group">
                                <label for="campaign_name">Campaign Name</label>
                                <input type="text" id="campaign_name" class="form-control input-sm" name="campaign_name" placeholder="Campaign name">
                            </div>

                            <div class="form-group">
                                <label class="" for="targeted_number">Mobile Number<span style="color: red">*</span></label>
                                <input type="text" id="targeted_number" name="targeted_number" class="form-control input-sm" required placeholder="01xxxxxxxxx" readonly>
                            </div>


                            <div class="form-group">
                                <label for="amount">Amount (Tk) <span style="color: red">*</span></label>
                                <input type="number" id="amount" name="amount" class="form-control" min="10" max="50000" required placeholder="00">
                            </div>

                            <div class="form-group">
                                <label class="" for="remarks">Remarks</label>
                                <input type="text" id="remarks" name="remarks" class="form-control input-sm" required placeholder="Short description">
                            </div>

                            <div class="form-group">
                                <label class="" for="flexipin">FlexiPin</label>
                                <input type="password" id="flexipin" name="flexipin" class="form-control input-sm" required placeholder="flexipin">
                            </div>

                            <div class="form-group">
                                <label></label>
                                <input type="submit" class="btn btn-primary btn-sm" name="" value="Submit">
                            </div>
                        </form>

                      </div>
                    </div>
                  </div>
                </div>
                 

                
                            <form action="<?php echo e(route('user.flexiload.importContact')); ?>" method="post" id="flexibook_import_file_form" enctype="multipart/form-data">
                                <?php echo csrf_field(); ?>
                                <!-- /.modal-dialog  start-->
                                <div id="import_contact_modal" class="modal fade" tabindex="-1" style="display: none;">
                                    <div class="modal-dialog modal-sm">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×
                                                </button>
                                                <h3 class="smaller lighter blue no-margin text-primary"> Import Contact </h3>
                                            </div>
                                            <div class="modal-body" style="">
                                                <div class="form-group">
                                                <label for="cphone"> Select File ( <strong style="color: red;">xlsx file only</strong> )</label>
                                                    <input type="file" name="sms_file" required=""/>
                                                </div>
                                                
                                                <div class="clearfix"></div>

                                                <div class="form-group">
                                                    <label for="flexibook_id"> Select a Book <span style="color: red;">*</span> </label>
                                                    <br/>
                                                    <select class="chosen-selecta form-control" id="flexibook_id" data-placeholder="Select Sender ID.." name="flexibook_id" required="">
                                                        <option value="<?php echo e($flexibook_id); ?>"><?php echo e($flexibook_name); ?></option>
                                                    </select>
                                                </div>

                                               <div class="form-group">
                                                    <label for="flexipin">Your Secret Flexipin number<span style="color: red;">*</span></label>
                                                    <input type="password" name="flexipin" class="form-control form-control-sm" placeholder="your secret flexipin number">
                                                </div>

                                                
                                            </div>

                                            <div class="modal-footer">
                                                <div class="fomr-group">
                                                  <button type="submit" class="button-success pull-right">Import</button>  
                                                </div>
                                            </div>
                                        </div><!-- /.modal-content -->
                                    </div><!-- /.modal-dialog -->
                                </div>
                            </form>
                        </div>
                        


                <!-- -----------Single  contact model- start--------- -->
                <!-- -----------Single  contact model- start--------- -->
                    <form action="<?php echo e(route('user.flexiload.storeSingleNumber')); ?>" method="post">
                        <?php echo csrf_field(); ?>
                        <div id="add_single_contact_modal" class="modal fade" tabindex="-1" style="display: none;">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×
                                        </button>
                                        <h3 class="smaller lighter blue no-margin text-primary"> Add Contact </h3>
                                    </div>
                                    <div class="modal-body">
                                         <div class="form-group">
                                            <label for="contact_name">Name </label>
                                            <input type="text" name="contact_name" id="contact_name"
                                                   class="form-control input-sm" required="" placeholder="Contact holder name">
                                        </div>

                                        <div class="form-group">
                                            <label for="contact_number">Contact No. </label>
                                            <input type="text" name="contact_number" id="contact_number"
                                                   class="form-control input-sm" required="" placeholder="01xxxxxxxxx">
                                        </div>

                                        <div class="form-group">
                                                <label for="edit_operator">Operator </label>
                                                <select name="operator" id="edit_operator" class="form-control">
                                                    <option value="">Select One</option>
                                                    <option value="gp">Grameen</option>
                                                    <option value="blink">Banglalink</option>
                                                    <option value="airtel">Airtel</option>
                                                    <option value="robi">Robi</option>
                                                    <option value="teletalk">Teletalk</option>
                                                    <option value="gpst">GP Skitto</option>
                                                </select>
                                            </div>

                                        <div class="form-group">
                                            <label for="amount">Initial Amount. </label>
                                            <input type="number" name="amount" id="amount" min="10" max="50000"
                                                   class="form-control input-sm" required="" placeholder="00">
                                        </div>

                                        <div class="form-group">
                                            <label>Number Type</label><br>
                                            <label>
                                                <input type="radio" class="ace" name="number_type" value="1"
                                                       required="" checked="">
                                                <span class="lbl">  Prepaid </span>
                                            </label>
                                            <label>
                                                <input type="radio" class="ace" name="number_type" value="2"
                                                       required="">
                                                <span class="lbl"> Postpaid </span>
                                            </label>
                                        </div>

                                        <div class="form-group">
                                            <label for="remarks">Remarks</label>
                                            <input type="text" name="remarks" id="remarks" class="form-control input-sm" placeholder="A short remarks">
                                        </div>
                                        
                                        <div class="form-group">
                                            <input type="hidden" name="book_id" value="<?php echo e($flexibook_id); ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>Status</label><br>
                                            <label>
                                                <input type="radio" class="ace" name="contactStatus" value="1"
                                                       required="" checked="">
                                                <span class="lbl">  Active </span>
                                            </label>
                                            <label>
                                                <input type="radio" class="ace" name="contactStatus" value="2"
                                                       required="">
                                                <span class="lbl"> Inactive </span>
                                            </label>
                                        </div>
                                        <br>
                                    </div>
                                    <div class="modal-footer">
                                        <button id="button_SingleDisNone" class="btn btn-sm btn-primary pull-right"
                                                name="single_Contact_submit">
                                            <i class="fa-check-square-o fa fa-times"></i>Submit
                                        </button> &nbsp;&nbsp;
                                        <button class="btn btn-sm btn-danger pull-right" data-dismiss="modal">
                                            <i class="ace-icon fa fa-times"></i>Close
                                        </button>
                                    </div>
                                </div><!-- /.modal-content -->
                                <div id="aside-inside-modal"
                                     class="modal aside aside-contained aside-bottom aside-hz aside-dark aside-hidden no-backdrop"
                                     data-placement="bottom" data-background="true" data-backdrop="false" tabindex="-1">
                                </div>
                            </div><!-- /.modal-dialog -->
                        </div>
                    </form>
                <!-- -----------Single  contact model- end --------- -->
                

                <!-- -----------Single contact edit model start--------- -->
                <form action="<?php echo e(route('user.flexiload.updateContact')); ?>" method="post">
                    <?php echo csrf_field(); ?>
                    <div id="edit_single_contact_modal" class="modal fade" tabindex="-1" style="display: none;">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×
                                    </button>
                                    <h3 class="smaller lighter blue no-margin text-primary"> Edit Contact </h3>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="edit_contact_number">Contact No </label>
                                        <input type="text" name="contact_number" id="edit_contact_number"
                                               class="form-control" required="">
                                    </div>

                                    <div class="form-group">
                                        <label for="edit_operator">Operator </label>
                                        <select name="operator" id="edit_operator" class="form-control">
                                            <option value="">Select One</option>
                                            <option value="gp">Grameen</option>
                                            <option value="blink">Banglalink</option>
                                            <option value="airtel">Airtel</option>
                                            <option value="robi">Robi</option>
                                            <option value="teletalk">Teletalk</option>
                                            <option value="gpst">GP Skitto</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="edit_contact_name">Name </label>
                                        <input type="text" name="contact_name" id="edit_contact_name" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="edit_designation">Amount </label>
                                        <input type="number" name="amount" id="edit_amount" class="form-control" min="10" max="50000">
                                    </div>

                                    <div class="form-group">
                                        <label for="edit_remarks">Remarks</label>
                                        <input type="text" name="remarks" id="edit_remarks" class="form-control">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Status</label><br>
                                        <label>
                                            <input type="radio" class="ace" name="contactStatus" value="1"
                                                   required="" checked="">
                                            <span class="lbl">  Active </span>
                                        </label>
                                        <label>
                                            <input type="radio" class="ace" name="contactStatus" value="2"
                                                   required="">
                                            <span class="lbl"> Inactive </span>
                                        </label>
                                    </div>
                                    <br>
                                </div>
                                <div class="modal-footer">
                                    <input type="hidden" name="contact_id" id="edit_contact_id">
                                    <button id="button_SingleDisNone" class="btn btn-sm btn-primary pull-right">
                                        <i class="fa-check-square-o fa fa-times"></i>Update
                                    </button> &nbsp;&nbsp;
                                    <button class="btn btn-sm btn-danger pull-right" data-dismiss="modal">
                                        <i class="ace-icon fa fa-times"></i>Close
                                    </button>
                                </div>
                            </div><!-- /.modal-content -->
                            <div id="aside-inside-modal"
                                 class="modal aside aside-contained aside-bottom aside-hz aside-dark aside-hidden no-backdrop"
                                 data-placement="bottom" data-background="true" data-backdrop="false" tabindex="-1">
                            </div>
                        </div><!-- /.modal-dialog -->
                    </div>
                </form>
                <!-- -----------Single contact edit model end --------- -->

            </div>

            <div class="col-lg-12 col-md-12 col-sm-12 col-lg-12 padding" style="min-height: 550px;">
                <table id="contact_details_table" class="table table-striped table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>SL</th>
                        <th>Name</th>
                        <th>Contact</th>
                        <th>Operator</th>
                        <th>Amount</th>
                        <th>Number type</th>
                        <th>Remarks</th>
                        <th> Status</th>
                        <th> Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php ($serial=1); ?>
                    <?php $__currentLoopData = $contacts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($serial++); ?></td>
                            <td id="name_<?php echo e($contact->id); ?>"><?php echo e($contact->name); ?></td>
                            <td id="phone_number_<?php echo e($contact->id); ?>"><?php echo e($contact->number); ?></td>
                            <td id="mobile_operator_<?php echo e($contact->id); ?>">
                                <?php if($contact->operator == 1): ?>
                                airtel
                                <?php elseif($contact->operator == 2): ?>
                                blink
                                <?php elseif($contact->operator == 3): ?>
                                gp
                                <?php elseif($contact->operator == 4): ?>
                                robi
                                <?php elseif($contact->operator == 5): ?>
                                teletalk
                                <?php else: ?>
                                <?php echo e($contact->operator); ?>

                                <?php endif; ?>
                            </td>
                            <td id="amount_<?php echo e($contact->id); ?>"><?php echo e($contact->amount); ?></td>
                            <td id="number_type_<?php echo e($contact->id); ?>">
                                <?php echo e($contact->number_type == 1?'Prepaid':'Postpaid'); ?>

                            </td>
                            <td id="remarks_<?php echo e($contact->id); ?>"><?php echo e($contact->remarks); ?></td>
                            
                            <td>
                            <?php if($contact->status=='1'): ?>
                            <style>
                                input[type=checkbox].ace.ace-switch.ace-switch-4:checked+.lbl::before, input[type=checkbox].ace.ace-switch.ace-switch-5:checked+.lbl::before {
                                    background-color: #25af56;
                                }
                            </style>
                            
                            <?php else: ?>
                            <style>
                                input[type=checkbox].ace.ace-switch.ace-switch-4+.lbl::before, input[type=checkbox].ace.ace-switch.ace-switch-5+.lbl::before {
                                    background-color: red;
                                    border: 1px solid #ce2b42;
                                    
                                }
                            </style>
                            <?php endif; ?>

                            
                            <input name="switch-field-1" id="contactID" <?php echo e(($contact->status=='1')?'checked' : ''); ?> onchange="updateStatus('<?php echo e($contact->id); ?>')" value="<?php echo e($contact->id); ?>" class="ace ace-switch ace-switch-5" type="checkbox" />
                                    <span class="lbl"></span>
                            </td>
                            <td>
                                <label>
                                    <a href="#edit_single_contact_modal" onclick="edit_contact_details('<?php echo e($contact->id); ?>')" role="button" data-toggle="modal"
                                       class="serialNumberId btn-none-edit"> Edit </a>
                                </label>
                                <label>
                                    | <a href="<?php echo e(route('user.flexiload.deleteContact', ['contact_id'=>$contact->id])); ?>" class="btn-none-delete"
                                         onclick="return confirm('Are you sure you want to delete ?');"> Delete </a>
                                </label>
                                <label>
                                    |

                                    <a href="javascript:void(0);" class="btn-none-info" data-toggle="<?php echo e(($contact->status==1)? "modal":""); ?>"  data-target="#sendLoadModal" onclick="getDataAndRiseModal(
                                    '<?php echo e($contact->name); ?>',
                                    '<?php echo e($contact->number); ?>', 
                                    '<?php echo e($contact->amount); ?>',
                                    '<?php echo e($contact->number_type); ?>',
                                    '<?php echo e($contact->remarks); ?>',
                                    '<?php echo e($contact->operator); ?>')"> Load </a>

                                </label>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div><!-- /.col -->
    </div><!-- /.row -->

<?php $__env->stopSection(); ?>


<?php $__env->startSection('custom_style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/css/chosen.min.css" />
    <link href="<?php echo e(asset('assets/datatable/jquery.dataTables.min.css')); ?>" rel="stylesheet" type="text/css">
    <link href="<?php echo e(asset('assets/datatable/rowReorder.dataTables.min.css')); ?>" rel="stylesheet" type="text/css">
    <link href="<?php echo e(asset('assets/datatable/responsive.dataTables.min.css')); ?>" rel="stylesheet" type="text/css">
    <style>
        @media(max-width:575px){
            .abcd{
                width: 130px;
            }
        }
        
        </style>
    <style>
        .chosen-container { width: 100% !important; }
        .tab-content { border: none !important; }
    </style>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('custom_script'); ?>
   <!--  <script src="<?php echo e(asset('assets')); ?>/js/jquery.dataTables.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/js/jquery.dataTables.bootstrap.min.js"></script> -->
    
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
        <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/rowreorder/1.2.8/js/dataTables.rowReorder.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript">
        // $('#reseller_list').DataTable();
        $(document).ready(function() {
        var table = $('#contact_details_table').DataTable( {
            responsive: true,
            columnDefs: [
                    { responsivePriority: 1, targets: 0 },
                    { responsivePriority: 2, targets: 1 },
                    { responsivePriority: 3, targets: 2 },
                    { responsivePriority: 4, targets: 8 },
                    { responsivePriority: 5, targets: 3 },
                    { responsivePriority: 6, targets: 4},
                    { responsivePriority: 7, targets: 5 },
                    { responsivePriority: 8, targets: 6 },
            ]
        } );
    } );
    </script>
    <script src="<?php echo e(asset('assets')); ?>/js/chosen.jquery.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/js/ajax_import_contact.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/js/ajax_send_sms.js"></script>

    <script type="text/javascript">
        $(".group_id").val();
        $('.chosen-select').chosen({allow_single_deselect:true});
        // $('#contact_details_table').DataTable();

        function edit_contact_details(id) {
            var phoneNumber = $("#phone_number_"+id).html();
            var mobileOperator = $("#mobile_operator_"+id).html();
            var contactName = $("#name_"+id).html();
            var amount = $("#amount_"+id).html();
            var number_type = $("#number_type_"+id).html();
            var remarks = $("#remarks_"+id).html();

            $("#edit_contact_number").val(phoneNumber);
            $("#edit_contact_name").val(contactName);
            $("#edit_operator option[value="+ $.trim(mobileOperator) +"]").attr("selected",true);
            $("#edit_amount").val(amount);
            $("#edit_amount").val(amount);
            $("#edit_remarks").val(remarks);
            $("#edit_contact_id").val(id);

        }

        $('#click1').click(function () {
            valid_dynamic_flexiBook_file('flexibook_import_file_form', '<?php echo e(route('user.sms.checkDynamicFile')); ?>');
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

        import_contact_form_submit('import_contact_form','<?php echo e(route('user.phonebook.importContact')); ?>', 'import_contact_modal');

        function getDataAndRiseModal(owner_name, number, amount, number_type, remarks,operator)
        {
            $("#sendLoadModal input[name='owner_name'").val(owner_name);
            $("#sendLoadModal input[name='targeted_number'").val(number);
            $("#sendLoadModal input[name='number_type'").val(number_type);
            $("#sendLoadModal input[name='operator'").val(operator);

            $("#sendLoadModal input[name='amount'").val(amount);
            $("#sendLoadModal input[name='remarks'").val(remarks);

        }
    </script>

    <script>
        $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

   
    
    
     function updateStatus(statusValue){
  
       $.ajax({
        url:"<?php echo e(route('user.flexiload.number-status')); ?>",
        method:"POST",
        data: {statusValue:statusValue},
        
        success:function(data)
        {
         
        }
       });
      
     }
    

    
        
    </script>


<?php $__env->stopSection(); ?>

<?php echo $__env->make('user.master', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>