@extends('admin.master')

@section('phone_book_menu_class','open')
@section('category_contact_menu_class', 'active')

@section('page_location')
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="{{ route('admin.index') }}">Dashboard</a>
        </li>
        <li class="active">Category Contact</li>
    </ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
    <h1>
        Category Contact
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            List
        </small>
    </h1>
@endsection

@section('main_content')

    <div class="space-6"></div>


    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-container">
            <div class="col-lg-12 col-md-12 col-sm-12 col-lg-12 padding">

                @include('admin.partials.all_error_messages')
                @include('admin.partials.session_messages')


                <a href="#modal_add_singe_category_contact" role="button" data-toggle="modal"
                   class="btn btn-danger btn-sm pull-right">
                    &nbsp; Add Single Contact &nbsp;
                </a>
                <a href="#modal_import_category_contact_from_file" role="button" data-toggle="modal"
                   class="btn btn-success btn-sm pull-right">
                    &nbsp; Import Contact &nbsp;
                </a>
                <a href="#modal_add_category" role="button" data-toggle="modal"
                   class="btn btn-primary btn-sm pull-right">
                    &nbsp; Add new category &nbsp;
                </a>
            </div>

            <div class="col-lg-12 col-md-12 col-sm-12 col-lg-12 padding">
                <table id="category_contact_table_list" class="table table-bordered">
                    <thead>
                    <tr>
                        <th>SL</th>
                        <th>Category name</th>
                        <th> Contact</th>
                        <th> Date</th>
                        <th> Action</th>
                        <th> System</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php($serial=1)
                    @foreach($categories as $category)
                        <tr>
                            <td>{{ $serial++ }}</td>
                            <td>{{ $category->name }}</td>
                            <td>{{ $category->CampaignContacts->count() }}</td>
                            <td>{{ $category->created_at->format('j M,Y') }}</td>
                            <td>
                                <label>
                                    <a href="#modal_update_category" onclick="get_category_name({{$category->id}})" role="button" data-toggle="modal"
                                       class="btn-none-edit pass_id"> Edit </a>
                                </label>
                                |
                                <a href="{{ route('admin.categoryContact.deleteCategory', $category->id) }}"
                                   class="btn-none-delete"
                                   onclick="return confirm('Are you sure you want to delete ?');"> Delete </a>
                            </td>
                            <td><a class="btn-none-details" href="{{ route('admin.categoryContact.show', $category->slug) }}">Show</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>

                </table>
            </div><!-- /.col -->

            <div class="col-lg-12 col-md-12 col-sm-12 col-lg-12">
                <form action="{{ route('admin.categoryContact.storeCategory') }}" method="post">
                    @csrf
                    <div id="modal_add_category" class="modal fade" tabindex="-1" style="display: none;">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×
                                    </button>
                                    <h3 class="smaller lighter blue no-margin text-primary"> Add Category </h3>
                                </div>

                                <div class="modal-body">
                                    Category name :
                                    <input type="text" name="category_name" class="form-control" required>
                                    <br>
                                </div>

                                <div class="modal-footer">
                                    <button id="button_DisNone" class="btn btn-sm btn-primary pull-right">
                                        <i class="fa-check-square-o fa fa-times"></i>
                                        Submit
                                    </button> &nbsp;&nbsp;

                                    <button class="btn btn-sm btn-danger pull-right" data-dismiss="modal">
                                        <i class="ace-icon fa fa-times"></i>
                                        Close
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
                <!-- end add category modal -->


                    <form action="{{ route('admin.categoryContact.updateCategory') }}" method="post">
                    @csrf
                    <div id="modal_update_category" class="modal fade" tabindex="-1" style="display: none;">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×
                                    </button>
                                    <h3 class="smaller lighter blue no-margin text-primary"> Edit Category </h3>
                                </div>
                                <input type="hidden" name="group_id" id="ContactCategoryId">
                                <div class="modal-body">
                                    Category name :
                                    <input type="text" name="category_name" class="form-control" id="edit_category_name" required>
                                    <br>
                                </div>

                                <div class="modal-footer">
                                    <button id="button_DisNone" class="btn btn-sm btn-primary pull-right">
                                        <i class="fa-check-square-o fa fa-times"></i>
                                        Update
                                    </button> &nbsp;&nbsp;

                                    <button class="btn btn-sm btn-danger pull-right" data-dismiss="modal">
                                        <i class="ace-icon fa fa-times"></i>
                                        Close
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
                <!-- end add category modal -->


                {{--start modal section--}}
                <div class="col-lg-12 col-md-12 col-sm-12 col-lg-12 padding">
                    <form action="{{ route('admin.categoryContact.importContact') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <!-- /.modal-dialog  start-->
                        <div id="modal_import_category_contact_from_file" class="modal fade" tabindex="-1" style="display: none;">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×
                                        </button>
                                        <h3 class="smaller lighter blue no-margin text-primary"> Import Contact </h3>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label>Groups</label>
                                            <select name="group_id" class="form-control" required="">
                                                <option value="">Nothing Selected</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" {{ ($category->id==@$category_id)? 'selected':'' }}> {{ $category->name }} </option>
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
                                        <button class="btn btn-sm btn-primary pull-right">
                                            <i class="fa-check-square-o fa fa-times"></i>
                                            Submit
                                        </button> &nbsp;&nbsp;
                                        <button class="btn btn-sm btn-danger pull-right" data-dismiss="modal">
                                            <i class="ace-icon fa fa-times"></i>
                                            Close
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

                    <!-- -----------end- file import modal-------- -->


                    <!-- -----------Signle  contact model- start--------- -->
                    <form action="{{ route('admin.categoryContact.storeContact') }}" method="post">
                        @csrf
                        <div id="modal_add_singe_category_contact" class="modal fade" tabindex="-1" style="display: none;">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×
                                        </button>
                                        <h3 class="smaller lighter blue no-margin text-primary"> Add Contact </h3>
                                    </div>

                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="">Contact No </label>
                                            <input type="text" name="contact_number" class="form-control" maxlength="13"
                                                   required="">
                                        </div>
                                        <div class="form-group">
                                            <label for="">Name </label>
                                            <input type="text" name="contact_name" class="form-control"
                                                   id="CategorySingle_Person">
                                        </div>
                                        <div class="form-group">
                                            <label for="">Designation </label>
                                            <input type="text" name="designation" class="form-control"
                                                   id="CategoryDesignation">
                                        </div>
                                        <div class="form-group">
                                            <label>Category</label>
                                            <select name="category_id" class="form-control" required="">
                                                <option value="">Nothing Selected</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" {{ ($category->id==@$category_id)? 'selected':'' }}> {{ $category->name }} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Status</label><br>
                                            <label>
                                                <input type="radio" class="ace" name="contactStatus" value="1" required=""
                                                       checked="">
                                                <span class="lbl">  Enabled </span>
                                            </label>
                                            <label>
                                                <input type="radio" class="ace" name="contactStatus" value="0" required="">
                                                <span class="lbl"> Disabled </span>
                                            </label>
                                        </div>
                                        <br>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-sm btn-primary pull-right">
                                            <i class="fa-check-square-o fa fa-times"></i>
                                            Submit
                                        </button> &nbsp;&nbsp;
                                        <button class="btn btn-sm btn-danger pull-right" data-dismiss="modal">
                                            <i class="ace-icon fa fa-times"></i>
                                            Close
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
                    <!-- -----------Signle  contact model- end --------- -->


                <!-- -----------Signle  contact model- end --------- -->
            </div>
        </div><!-- /.col -->
    </div>

@endsection


@section('custom_script')
    <script src="{{ asset('assets') }}/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('assets') }}/js/jquery.dataTables.bootstrap.min.js"></script>
    <script type="text/javascript">
        $('#category_contact_table_list').DataTable();
    </script>
    @include('admin.ajax.get_category_name_for_edit')
@endsection
