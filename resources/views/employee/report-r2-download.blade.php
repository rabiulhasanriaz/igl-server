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
	<h3 style="text-align: center; margin-top: -50px;">SMS Details of {{ App\Model\User::company_name(request()->user)->company_name }}</h3>
	<table >
		<thead>
			<tr>
				<th style="text-align: center; width: 20px; padding:0px; font-size: 10px;">Sl</th>
				<th style="text-align: center; width: 80px; padding:0px; font-size: 10px;">C. Name</th>
				<th style="text-align: center; width: 90px; padding:0px; font-size: 10px;">Date & Time</th>
				<th style="text-align: center; padding:0px; font-size: 10px;">Content</th>
				<th style="text-align: center; width: 40px; padding:0px; font-size: 10px;">Qty</th>
				<th style="text-align: center; width: 40px; padding:0px; font-size: 10px;">Price</th>
			</tr>
		</thead>

        
		<tbody>
			@php
            $total = 0;
            $sub_total = 0;
			$total_sms = 0;
            @endphp
            
			@foreach( $route2transactions as $date => $data )
            
				<tr>
					<td style="text-align: center; font-size: 10px; padding:0px;" colspan="6">{{ $date }}</td>
				</tr>
                @foreach ($data as $item)
                
                <tr>
                    <td style="text-align: center; font-size: 10px; padding:0px;">{{ $loop->iteration }}</td>
                    <td style="text-align: center; font-size: 10px; padding:0px;">{{ $item->sdci_campaign_title }}</td>
                    <td style="text-align: center; font-size: 10px; padding:0px;">{{ $item->sdci_targeted_time }}</td>
					<td style="font-size: 10px; padding:0px;">
						@if(count($item->campaignData) > 0)
                            {!! $item->campaignData->first()->sd_message !!}
                    	@endif
					</td>
					<td style="text-align: center; font-size: 10px; padding:0px;">{{ $item->sdci_total_submitted }}</td>
                    <td style="text-align: right; font-size: 10px; padding:0px;">{{ number_format($item->sdci_total_cost,2) }}</td>
                </tr>
                @php
                    $total += $item->sdci_total_cost;
                    $total_sms += $item->sdci_total_submitted;
                @endphp
				
                
				
                @endforeach
                
                
                
            @endforeach
				
				<tr>
                    <td colspan="4" style="font-size: 10px;">In Word:- {{ \OtherHelpers::number_to_text($total) }}</td>
                    <td style="text-align: right; font-size: 10px;">{{ number_format($total_sms,2) }}</td>
                    <td style="text-align: right; font-size: 10px;">{{ number_format($total,2) }}</td>
                </tr>
		</tbody>
        
				
	

		
	</table>
	

</body>
</html>
