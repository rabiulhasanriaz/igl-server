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
            <li class="active">Flexibook</li>
        </ul><!-- /.breadcrumb -->
    @endsection


    @section('page_header')
        <h1>
            Flexibook
            <small>
                <i class="ace-icon fa fa-angle-double-right"></i>
                Flexibook List
            </small>
        </h1>
    @endsection


    @section('main_content')

        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                {{--session and error messages--}}
                @include('admin.partials.all_error_messages')
                @include('admin.partials.session_messages')
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

                <a href="{{ asset('assets/bulkLoadDemo.xlsx') }}" role="button"
                   class="btn btn-info btn-sm pull-right">&nbsp; Download Excel Demo &nbsp;</a>
                <a href="#add_single_contact_modal" role="button" data-toggle="modal"
                   class="btn btn-danger btn-sm pull-right">&nbsp; Add Single Contact &nbsp;</a>
                <a href="#import_contact_modal" role="button" data-toggle="modal"
                   class="btn btn-success btn-sm pull-right">&nbsp; Import Contact &nbsp;</a>
                <a href="#add_new_category_modal" role="button" data-toggle="modal"
                   class="btn btn-primary btn-sm pull-right">&nbsp;
                    Add new Book &nbsp;</a>


                {{--contact group table section start--}}
                <div class="col-lg-12 col-md-12 col-sm-12 col-lg-12">

                    <table id="contact_group_table" class="table table-bordered">
                        <thead>
                        <tr>
                            <th>SL</th>
                            <th>Book name</th>
                            <th>Contact</th>
                            <th>Date</th>
                            <th>Action</th>
                            <th>System</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php($serial=1)
                        @foreach($flexibooks as $flexibook)
                            <tr>
                                <td>{{ $serial++ }}</td>
                                <td>{{ $flexibook->name }}</td>
                                <td id="count_total_number{{@$contact_group->id}}">
                                   {{ \App\Model\LoadFlexibooksData::where('load_flexibooks_id',$flexibook->id)->count() }}
                                </td>
                                <td>{{ $flexibook->created_at->format('Y-m-d') }}</td>
                                <td>
                                    <label>
                                        <a href="#edit_flexibook_modal" onclick="updateFlexibookModal(
                                            '{{ $flexibook->id }}',
                                            '{{ $flexibook->name }}'
                                        )" role="button" data-toggle="modal" class="btn-none-edit pass_id">
                                            Edit </a>
                                    </label>
                                    | <a href="{{ route('user.flexiload.deleteFlexibook', $flexibook->id) }}" class="btn-none-delete"
                                         onclick="return confirm('Are you sure you want to delete ?');"> Delete </a>
                                </td>

                                <td><a class="btn-none-details"
                                       href="{{ route('user.flexiload.flexibook_details', $flexibook->id) }}">Show</a>
                                       |<a class="btn-none-edit" onclick="loadBookModal(
                                       '{{$flexibook->id}}', 
                                        '{{ \App\Model\LoadFlexibook::book_price($flexibook->id) }}'
                                       )" data-toggle="modal" data-target="#flexibookLoadModal" style="cursor: pointer;">Load</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                {{--contact group table section end--}}


                {{-- start Flexibook load modal --}}
                    <!-- Modal -->
                    <div class="modal fade" id="flexibookLoadModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                      <div class="modal-dialog modal-sm" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                            <h5 class="modal-title" id="exampleModalLabel">Load to this Book</h5>
                          </div>
                            <form action="{{ route('user.flexiload.flexiload_book') }}" onsubmit="return checkForm(this);" method="post">
                              <div class="modal-body">
                                    @csrf
                                    <p class="text-primary text-center" id="info_total_price"></p>
                                    <input type="hidden" name="flexibook_id">
                                    <div class="form-group">
                                        <div class="checkbox">
                                          <label><input type="checkbox" id="customize_amount_checkbox" name="car">Customize Amount</label>
                                        </div>
                                        <input type="number" name="customize_amount" class="form-control" placeholder="00" title="Set a customized amount if you want to" min="10" max="1000" style="display: none;">
                                    </div>

                                    <div class="form-group">
                                        <label for="campaign_name">Campaing Name</label>
                                        <input type="text" name="campaign_name" class="form-control" placeholder="Campaign name">
                                    </div>

                                    <div class="form-group">
                                        <label for="flexipin">Flexipin</label>
                                        <input type="password" name="flexipin" class="form-control" placeholder="Your secret flexipin number" required>
                                    </div>
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" name="myButton" class="btn btn-primary">Load</button>
                              </div>
                            </div>
                            </form>
                      </div>
                    </div>
                {{-- End Flexibook load modal --}}


                {{--start add new group --}}
                <div class="col-lg-12 col-md-12 col-sm-12 col-lg-12" style="background: #f8f8f8;">
                    <form action="{{ route('user.flexiload.createFlexibook')  }}" method="post">
                        @csrf
                        <div id="add_new_category_modal" class="modal fade" tabindex="-1" style="display: none;">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×
                                        </button>
                                        <h3 class="smaller lighter blue no-margin text-primary"> Create a new book </h3>
                                    </div>
                                    <div class="modal-body">
                                        Book name :
                                        <input type="text" name="flexibook_name" class="form-control" id="" required="" placeholder="Book name">
                                        <br>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-sm btn-primary pull-right" type="submit">
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
                </div>
                {{--end add new group section--}}


                {{--start edit Flexibook --}}
                <div class="col-lg-12 col-md-12 col-sm-12 col-lg-12" style="background: #f8f8f8;">
                    <form action="{{ route('user.flexiload.updateFlexibook')  }}" method="post">
                        @csrf
                        <div id="edit_flexibook_modal" class="modal fade" tabindex="-1" style="display: none;">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×
                                        </button>
                                        <h3 class="smaller lighter blue no-margin text-primary"> Edit Flexibook </h3>
                                    </div>
                                    <div class="modal-body">
                                        Book name :
                                        <input type="text" name="book_name" class="form-control" id="book_name"
                                               required="">
                                        <input type="hidden" name="book_id" id="book_id" value="">
                                        <br>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-sm btn-primary pull-right" type="submit">
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
                </div>
                {{--end edit group section--}}






                <div class="col-lg-12 col-md-12 col-sm-12 col-lg-12" style="background: #f8f8f8;">
                        {{--start import contact section--}}
                            <form action="{{ route('user.flexiload.importContact') }}" method="post" id="flexibook_import_file_form" enctype="multipart/form-data">
                                @csrf
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
                                                        @foreach($flexibooks as $flexibook)
                                                            <option value="{{ $flexibook->id }}">{{ $flexibook->name }}</option>
                                                        @endforeach
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
                        {{--end import contact section--}}

















                        <!-- -----------Single  contact model- start--------- -->
                        <form action="{{ route('user.flexiload.storeSingleNumber') }}" method="post" id="" enctype="multipart/form-data">
                            @csrf
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
                                                       class="form-control input-sm" required="" placeholder="Name">
                                            </div>

                                            <div class="form-group">
                                                <label for="contact_number">Contact No. </label>
                                                <input type="text" name="contact_number" id="contact_number"
                                                       class="form-control input-sm" required="" placeholder="Contact No. ">
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
                                                <input type="text" name="remarks" id="remarks" class="form-control input-sm" placeholder="A short description">
                                            </div>
                                            
                                            <div class="form-group">
                                                <label>Books</label>
                                                <select name="book_id" class="chosen-select form-control"  required="">
                                                    <option value="">Nothing Selected</option>
                                                    @foreach($flexibooks as $flexibook)
                                                        <option value="{{ $flexibook->id }}">{{ $flexibook->name }}</option>
                                                    @endforeach
                                                </select>
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
                    </div>
                </div>
            </div><!-- /.col -->
        </div><!-- /.row -->

    @endsection


    @section('custom_style')
        <link rel="stylesheet" href="{{ asset('assets') }}/css/chosen.min.css" />
        <style>
            .chosen-container { width: 100% !important; }
            .tab-content { border: none !important; }
        </style>
        <link href="{{ asset('assets/datatable/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ asset('assets/datatable/rowReorder.dataTables.min.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ asset('assets/datatable/responsive.dataTables.min.css') }}" rel="stylesheet" type="text/css">
        <style>
            @media(max-width:575px){
                .abcd{
                    width: 130px;
                }
            }
            
            </style>
    @endsection


    @section('custom_script')
        {{-- <script src="{{ asset('assets') }}/js/jquery.dataTables.min.js"></script>
        <script src="{{ asset('assets') }}/js/jquery.dataTables.bootstrap.min.js"></script> --}}
        {{--<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
        <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/rowreorder/1.2.6/js/dataTables.rowReorder.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>--}}
        <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
        <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/rowreorder/1.2.8/js/dataTables.rowReorder.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
        <script type="text/javascript">
        // $('#reseller_list').DataTable();
        $(document).ready(function() {
        var table = $('#contact_group_table').DataTable( {
            responsive: true,
            columnDefs: [
                    { responsivePriority: 1, targets: 0 },
                    { responsivePriority: 2, targets: 1 },
                    { responsivePriority: 3, targets: 4 },
                    { responsivePriority: 4, targets: 2 },
                    { responsivePriority: 5, targets: 3 },
            ]
        } );
    } );
    </script>
        <script src="{{ asset('assets') }}/js/chosen.jquery.min.js"></script>
        <script src="{{ asset('assets') }}/js/ajax_import_contact.js"></script>
        <script src="{{ asset('assets') }}/js/ajax_send_sms.js"></script>


        <script type="text/javascript">
            function checkForm(form)
            {

                form.myButton.disabled = true;
                // form.myButton.value = "Please wait...";
                return true;
            }

            $('.chosen-select').chosen({allow_single_deselect:true});
            // $('#contact_group_table').DataTable();

            function updateFlexibookModal(id, name){
                $("#book_id").val(id);
                $("#book_name").val(name);
            }


            $('#click1').click(function () {
                valid_dynamic_flexiBook_file('flexibook_import_file_form', '{{ route('user.sms.checkDynamicFile') }}');
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

            function loadBookModal(book_id, total_price)
            {
                $("#info_total_price").text('Total Price : '+total_price+' Tk');
                $("#flexibookLoadModal input[name='flexibook_id']").val(book_id);
            }

            $('#customize_amount_checkbox').change(function() {
                  if(this.checked) {
                    $("input[name='customize_amount']").show(500);
                  }else{
                    $("input[name='customize_amount']").val(null);
                    $("input[name='customize_amount']").hide(500);
                  }
              });
            $("input[name='customize_amount']").keyup(function(){
               
            });
            // import_contact_form_submit('import_contact_form','{{ route('user.phonebook.importContact') }}', 'import_contact_modal');
        </script>
    @endsection
