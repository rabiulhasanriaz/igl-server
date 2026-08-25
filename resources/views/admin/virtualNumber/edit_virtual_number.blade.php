@extends('admin.master')

@section('virtual_number_menu_class','open')
@section('virtual_number_list_menu_class', 'active')
@section('page_location')
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="{{ route('admin.index') }}">Dashboard</a>
        </li>
        <li>
            <a href="{{ route('admin.senderID.index') }}">Sender ID</a>
        </li>
        <li class="active">Virtual Number</li>
    </ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
    <h1>
        Virtual Number
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            Update
        </small>
    </h1>
@endsection

@section('main_content')

    <div class="space-6"></div>

    @include('admin.partials.session_messages')


    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-container">

                <form action="{{ route('admin.virtualNumber.update', $virtualNumber->id) }}" method="post" class="form-horizontal"
                      role="form">

                    @csrf
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 col-lg-offset-3 col-md-offset-3">
                        <!-- PAGE CONTENT BEGINS -->
                        <div class="form-group">
                            <label for="form-field-select-3"> Operator name </label>
                            <br/>
                            <select class="chosen-select form-control" id="form-field-select-3"
                                    data-placeholder="Operator name.." name="operator_id" required="">
                                <option value=""></option>
                                @foreach($operators as $operator)
                                    <option value="{{ $operator->id }}" {{ ($operator->id==$virtualNumber->operator_id) ? 'selected':'' }}> {{ $operator->ope_operator_name }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('operator_id'))
                                <span class="text-danger">{{ $errors->first('operator_id') }}</span>
                            @endif
                        </div>


                        <div class="form-group">
                            <label for="form-field-select-3">Virtual number</label>

                            <input type="text" name="virtual_number" value="{{ $virtualNumber->sivn_number }}"
                                   class="form-control"
                                   placeholder="Virtual number" maxlength="100" required="">
                            @if($errors->has('virtual_number'))
                                <span class="text-danger">{{ $errors->first('virtual_number') }}</span>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="form-field-select-3">Virtual number name</label>

                            <input type="text" name="virtual_number_name" value="{{ $virtualNumber->sivn_name}}"
                                   class="form-control"
                                   placeholder="Virtual number name" maxlength="100" required="">
                            @if($errors->has('virtual_number_name'))
                                <span class="text-danger">{{ $errors->first('virtual_number_name') }}</span>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="form-field-select-3">API User name</label>

                            <input type="text" name="api_username" value="{{ $virtualNumber->sivn_api_user_name }}"
                                   class="form-control" placeholder="Api user name"
                                   maxlength="100" required="">
                            @if($errors->has('api_username'))
                                <span class="text-danger">{{ $errors->first('api_username') }}</span>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="form-field-select-3">API Password</label>

                            <input type="text" name="api_password" value="{{ $virtualNumber->sivn_api_password }}"
                                   class="form-control" placeholder="Api password"
                                   maxlength="100" required="">
                            @if($errors->has('api_password'))
                                <span class="text-danger">{{ $errors->first('api_password') }}</span>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="form-field-select-3">Auto Load Amount</label>

                            <input type="text" name="auto_load_amount" value="{{ $virtualNumber->sivn_load_amount }}"
                                   class="form-control" placeholder="Enter Autometic load amount" required>
                            @if($errors->has('auto_load_amount'))
                                <span class="text-danger">{{ $errors->first('auto_load_amount') }}</span>
                            @endif
                        </div>

                        <div class="clearfix form-group">

                            <input type="submit" class="btn btn-info" value="Update">
                            &nbsp; &nbsp; &nbsp;
                            <button class="btn btn-danger" type="reset">
                                <i class="ace-icon fa fa-undo bigger-110"></i>
                                Reset
                            </button>
                        </div>
                    </div>

                </form>
            </div><!-- end bg-container-->
        </div>
    </div><!-- /.row -->
@endsection




@section('custom_style')
    <link rel="stylesheet" href="{{ asset('assets') }}/css/chosen.min.css"/>
@endsection

@section('custom_script')
    <script src="{{ asset('assets') }}/js/chosen.jquery.min.js"></script>
    <script type="text/javascript">
        $('.chosen-select').chosen({allow_single_deselect: true});
    </script>
@endsection
