<table class="table table-striped table-bordered table-hover" id="view_archived_report">
	<thead>
		<tr>
			<th style="text-align: center;">Sl</th>
			<th style="text-align: center;">User Name</th>
			<th style="text-align: center;">Date & Time</th>
			<th style="text-align: center;">Mobile Number</th>
			<th style="text-align: center;">Package Name</th>
			<th style="text-align: center;">Transaction Id</th>
			<th style="text-align: center;">Remarks</th>
			<th style="text-align: center;">Amount</th>
		</tr>
	</thead>

	<tbody>
		@php($sl=0)
		@foreach($allData as $data )
			<tr>
				<td style="text-align: center;">{{ ++$sl }}</td>
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
		@foreach($all as $data )
		
			<tr>
				<td style="text-align: center;">{{ ++$sl }}</td>
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
	
				<td class="text-center">Pending</td>
				<td>{{ $data->remarks }}</td>
				<td style="text-align: right;">{{ $data->campaign_price }}</td>
			</tr>	
			
		@endforeach
			
	</tbody>

	<tfoot>

	</tfoot>
</table>

<link href="{{ asset('assets/datatable/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('assets/datatable/rowReorder.dataTables.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('assets/datatable/responsive.dataTables.min.css') }}" rel="stylesheet" type="text/css">


<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/rowreorder/1.2.6/js/dataTables.rowReorder.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
<script type="text/javascript">
	// $('#reseller_list').DataTable();
	$(document).ready(function() {
	var table = $('#view_archived_report').DataTable( {
		responsive: true,
	} );
} );
</script>

