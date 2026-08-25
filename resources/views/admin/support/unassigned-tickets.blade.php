@extends('admin.master')

@section('page_title', 'Unassigned Tickets')
@section('support_menu', 'active')

@section('page_location')
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
        </li>
        <li>
            <a href="{{ route('admin.support.tickets') }}">Support Tickets</a>
        </li>
        <li class="active">Unassigned Tickets</li>
    </ul>
@endsection

@section('page_header')
    <h1>
        <i class="ace-icon fa fa-question-circle"></i>
        Unassigned Tickets
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            Tickets waiting for assignment
        </small>
    </h1>
@endsection

@section('main_content')
    <div class="row">
        <div class="col-xs-12">
            
            @if(session('success'))
                <div class="alert alert-success">
                    <button type="button" class="close" data-dismiss="alert">
                        <i class="ace-icon fa fa-times"></i>
                    </button>
                    <strong>
                        <i class="ace-icon fa fa-check"></i>
                        Success!
                    </strong>
                    {{ session('success') }}
                    <br>
                </div>
            @endif

            <!-- Stats -->
            <div class="row">
                <div class="col-xs-12 col-sm-3">
                    <div class="infobox infobox-blue">
                        <div class="infobox-icon">
                            <i class="ace-icon fa fa-ticket"></i>
                        </div>
                        <div class="infobox-data">
                            <span class="infobox-data-number">{{ $tickets->total() }}</span>
                            <div class="infobox-content">Unassigned</div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-3">
                    <div class="infobox infobox-green">
                        <div class="infobox-icon">
                            <i class="ace-icon fa fa-clock-o"></i>
                        </div>
                        <div class="infobox-data">
                            <span class="infobox-data-number">{{ $openCount ?? 0 }}</span>
                            <div class="infobox-content">Open</div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-3">
                    <div class="infobox infobox-orange">
                        <div class="infobox-icon">
                            <i class="ace-icon fa fa-hourglass-start"></i>
                        </div>
                        <div class="infobox-data">
                            <span class="infobox-data-number">{{ $pendingCount ?? 0 }}</span>
                            <div class="infobox-content">Pending</div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-3">
                    <div class="infobox infobox-red">
                        <div class="infobox-icon">
                            <i class="ace-icon fa fa-exclamation-triangle"></i>
                        </div>
                        <div class="infobox-data">
                            <span class="infobox-data-number">{{ $urgentCount ?? 0 }}</span>
                            <div class="infobox-content">Urgent</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-6"></div>

            <!-- Quick Assign -->
            <div class="widget-box">
                <div class="widget-header">
                    <h4 class="widget-title">Quick Assign</h4>
                </div>
                <div class="widget-body">
                    <div class="widget-main">
                        <form method="POST" action="{{ route('admin.support.bulkAssign') }}" id="quick-assign-form">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <select name="assigned_to" class="form-control" required>
                                        <option value="">Select Agent</option>
                                        @foreach($admins as $admin)
                                            <option value="{{ $admin->id }}">
                                                {{ $admin->company_name ?: $admin->email }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-primary" id="assign-selected">
                                        <i class="ace-icon fa fa-user"></i>
                                        Assign Selected Tickets
                                    </button>
                                </div>
                            </div>
                            <input type="hidden" name="ticket_ids" id="selected-ticket-ids">
                        </form>
                    </div>
                </div>
            </div>

            <div class="space-6"></div>

            <!-- Tickets Table -->
            <div class="table-header">
                Unassigned Tickets
                <div class="pull-right">
                    <span class="badge badge-primary">{{ $tickets->total() }} tickets</span>
                </div>
            </div>
            
            <div>
                <table id="unassigned-tickets-table" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th class="center" width="50">
                                <label class="pos-rel">
                                    <input type="checkbox" class="ace" id="select-all">
                                    <span class="lbl"></span>
                                </label>
                            </th>
                            <th>Ticket ID</th>
                            <th>Customer</th>
                            <th>Subject</th>
                            <th>Category</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Age</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                            @php
                                $age = $ticket->created_at->diffInHours(now());
                                $ageClass = $age > 48 ? 'danger' : ($age > 24 ? 'warning' : 'success');
                            @endphp
                            <tr>
                                <td class="center">
                                    <label class="pos-rel">
                                        <input type="checkbox" class="ace ticket-checkbox" value="{{ $ticket->id }}">
                                        <span class="lbl"></span>
                                    </label>
                                </td>
                                <td>
                                    <strong>{{ $ticket->ticket_number }}</strong>
                                </td>
                                <td>
                                    @if($ticket->user)
                                        <div>
                                            <strong>{{ $ticket->user->company_name ?: $ticket->user->email }}</strong>
                                        </div>
                                        <small class="text-muted">{{ $ticket->user->email }}</small>
                                    @else
                                        <span class="label label-danger">User Deleted</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.support.tickets.show', $ticket->id) }}">
                                        {{ strlen($ticket->subject) > 50 ? substr($ticket->subject, 0, 50) . '...' : $ticket->subject }}
                                    </a>
                                </td>
                                <td>{{ $ticket->category }}</td>
                                <td>
                                    @switch($ticket->priority)
                                        @case('low')
                                            <span class="label label-info">Low</span>
                                            @break
                                        @case('medium')
                                            <span class="label label-warning">Medium</span>
                                            @break
                                        @case('high')
                                            <span class="label label-danger">High</span>
                                            @break
                                        @case('urgent')
                                            <span class="label label-danger" style="background-color: #dc3545;">Urgent</span>
                                            @break
                                    @endswitch
                                </td>
                                <td>
                                    @switch($ticket->status)
                                        @case('open')
                                            <span class="label label-success">Open</span>
                                            @break
                                        @case('in_progress')
                                            <span class="label label-primary">In Progress</span>
                                            @break
                                        @case('on_hold')
                                            <span class="label label-warning">On Hold</span>
                                            @break
                                        @case('resolved')
                                            <span class="label label-info">Resolved</span>
                                            @break
                                        @case('closed')
                                            <span class="label label-default">Closed</span>
                                            @break
                                    @endswitch
                                </td>
                                <td>{{ $ticket->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <span class="label label-{{ $ageClass }}">
                                        {{ $age }}h
                                    </span>
                                </td>
                                <td>
                                    <div class="hidden-sm hidden-xs action-buttons">
                                        <a href="{{ route('admin.support.tickets.show', $ticket->id) }}" 
                                           class="btn btn-xs btn-info" title="View">
                                            <i class="ace-icon fa fa-eye bigger-120"></i>
                                        </a>
                                        <button class="btn btn-xs btn-success assign-ticket" 
                                                data-id="{{ $ticket->id }}" 
                                                title="Assign to Me">
                                            <i class="ace-icon fa fa-user-plus bigger-120"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">
                                    <div class="alert alert-success">
                                        <i class="ace-icon fa fa-check-circle"></i>
                                        Great! All tickets are assigned.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                
                <!-- Pagination -->
                @if($tickets->hasPages())
                    <div class="pull-right">
                        {{ $tickets->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Select all checkbox
    $('#select-all').click(function() {
        $('.ticket-checkbox').prop('checked', this.checked);
        updateSelectedTickets();
    });

    // Update selected tickets when checkboxes change
    $('.ticket-checkbox').change(function() {
        updateSelectedTickets();
    });

    function updateSelectedTickets() {
        const selectedTickets = [];
        $('.ticket-checkbox:checked').each(function() {
            selectedTickets.push($(this).val());
        });
        $('#selected-ticket-ids').val(selectedTickets.join(','));
    }

    // Assign selected tickets
    $('#assign-selected').click(function(e) {
        e.preventDefault();
        
        const selectedTickets = $('#selected-ticket-ids').val();
        const assignedTo = $('select[name="assigned_to"]').val();
        
        if (!selectedTickets) {
            alert('Please select at least one ticket.');
            return;
        }
        
        if (!assignedTo) {
            alert('Please select an agent.');
            return;
        }
        
        const ticketIds = selectedTickets.split(',');
        
        $.ajax({
            url: '{{ route("admin.support.bulkAssign") }}',
            method: 'POST',
            data: {
                ticket_ids: ticketIds,
                assigned_to: assignedTo,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                alert(response.message);
                location.reload();
            },
            error: function(xhr) {
                alert('Error: ' + (xhr.responseJSON.message || 'Something went wrong.'));
            }
        });
    });

    // Assign single ticket to me
    $('.assign-ticket').click(function() {
        const ticketId = $(this).data('id');
        
        $.ajax({
            url: '{{ route("admin.support.tickets.assign", ["id" => ":id"]) }}'.replace(':id', ticketId),
            method: 'POST',
            data: {
                assigned_to: '{{ Auth::id() }}',
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                alert('Ticket assigned to you successfully.');
                location.reload();
            },
            error: function(xhr) {
                alert('Error assigning ticket.');
            }
        });
    });

    // Initialize DataTable
    $('#unassigned-tickets-table').dataTable({
        "bPaginate": false,
        "bLengthChange": false,
        "bFilter": true,
        "bSort": true,
        "bInfo": false,
        "bAutoWidth": false,
        "order": [[7, 'desc']] // Sort by created date
    });
});
</script>
@endpush