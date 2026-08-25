@extends('admin.master')

@section('sender_id_menu_class','open')
@section('add_sender_id_menu_class', 'active')
@section('page_location')
<ul class="breadcrumb">
	<li>
		<i class="ace-icon fa fa-home home-icon"></i>
		<a href="{{ route('admin.index') }}">Dashboard</a>
	</li>
	<li class="active">Sender ID</li>
</ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
<h1>
	Sender ID
	<small>
		<i class="ace-icon fa fa-angle-double-right"></i>
		 Add
	</small>
</h1>
@endsection

@section('main_content')

<div class="space-6"></div>


<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-container">

		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 col-lg-offset-3 col-md-offset-3">
			<!-- PAGE CONTENT BEGINS -->
			@include('reseller.partials.session_messages')
			@include('reseller.partials.all_error_messages')
			<form action="{{ route('admin.senderID.create') }}" method="post" class="form-horizontal" role="form">
				@csrf
				<div class="form-group">
					<label for="non_masking"> Select non-masking  </label>
					<br />
					<select class="chosen-select form-control" id="non_masking" data-placeholder="Non-masking">
						<option value=""> </option>
                        @foreach($nonMaskingSenderIds as $nonMaskingSenderId)
                            <option value="{{ $nonMaskingSenderId->number }}">{{ $nonMaskingSenderId->number }}</option>
                        @endforeach

					</select>
				</div> 

				<div class="form-group">
					<label for="viewNonMasking">Add Sender ID</label>
					<input type="text" name="sender_id" value="{{ old('sender_id') }}" class="form-control" placeholder="Sender ID" maxlength="15" id="viewNonMasking" required>
                    <span class="duplicate-senderId text-danger"></span>
                    <span class="valid-senderId text-success"></span>
                    @if($errors->has('sender_id'))
                        <span class="text-danger retErrSenderId">{{ $errors->first('sender_id') }}</span>
                    @endif
				</div>
				<div class="form-group">
					<label for=""> Virtual number Robi  </label>
					<br />
					<select class="chosen-select form-control" id="form-field-select-3" data-placeholder="Robi" name="robi_virtual_number" >
						<option value="">  </option>
						@foreach($robiVirtualNumbers as $robiVirtualNumber)
							<option value="{{ $robiVirtualNumber->id }}">{{ $robiVirtualNumber->sivn_number }}</option>
						@endforeach
					</select>
				</div>
				
				
				<div class="form-group">
					<label for=""> Virtual number Teletalk  </label>
					<br />

					
					<select class="chosen-select col-sm-4" id="form-field-select-3" data-placeholder="Teletalk" name="teletalk_virtual_number">
						<option value="">  </option>
                        @foreach($teletalkVirtualNumbers as $teletalkVirtualNumber)
                            <option value="{{ $teletalkVirtualNumber->id }}">{{ $teletalkVirtualNumber->sivn_number }}</option>
                        @endforeach
					</select>

					<input type="" class="col-sm-4  pull-right" name="teletalk_user_name" placeholder="User Name">

					<input type="" class="col-sm-4  pull-right" name="teletalk_user_password" placeholder="Password">
					

				</div>
				
				<div class="form-group">
					<label for=""> Virtual number Grameen  </label>
					<br />
					<select class="chosen-select form-control" id="form-field-select-3" data-placeholder="Grameen" name="gp_virtual_number" >
						<option value="">  </option>
							@foreach($gpVirtualNumbers as $gpVirtualNumber)
                            <option value="{{ $gpVirtualNumber->id }}">{{ $gpVirtualNumber->sivn_number }}</option>
                            @endforeach
					</select>
				</div>
				
				<div class="form-group">
					<label for=""> Virtual number Airtel   </label>
					<br />
					<select class="chosen-select form-control" id="form-field-select-3" data-placeholder="Airtel " name="airtel_virtual_number" >
						<option value="">  </option>
                        @foreach($airtelVirtualNumbers as $airtelVirtualNumber)
                            <option value="{{ $airtelVirtualNumber->id }}">{{ $airtelVirtualNumber->sivn_number }}</option>
                        @endforeach
					</select>
				</div>
				
				<div class="form-group">
					<label for=""> Virtual number BanglaLink  </label>
					<br />
					<select class="chosen-select form-control" id="form-field-select-3" data-placeholder="BanglaLink" name="bl_virtual_number" >
						<option value="">  </option>
                        @foreach($blVirtualNumbers as $blVirtualNumber)
                            <option value="{{ $blVirtualNumber->id }}">{{ $blVirtualNumber->sivn_number }}</option>
                        @endforeach
					</select>
				</div>


				<div class="clearfix form-group" id = "create_btn">
				
					<input type="submit" class="btn btn-info" value="Submit">
					&nbsp; &nbsp; &nbsp;
					<button class="btn btn-danger" type="reset">
						<i class="ace-icon fa fa-undo bigger-110"></i>
						Reset
					</button>
																
				</div>

			</form>
		</div>
		
		
	</div><!-- /.col -->
</div><!-- /.row -->


@endsection




@section('custom_style')
	<link rel="stylesheet" href="{{ asset('assets') }}/css/chosen.min.css" />
@endsection

@section('custom_script')
	<script src="{{ asset('assets') }}/js/chosen.jquery.min.js"></script>
	<script type="text/javascript">
        $('.chosen-select').chosen({allow_single_deselect:true});
        $('#non_masking').change(function () {
            var nonMaskingValue = $('#non_masking').val();
            $('#viewNonMasking').val(nonMaskingValue);
            checkSenderIdExistence(nonMaskingValue);
        });
        $('#viewNonMasking').bind("keyup", function() {
            var nonMaskingVal = $('#viewNonMasking').val();
            checkSenderIdExistence(nonMaskingVal);
        });
	</script>
	@include('admin.ajax.check_sender_id_existence')
@endsection