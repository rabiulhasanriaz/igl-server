<!DOCTYPE html>
<html>
<head>
	<title></title>
	<style type="text/css">
		table{
			border-collapse: collapse;
			width: 100%;
		}
		table, th, td{
			border: 1px solid lightgray;
		}
		th, td{
			padding: 3px;
		}

		@page { margin: 150px 50px; } 

	    #header { 
			position: fixed; 
			left: 0px; 
			top: -120px; 
			right: 0px; 
			height: 150px; 
		    text-align: center; 
		}
		#footer { 
			position: fixed; 
			left: 0px; 
			bottom: -100px; 
			right: 0px; 
			height: 70px; 
		}
	    #footer .page:after { content: counter(page, upper-roman); }
	</style>
</head>
<body>
	<div id="header">
			<div class="logo">
				<img src="{{ OtherHelpers::website_logo() }}" alt="">
			</div>
			<div id="footer">
				<p style="font-size: 20px;">We Are Authorized Aggregator of: 
					<img src="{{ asset('assets/images/operator_icon') }}/airtel.png" style="width: 20px; height: 20px; margin-right: 5px;">
					<img src="{{ asset('assets/images/operator_icon') }}/banglalink.png" style="width: 20px; height: 20px; margin-right: 5px;">
					<img src="{{ asset('assets/images/operator_icon') }}/gp.png" style="width: 20px; height: 20px; margin-right: 5px;">
					<img src="{{ asset('assets/images/operator_icon') }}/robi.png" style="width: 20px; height: 20px; margin-right: 5px;">
					<img src="{{ asset('assets/images/operator_icon') }}/teletalk.png" style="width: 20px; height: 20px;">
				</p>
			</div>
	</div>
	<h3 style="text-align: center;">Flexiload Bill Payment Details</h3>
	<table>
		<thead>
			<tr>
				<th style="text-align: center;">Sl</th>
				<th style="text-align: center;">Date & Time</th>
				<th style="text-align: center;">User Name</th>
				<th style="text-align: center;">Number</th>
				<th style="text-align: center;">Transaction ID</th>
				<th style="text-align: center;">Remarks</th>
				<th style="text-align: center;">Amount</th>
			</tr>
		</thead>

		<tbody>
			@php($total = 0)
			@foreach( $allData as $data )
				<tr>
					<td style="text-align: center; font-size: 13px;">{{ $loop->iteration }}</td>
					<td style="text-align: center; font-size: 13px;">{{ $data->created_at }}</td>
					<td style="text-align: center; font-size: 13px;">{{ $data->owner_name }}</td>
					<td style="text-align: center; font-size: 13px;">{{ $data->targeted_number }}</td>
						<td style="text-align: center; font-size: 13px;">{{ $data->transaction_id }}</td>
					<td style="text-align: center; font-size: 13px;">{{ $data->remarks }}</td>
					<td style="text-align: right; font-size: 13px;">{{ $data->campaign_price }}</td>
				</tr>
				{{ $total +=  $data->campaign_price }}
			@endforeach
		</tbody>

		<tfoot>
			<tr>
				<td colspan="5" style="text-align: right;">Total:</td>
				<td style="text-align: right;">{{ number_format($total,2) }}</td>
			</tr>
			<tr>
				<td colspan="6" style="text-align: left;">In Word:- {{ \OtherHelpers::number_to_text($total) }}</td>
			</tr>
		</tfoot>
	</table>
	

</body>
</html>
