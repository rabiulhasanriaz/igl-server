<table class="table table-striped table-bordered table-hover">
	<thead>
		<tr>
			<th style="text-align: center;">Sl</th>
			<th style="text-align: center;">User Name</th>
			<th style="text-align: center;">Date & Time</th>
			<th style="text-align: center;">Mobile Number</th>
			<th style="text-align: center;">Package Name</th>
			<th style="text-align: center;">Trx</th>
			<th style="text-align: center;">Remarks</th>
			<th style="text-align: center;">Amount</th>
		</tr>
	</thead>

	<tbody>
		
		@foreach($allData as $data )
			<tr>
				<td style="text-align: center;">{{ $loop->iteration }}</td>
				<td>{{ $data->owner_name ?? 'N/A' }}</td>
				<td style="text-align: center;">{{ $data->created_at }}</td>
				<td style="text-align: center;">{{ $data->targeted_number }}</td>
				<td>
					@if($data->package_id == 0)
						Single Load
					@else
						{{ $data->package_info['package_name'] }}
					@endif
				</td>
				<td>{{ $data->transaction_id }}</td>
				<td>{{ $data->remarks }}</td>
				<td style="text-align: right;">{{ $data->campaign_price }}</td>
			</tr>
		@endforeach
	</tbody>

	<tfoot>
		
	</tfoot>
</table>