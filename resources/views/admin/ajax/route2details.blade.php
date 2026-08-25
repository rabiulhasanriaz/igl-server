<table class="table table-striped table-bordered table-hover" id="details">
    <thead>
    <tr>
        <th>SL</th>
        <th>Campaign Title</th>
        <th>Campaign ID</th>
        <th>Submitted Date and Time</th>
        <th>Total Submitted</th>
        <th>Total Cost</th>
    </tr>
    </thead>
    <tbody>
    @php
    $sl=0;
    $total_submitted = 0;
    $total_cost = 0;
    @endphp
    @foreach ($details as $detail)
    <tr>
        <td>{{ ++$sl }}</td>
        <td class="text-center">{{ $detail->sdci_campaign_title }}</td>
        <td>{{ $detail->sdci_campaign_id }}</td>
        <td>{{ $detail->created_at }}</td>
        <td class="text-right">{{ number_format($detail->sdci_total_submitted,2) }}</td>
        <td class="text-right">{{ number_format($detail->sdci_total_cost,2) }}</td>
    </tr>
    @php($total_submitted = $total_submitted + $detail->sdci_total_submitted)
    @php($total_cost = $total_cost + $detail->sdci_total_cost)
    @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4" class="text-right">Total</td>
            <td class="text-right">{{ number_format($total_submitted,2) }}</td>
            <td class="text-right">{{ number_format($total_cost,2) }}</td>
            
        </tr>
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
	var table = $('#details').DataTable( {
		responsive: true,
	} );
} );
</script>


