@extends('user.master')

@section('messaging_menu_class','open')
@section('templates_menu_class','active')
@section('page_location')
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="{{ route('user.index') }}">Dashboard</a>
        </li>
        <li class="active">SMS Templates</li>
    </ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
    <h1>
        SMS Templates
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            List
        </small>
    </h1>
@endsection


@section('main_content')

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

            @include('user.partials.session_messages')
            @include('user.partials.all_error_messages')

            <a href="#my-modal" role="button" data-toggle="modal" class="btn btn-primary btn-sm pull-right">
                &nbsp; Add New Templates&nbsp;
            </a>
            <div class="tab-pane fade active in" id="recipient">
                <form action="{{ route('user.template.store') }}" method="post">
                    @csrf
                    <div id="my-modal" class="modal fade" tabindex="-1" style="display: none;">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×
                                    </button>
                                    <h3 class="smaller lighter blue no-margin text-primary"> New Template </h3>
                                </div>

                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="templateTitle">Template Name </label>
                                        <input type="text" name="tmp_name" id="templateTitle" class="form-control"
                                               required="">
                                    </div>
                                    <div class="form-group">
                                        <label>Select SMS Type
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="mt-radio-inline">
                                            <label class="mt-radio">
                                                <input type="radio" name="recipientsmsRadios"
                                                       id="recipientsmsRadiosText" class="ace" value="text" checked="">
                                                <span class="lbl"></span> Text
                                            </label> &nbsp;
                                            <label class="mt-radio">
                                                <input type="radio" name="recipientsmsRadios"
                                                       id="recipientsmsRadiosFlash" class="ace" value="flash">
                                                <span class="lbl"></span> Flash
                                            </label> &nbsp;
                                            <label class="mt-radio">
                                                <input type="radio" name="recipientsmsRadios"
                                                       id="recipientsmsRadiosUnicodeFlash" class="ace"
                                                       value="flashunicode">
                                                <span class="lbl"></span> Flash Unicode
                                            </label> &nbsp;
                                            <label class="mt-radio">
                                                <input type="radio" name="recipientsmsRadios"
                                                       id="recipientsmsRadiosUnicode" class="ace" value="unicode">
                                                <span class="lbl"></span> Unicode
                                            </label>

                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="templateMessage">Enter SMS Content
                                            <span class="required" style="color: red;"> * </span>
                                        </label>
                                        <textarea class="count_me form-control" name="tmp_message"
                                                  id="templateMessage" required=""
                                                  style="min-height: 120px;"></textarea>
                                        <div class="row">
                                            <div class="col-md-4"><span>CHECK YOUR SMS COUNT</span></div>
                                            <div class="col-md-8">
                                                <div style="float: right">
                                                    <span class="charleft contacts-count">&nbsp;</span><span
                                                            class="parts-count"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button id="button_DisNone" type="submit" class="btn btn-sm btn-primary pull-right">
                                        <i class="fa-check-square-o fa fa-times"></i>
                                        Save changes
                                    </button> &nbsp;&nbsp;
                                    <button class="btn btn-sm btn-danger pull-right" data-dismiss="modal">
                                        <i class="ace-icon fa fa-times"></i>
                                        Close
                                    </button>
                                </div>
                            </div><!-- /.modal-content -->
                            <div id="aside-inside-modal"
                                 class="modal aside aside-contained aside-bottom aside-hz aside-dark aside-hidden no-backdrop"
                                 data-placement="bottom" data-background="true" data-backdrop="false"
                                 tabindex="-1"></div>
                        </div><!-- /.modal-dialog -->
                    </div>
                </form>
            </div>

            <div class="tab-pane fade active in" id="recipientEdit">
                <form action="{{ route('user.template.update') }}" method="post">
                    @csrf
                    <div id="my-modal-edit" class="modal fade" tabindex="-1" style="display: none;">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×
                                    </button>
                                    <h3 class="smaller lighter blue no-margin text-primary"> Edit Template </h3>
                                </div>

                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="editTemplateTitle">Template Name </label>
                                        <input type="text" name="tmp_name" id="editTemplateTitle" class="form-control"
                                               required="">
                                    </div>
                                    <div class="form-group">
                                        <label>Select SMS Type
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="mt-radio-inline">
                                            <label class="mt-radio">
                                                <input type="radio" name="recipientsmsRadios"
                                                       id="recipientsmsRadiosText" class="ace" value="text">
                                                <span class="lbl"></span> Text
                                            </label> &nbsp;
                                            <label class="mt-radio">
                                                <input type="radio" name="recipientsmsRadios"
                                                       id="recipientsmsRadiosFlash" class="ace" value="flash">
                                                <span class="lbl"></span> Flash
                                            </label> &nbsp;
                                            <label class="mt-radio">
                                                <input type="radio" name="recipientsmsRadios"
                                                       id="recipientsmsRadiosUnicodeFlash" class="ace"
                                                       value="flashunicode">
                                                <span class="lbl"></span> Flash Unicode
                                            </label> &nbsp;
                                            <label class="mt-radio">
                                                <input type="radio" name="recipientsmsRadios"
                                                       id="recipientsmsRadiosUnicode" class="ace" value="unicode">
                                                <span class="lbl"></span> Unicode
                                            </label>

                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="editTemplateMessage">Enter SMS Content
                                            <span class="required" style="color: red;"> * </span>
                                        </label>
                                        <textarea class="count_me form-control" name="tmp_message"
                                                  id="editTemplateMessage" required=""
                                                  style="min-height: 120px;"></textarea>
                                        <div class="row">
                                            <div class="col-md-4"><span>CHECK YOUR SMS COUNT</span></div>
                                            <div class="col-md-8">
                                                <div style="float: right">
                                                    <span class="charleft contacts-count">&nbsp;</span><span
                                                            class="parts-count"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="template_id" id="editTemplateId" value="">
                                <div class="modal-footer">
                                    <button id="update_template_btn" class="btn btn-sm btn-primary pull-right">
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
                                 data-placement="bottom" data-background="true" data-backdrop="false"
                                 tabindex="-1"></div>
                        </div><!-- /.modal-dialog -->
                    </div>
                </form>
            </div>
        </div><!-- /.col -->

        <div class="col-lg-12 col-md-12 col-sm-12 col-lg-12">
            <table class="table table-bordered" id="template-table">
                <thead>
                <tr>
                    <th>SL</th>
                    <th class="col-md-2">Template title</th>
                    <th class="col-md-7">Template Contain</th>
                    <th> Total message</th>
                    <th> Action</th>
                </tr>
                </thead>

                <tbody>
                @php($serial=1)
                @foreach($templates as $template)
                    <tr class="d-flex">
                        <td>{{ $serial++ }}</td>
                        <td id="template_name_{{ $template->id }}">{{ $template->st_name }}</td>
                        <td id="template_text_{{ $template->id }}">{{ $template->st_content }}</td>
                        <td>{{ $template->st_total_sms }}</td>
                        <td>
                            <label>
                                <input type="hidden" id="template_id_{{$template->id}}" value="{{ $template->id }}">
                                <a href="#my-modal-edit" onclick="getTemplate({{ $template->id }})" role="button"
                                   data-toggle="modal" class="btn-none-edit pass_id">
                                    Edit </a>
                            </label>
                            | <a href="{{ route('user.template.delete', $template->id) }}" class="btn-none-delete"
                                 onclick="return confirm('Are you sure you want to delete ?');"> Delete </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>

            </table>
        </div>
    </div><!-- /.row -->

@endsection
@section('custom_style')
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
    <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/rowreorder/1.2.6/js/dataTables.rowReorder.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript">
        // $('#reseller_list').DataTable();
        $(document).ready(function() {
        var table = $('#template-table').DataTable( {
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

    <script src="{{ asset('assets') }}/js/jquery.textareaCounter.plugin.js"></script>
    <script src="{{ asset('assets') }}/js/text-area-counter.js"></script>

    <!--suppress LocalVariableNamingConventionJS -->
    <script type="text/javascript">
        $(document).ready(function () {

            // $('#template-table').DataTable();
            count_textarea('#recipient');
            count_textarea('#recipientEdit');
        });

        function getTemplate(val) {
            var tmp_title = $("#template_name_" + val).html();
            var tmp_text = $("#template_text_" + val).html();
            var tmp_id = $("#template_id_" + val).val();
            $("#editTemplateTitle").val(tmp_title);
            $("#editTemplateMessage").val(tmp_text);
            $("#editTemplateId").val(tmp_id);
        }


    </script>



@endsection
