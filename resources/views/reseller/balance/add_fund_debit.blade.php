@extends('reseller.master')

@section('add_fund_debit_menu_class','active')
@section('acc_details_menu_class','open')

@section('page_location')
	<ul class="breadcrumb">
		<li>
			<i class="ace-icon fa fa-home home-icon"></i>
			<a href="{{ route('reseller.index') }}">Dashboard</a>
		</li>
		<li class="active">Price Sms</li>
	</ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
	<h1>
		Price & Coverage
		<small>
			<i class="ace-icon fa fa-angle-double-right"></i>
			Price List
		</small>
	</h1>
@endsection

@section('main_content')

<div class="space-6"></div>

<div class="row bg-container">
	<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
		{{--  --}}
	</div>
	<div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
		@include('reseller.partials.all_error_messages')
		@include('reseller.partials.session_messages')

		<form action="{{ route('reseller.balance.debit.store') }}" method="post" class="form-horizontal" role="form">
			@csrf
			<div class="form-group">
				<label for="form-field-select-3" style="font-size: 20px;">Company Name :</label>
				<br />
				<select class="chosen-select form-control" id="form-field-select-3" data-placeholder="Select User" name="user_id" required="" onchange="user_balance(this.value)">
					<option value="">  </option>
					@foreach($resellers as $reseller)
						<option value="{{ $reseller->id }}"> {{ $reseller->company_name }}- ( {{ $reseller->cellphone }}
							)
						</option>
					@endforeach
				</select>
			</div>

			<div class="form-group">
				<label for="credit" style="font-size: 20px;">Debit amount :<span class="text-success" id="CustomerBalance"></span></label>
				<input type="text" name="debit_amount" id="credit" onkeyup="show_terget_time(this.value)" value="" class="form-control input-mask-numberTk" placeholder="00.00" maxlength="10" required style="font-size: 20px;">
			</div>

			<div class="form-group">
				<label for="payReference" style="font-size: 20px;">Payment Reference :</label>

				<input style="font-size: 20px;" type="text" name="payment_reference" id="payReference" value="" class="form-control" placeholder="Reference" maxlength="32" required>
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
	<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12" style="margin-left: 20px;">
		<div class="row" style="margin-left: 20px;">
			
				<div id="transaction-history">
					<!-- Transaction history will be displayed here -->
				</div>

		</div>
	</div>
</div>


@endsection

@section('custom_style')
	<link rel="stylesheet" href="{{ asset('assets') }}/css/chosen.min.css"/>
@endsection

@section('custom_script')
	<script src="{{ asset('assets') }}/js/chosen.jquery.min.js"></script>
	<script type="text/javascript">
	
        $('.chosen-select').chosen({allow_single_deselect: true});

		// ===
		$('#form-field-select-3').on('change', function() {
			var userId = $(this).val();

			if (userId) {
				$.ajax({
					type: 'GET',
					url: "{{ route('reseller.transaction.history') }}",
					data: {
						userId: userId
					},
					success: function(data) {
						console.log('AJAX Response:', data);

						$('#transaction-history').html(data);
					}
				});
			} else {
				$('#transaction-history').empty();
			}
		});
		// ===

        function show_terget_time(value) {

            var max_ammount = $("#balanceOfCustomer").text();
            max_ammount = parseFloat(max_ammount);
            if(value<0){
                $("#submitBtn").hide();
            }
            else if(value>max_ammount){
                $("#submitBtn").hide();
            }
            else{
                $("#submitBtn").show();
            }
        }
	</script>
	@include('admin.ajax.check_customer_available_balance')
@endsection
