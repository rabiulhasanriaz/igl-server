@extends('user.master')

@section('phone_book_menu_class','open')
@section('contact_and_group_menu_class','active')
@section('page_location')
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="{{ route('user.index') }}">Dashboard</a>
        </li>
        <li class="active">Phonebook</li>
    </ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
    <h1>
        <a href="{{ route('user.phonebook.index') }}">Phonebook</a>
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            Group Name
        </small>
    </h1>
@endsection


@section('main_content')

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="col-lg-12 col-md-12 col-sm-12 col-lg-12 padding">

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

                <a href="#import_contact_modal" role="button" data-toggle="modal"
                   class="btn btn-primary btn-sm pull-right">&nbsp; Import Contact &nbsp;</a>
                <a href="#add_single_contact_modal" role="button" data-toggle="modal"
                   class="btn btn-danger btn-sm pull-right" id="updateButtonHide">&nbsp; Add Single Contact &nbsp;</a>

                {{--start import contact section--}}
                <form action="{{ route('user.phonebook.importContact') }}" id="import_contact_form" method="post" enctype="multipart/form-data">
                @csrf
                <!-- /.modal-dialog  start-->
                    <div id="import_contact_modal" class="modal fade" tabindex="-1" style="display: none;">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×
                                    </button>
                                    <h3 class="smaller lighter blue no-margin text-primary"> Import Contact </h3>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="group_id">Groups</label>
                                        <select name="group_id" id="group_id" class="chosen-select form-control group_id" required="">
                                            <option value="">Nothing Selected</option>
                                            @foreach($contact_groups as $contact_group)
                                                <option value="{{ $contact_group->id }}"> {{ $contact_group->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Import Contact <span class="text-primary">(only .xls, .xlsx, .csv, .txt file accept)</span></label>
                                        <input type="file" name="contact_file" accept=".xls, .xlsx, .csv, .txt" required="">
                                    </div>
                                    <br>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-sm btn-primary pull-right"
                                            name="group_fileUpload_submit">
                                        <i class="fa-check-square-o fa fa-times"></i>Submit
                                    </button> &nbsp;&nbsp;
                                    <button class="btn btn-sm btn-danger pull-right modal_close" data-dismiss="modal">
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
            {{--start import contact section--}}


                <!-- -----------Single  contact model- start--------- -->
                <form action="{{ route('user.phonebook.storeContact') }}" method="post">
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
                                        <label for="contact_number">Contact No </label>
                                        <input type="text" name="contact_number" id="contact_number"
                                               class="form-control" required="">
                                    </div>
                                    <div class="form-group">
                                        <label for="">Name </label>
                                        <input type="text" name="contact_name" id="valuePass" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="">Designation </label>
                                        <input type="text" name="designation" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>Groups</label>
                                        <select name="category_id" class="chosen-select form-control group_id"  required="">
                                            <option value="">Nothing Selected</option>
                                            @foreach($contact_groups as $contact_group)
                                                <option value="{{ $contact_group->id }}">{{ $contact_group->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Status</label><br>
                                        <label>
                                            <input type="radio" class="ace" name="contactStatus" value="1"
                                                   required="" checked="">
                                            <span class="lbl">  Enabled </span>
                                        </label>
                                        <label>
                                            <input type="radio" class="ace" name="contactStatus" value="2"
                                                   required="">
                                            <span class="lbl"> Disabled </span>
                                        </label>
                                    </div>
                                    <br>
                                </div>
                                <div class="modal-footer">
                                    <button id="button_SingleDisNone" class="btn btn-sm btn-primary pull-right">
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
                <form action="{{ route('user.phonebook.updateContact') }}" method="post">
                    @csrf
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
                                        <label for="edit_contact_name">Name </label>
                                        <input type="text" name="contact_name" id="edit_contact_name" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="edit_designation">Designation </label>
                                        <input type="text" name="designation" id="edit_designation" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label >Groups</label>
                                        <select name="category_id" class="chosen-select form-control group_id"  required="">
                                            <option value="">Nothing Selected</option>
                                            @foreach($contact_groups as $contact_group)
                                                <option value="{{ $contact_group->id }}">{{ $contact_group->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Status</label><br>
                                        <label>
                                            <input type="radio" class="ace" name="contactStatus" value="1"
                                                   required="" checked="">
                                            <span class="lbl">  Enabled </span>
                                        </label>
                                        <label>
                                            <input type="radio" class="ace" name="contactStatus" value="2"
                                                   required="">
                                            <span class="lbl"> Disabled </span>
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
                        <th>Contact</th>
                        <th>Name</th>
                        <th>Designation</th>
                        <th>Group</th>
                        <th> Status</th>
                        <th> Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php($serial=1)
                    @php($contacts=\App\Model\PhonebookContact::where('category_id', $group_id)->orderBy('name','asc')->get())
                    @foreach($contacts as $contact)
                        <tr>
                            <td>{{ $serial++ }}</td>
                            <td id="phone_number_{{ $contact->id }}">{{ $contact->phone_number }}</td>
                            <td id="name_{{ $contact->id }}">{{ $contact->name }}</td>
                            <td id="designation_{{ $contact->id }}">{{ $contact->designation }}</td>
                            <td>{{ $group_name }}</td>
                            @if($contact->status=='1')
                            <td style="color: #1bcb00">Active</td>
                            @else
                                <td style="color: #cb0021">InActive</td>
                            @endif
                            <td>
                                <label>
                                    <a href="#edit_single_contact_modal" onclick="get_contact_details({{$contact->id}})" role="button" data-toggle="modal"
                                       class="serialNumberId btn-none-edit"> Edit </a>
                                </label>
                                <label>
                                    | <a href="{{ route('user.phonebook.deleteContact', $contact->id) }}" class="btn-none-delete"
                                         onclick="return confirm('Are you sure you want to delete ?');"> Delete </a>
                                </label>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
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
@endsection


@section('custom_script')
    <script src="{{ asset('assets') }}/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('assets') }}/js/jquery.dataTables.bootstrap.min.js"></script>
    <script src="{{ asset('assets') }}/js/chosen.jquery.min.js"></script>
    <script src="{{ asset('assets') }}/js/ajax_import_contact.js"></script>

    <script type="text/javascript">
        $(".group_id").val({{$group_id}});
        $('.chosen-select').chosen({allow_single_deselect:true});
        $('#contact_details_table').DataTable();

        function get_contact_details(id) {
            var phoneNumber = $("#phone_number_"+id).html();
            var contactName = $("#name_"+id).html();
            var designation = $("#designation_"+id).html();

            $("#edit_contact_number").val(phoneNumber);
            $("#edit_contact_name").val(contactName);
            $("#edit_designation").val(designation);
            $("#edit_contact_id").val(id);

        }

        import_contact_form_submit('import_contact_form','{{ route('user.phonebook.importContact') }}', 'import_contact_modal');
    </script>


@endsection
