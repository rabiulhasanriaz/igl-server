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
			height: 50px; 
		}
	    #footer .page:after { content: counter(page, upper-roman); }
	</style>
</head>
<body>
	<div id="header">
			<div class="logo">
				<img src="{{asset('assets/uploads/default.png')}}" alt="" style="height: 70px;">
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
	<h3 style="text-align: center; margin-top: -50px;">SMS Details of {{ Auth::user()->company_name }}</h3>
	<table >
		<thead>
			<tr>
				<th style="text-align: center;">Sl</th>
				<th style="text-align: center;">Pay. Reference</th>
				<th style="text-align: center;">Transaction Date</th>
				<th style="text-align: center;">Pay. Mode</th>
				<th style="text-align: center;">Credit</th>
			</tr>
		</thead>

        
		<tbody>
			@php
            $total = 0;
            $sub_total = 0;
            @endphp
            
			@foreach( $stat as $data )
                <tr>
                    <td style="text-align: center; font-size: 13px;">{{ $loop->iteration }}</td>
                    <td style="text-align: center; font-size: 13px;">{{ $data->asb_pay_ref }}</td>
                    <td style="text-align: center; font-size: 13px;">{{ $data->created_at }}</td>
                    <td style="text-align: center; font-size: 13px;">
                        @if ($data->asb_pay_mode == 1)
                                Cash
                        @elseif($data->asb_pay_mode == 2)
                                Bank
                        @elseif($data->asb_pay_mode == 3)
                                Check
                        @else
                        		Others
                        @endif
                    </td>
                    <td style="text-align: right; font-size: 13px;">{{ number_format($data->asb_credit,2) }}</td>
                </tr>
                @php
                    $total = $total + $data->asb_credit;
                @endphp
                
            @endforeach
				
				<tr>
                    <td colspan="2">In Word:- {{ \OtherHelpers::number_to_text($total) }}</td>
                    <td style="text-align: right;">Total:</td>
                    <td style="text-align: right;" colspan="2">{{ number_format($total,2) }}</td>
                </tr>
		</tbody>
        
				
	

		
	</table>
	

</body>
</html>
