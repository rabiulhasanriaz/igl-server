@extends('admin.master')

@section('account_menu_class','open')
@section('add_fund_debit_menu_class', 'active')
@section('page_location')
<ul class="breadcrumb">
	<li>
		<i class="ace-icon fa fa-home home-icon"></i>
		<a href="{{ route('admin.index') }}">Dashboard</a>
	</li>
	<li>
		<a href="#">Balance</a>
	</li>
	<li class="active">Debit</li>
</ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
<h1>
	Debit
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

		@include('admin.partials.all_error_messages')
		@include('admin.partials.session_messages')
			<!-- PAGE CONTENT BEGINS -->
			<form action="{{ route('admin.balance.debit.store') }}" method="post" class="form-horizontal" role="form">
				@csrf
				<div class="form-group">
					<label for="form-field-select-3"> Company name </label>
					<br />
					<select class="chosen-select form-control" id="form-field-select-3" data-placeholder="Company name.." name="user_id" required="" onchange="customer_balance(this.value)">
						<option value="">  </option>
						@foreach($resellers as $reseller)
							<option value="{{ $reseller->id }}"> {{ $reseller->company_name }}- ( {{ $reseller->cellphone }}
								)
							</option>
						@endforeach
					</select>
				</div>

				<div class="form-group">
					<label for="credit">Debit amount <span class="text-success" id="CustomerBalance"></span></label>
					<input type="text" name="debit_amount" id="credit" onkeyup="show_terget_time(this.value)" value="" class="form-control input-mask-numberTk" placeholder="00.00" maxlength="10" required>
				</div>

				<div class="form-group">
					<label for="payReference"> Payment referance :</label>

					<input type="text" name="payment_reference" id="payReference" value="" class="form-control" placeholder="......" maxlength="32" required>
				</div>
		
				<div class="clearfix form-group" id="submit_btn_debit">
						<input type="submit" class="btn btn-info" value="Submit" id="submitBtn" >
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
	<link rel="stylesheet" href="{{ asset('assets') }}/css/chosen.min.css"/>
@endsection

@section('custom_script')
	<script src="{{ asset('assets') }}/js/chosen.jquery.min.js"></script>
	<script type="text/javascript">
		$('.chosen-select').chosen({allow_single_deselect: true});

        function show_terget_time(value) {

            var max_ammount = $("#balanceOfCustomer").text();
            max_ammount = parseFloat(max_ammount);
            if(value>max_ammount){
                $("#submitBtn").hide();
			}
			else{
			    $("#submitBtn").show();
			}
        }
	</script>
	@include('admin.ajax.check_customer_available_balance')
@endsection