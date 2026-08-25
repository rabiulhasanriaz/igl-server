<table id="todays-sms-report-details" class="table table-striped table-bordered table-hover">
    <thead>
    <tr>
        <th>Sl</th>
        <th>Mobile</th>
        <th>Targeted time</th>
        <th>Sender ID</th>
        <th>Operator</th>
        <th class="hidden-480">Charge/SMS</th>
        <th class="hidden-480">SMS Text</th>
        <th class="hidden-480">Status Report</th>
    </tr>
    </thead>

    <tbody>
    @php($serial=1)
    @foreach($reports as $report)
        <tr>
            <td>{{ @$serial++ }}</td>
            <td>{{ @$report->sct_cell_no }}</td>
            <td>{{ @$report->sct_target_time->format('Y-M-d H:sa') }}</td>
            <td>{{ @$report->sender->sir_sender_id }}</td>
            <td>{{ @$report->operator->ope_operator_name }}</td>
            <td class="hidden-480">{{ @$report->sct_sms_cost }}</td>
            <td class="hidden-480"><pre style="width: 340px; line-height: 15px;">{!! @$report->sct_message !!}</pre></td>
            @if(@$report->sct_delivery_report=="DELIVERED")
                <td class="hidden-480 text-success">{{ @$report->sct_delivery_report }}</td>
            @else
                <td class="text-danger hidden-480">{{ @$report->sct_delivery_report }}</td>
            @endif
        </tr>
    @endforeach
    </tbody>
</table>



    <script src="{{ asset('assets') }}/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('assets') }}/js/jquery.dataTables.bootstrap.min.js"></script>
    <script type="text/javascript">
        $('#todays-sms-report-details').DataTable();
    </script>



