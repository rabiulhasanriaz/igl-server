<script type="text/javascript">
	function get_employee_payable_balance(customerId) {
	    var cId = customerId;
	    $.ajaxSetup({
	        headers: {
	            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	        }
	    });

	    $.ajax({
	        type: 'POST',
	        url: "{{ url('/ajax/checkEmployeeAvailableBalance') }}",
	        data: {cId: cId},
	        success: function (data) {
	            var result = Number(data).toFixed(2);

// If balance is less than 1000 imployee is not eligible to get paymrnt
	            if ( result < 1000 ){
		            $("#CustomerBalance").html("<b>Employee Payable Balance : </b><span id='balanceOfCustomer' class='badge badge-success text-bold'>"+result+" tk.</span> <span> ( balance less than 1000 not eligible to get payment )</span>");
		            $("#employee_pay_amount").attr('max', result);
	            	$('input[type="submit"]').addClass('disabled');
	            }else{
		            $("#CustomerBalance").html("<b>Employee Payable Balance : </b><span id='balanceOfCustomer' class='badge badge-success text-bold'>"+result+" tk.</span>");
		            $("#employee_pay_amount").attr('max', result);
	            }
	        },
	        error: function(data) {
	        	alert('Something wrong');
	        }
	    });
	}

</script>