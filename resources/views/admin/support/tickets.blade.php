@extends('admin.master')

@section('page_title', 'Support Tickets Management')
@section('support_menu_class', 'active')
@section('support_tickets_class', 'active')
@section('page_location')
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="">Dashboard</a>
        </li>
        <li class="active">Support Tickets</li>
    </ul>
@endsection

@section('page_header')
    <h1>
        <i class="ace-icon fa fa-ticket"></i>
        Support Tickets
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            Manage all support tickets
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

            @if(session('error'))
                <div class="alert alert-danger">
                    <button type="button" class="close" data-dismiss="alert">
                        <i class="ace-icon fa fa-times"></i>
                    </button>
                    <strong>
                        <i class="ace-icon fa fa-times"></i>
                        Error!
                    </strong>
                    {{ session('error') }}
                    <br>
                </div>
            @endif

            <!-- Quick Stats -->
            <div class="row">
                <div class="col-xs-12 col-sm-3">
                    <div class="infobox infobox-blue">
                        <div class="infobox-icon">
                            <i class="ace-icon fa fa-ticket"></i>
                        </div>
                        <div class="infobox-data">
                            <span class="infobox-data-number">{{ $stats['total'] ?? 0 }}</span>
                            <div class="infobox-content">Total Tickets</div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-3">
                    <div class="infobox infobox-green">
                        <div class="infobox-icon">
                            <i class="ace-icon fa fa-clock-o"></i>
                        </div>
                        <div class="infobox-data">
                            <span class="infobox-data-number">{{ $stats['open'] ?? 0 }}</span>
                            <div class="infobox-content">Open</div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-3">
                    <div class="infobox infobox-orange">
                        <div class="infobox-icon">
                            <i class="ace-icon fa fa-users"></i>
                        </div>
                        <div class="infobox-data">
                            <span class="infobox-data-number">{{ $stats['unassigned'] ?? 0 }}</span>
                            <div class="infobox-content">Unassigned</div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-3">
                    <div class="infobox infobox-red">
                        <div class="infobox-icon">
                            <i class="ace-icon fa fa-exclamation-triangle"></i>
                        </div>
                        <div class="infobox-data">
                            <span class="infobox-data-number">{{ $highPriority ?? 0 }}</span>
                            <div class="infobox-content">High Priority</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="space-6"></div>
            <div class="row">
                <div class="col-xs-12">
                    <div class="btn-group">
                        <a href="{{ route('admin.ticket.tickets') }}" class="btn btn-sm btn-white {{ request()->routeIs('admin.ticket.tickets') ? 'btn-primary' : '' }}">
                            All Tickets
                        </a>
                        <a href="{{ route('admin.ticket.myTickets') }}" class="btn btn-sm btn-white {{ request()->routeIs('admin.ticket.myTickets') ? 'btn-primary' : '' }}">
                            <i class="ace-icon fa fa-user"></i>
                            My Tickets ({{ $myTicketsCount ?? 0 }})
                        </a>
                        <a href="{{ route('admin.ticket.unassignedTickets') }}" class="btn btn-sm btn-white {{ request()->routeIs('admin.ticket.unassignedTickets') ? 'btn-primary' : '' }}">
                            <i class="ace-icon fa fa-question-circle"></i>
                            Unassigned ({{ $unassignedCount ?? 0 }})
                        </a>
                    </div>
                </div>
            </div>

            <div class="space-6"></div>

            <!-- Filters -->
            <div class="widget-box">
                <div class="widget-header">
                    <h4 class="widget-title">Filters</h4>
                </div>
                <div class="widget-body">
                    <div class="widget-main">
                        <form method="GET" action="{{ route('admin.ticket.tickets') }}" class="form-inline">
                            <div class="form-group">
                                <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
                            </div>
                            &nbsp;
                            <div class="form-group">
                                <select name="status" class="form-control" onchange="this.form.submit()">
                                    <option value="">All Status</option>
                                    <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="on_hold" {{ request('status') == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                                    <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                                </select>
                            </div>
                            &nbsp;
                            <div class="form-group">
                                <select name="priority" class="form-control" onchange="this.form.submit()">
                                    <option value="">All Priority</option>
                                    <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                                    <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                </select>
                            </div>
                            &nbsp;
                            <div class="form-group">
                                <select name="assigned_to" class="form-control" onchange="this.form.submit()">
                                    <option value="">All Agents</option>
                                    @foreach($admins as $admin)
                                        <option value="{{ $admin->id }}" {{ request('assigned_to') == $admin->id ? 'selected' : '' }}>
                                            {{ $admin->company_name ?: $admin->email }}
                                        </option>
                                    @endforeach
                                    <option value="unassigned" {{ request('assigned_to') == 'unassigned' ? 'selected' : '' }}>Unassigned</option>
                                </select>
                            </div>
                            &nbsp;
                            <div class="form-group">
                                <select name="category" class="form-control" onchange="this.form.submit()">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                            {{ $category }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Tickets Table -->
            <div class="table-header">
                Support Tickets
                <div class="pull-right">
                    <span class="badge badge-primary">{{ $tickets->total() }} records</span>
                </div>
            </div>
            
            <div>
                <table id="tickets-table" class="table table-striped table-bordered table-hover">
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
                            <th>Assigned To</th>
                            <th>Created</th>
                            <th>Last Reply</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $ticket)
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
                                    <a href="{{ route('admin.ticket.tickets.show', $ticket->id) }}">
                                        {{ strlen($ticket->subject) > 60 ? substr($ticket->subject, 0, 60) . '...' : $ticket->subject }}
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
                                <td>
                                    @if($ticket->assignedAgent)
                                        <div>
                                            <strong>{{ $ticket->assignedAgent->company_name ?: $ticket->assignedAgent->email }}</strong>
                                        </div>
                                        <small class="text-muted">{{ $ticket->assignedAgent->email }}</small>
                                    @else
                                        <span class="label label-danger">Unassigned</span>
                                    @endif
                                </td>
                                <td>{{ $ticket->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    @if($ticket->last_replied_at)
                                        {{ $ticket->last_replied_at->format('Y-m-d H:i') }}
                                    @else
                                        <span class="text-muted">Never</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="hidden-sm hidden-xs action-buttons">
                                        <a href="{{ route('admin.ticket.tickets.show', $ticket->id) }}" 
                                           class="btn btn-xs btn-info" title="View">
                                            <i class="ace-icon fa fa-eye bigger-120"></i>
                                        </a>
                                        <button class="btn btn-xs btn-danger delete-ticket" 
                                                data-id="{{ $ticket->id }}" 
                                                title="Delete">
                                            <i class="ace-icon fa fa-trash-o bigger-120"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center">
                                    <div class="alert alert-info">
                                        <i class="ace-icon fa fa-info-circle"></i>
                                        No tickets found.
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

            <!-- Bulk Actions -->
            <div class="space-6"></div>
            <div class="widget-box">
                <div class="widget-header">
                    <h4 class="widget-title">Bulk Actions</h4>
                </div>
                <div class="widget-body">
                    <div class="widget-main">
                        <div class="row">
                            <div class="col-md-4">
                                <select id="bulk-action" class="form-control">
                                    <option value="">Select Action</option>
                                    <option value="assign">Assign to Agent</option>
                                    <option value="status">Update Status</option>
                                    <option value="priority">Update Priority</option>
                                    <option value="delete">Delete Selected</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <div id="action-params" style="display: none;">
                                    <!-- Dynamic parameters will be loaded here -->
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button id="apply-bulk-action" class="btn btn-primary">
                                    <i class="ace-icon fa fa-check"></i>
                                    Apply
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
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
    });

    // Bulk action parameters
    $('#bulk-action').change(function() {
        const action = $(this).val();
        const paramsDiv = $('#action-params');
        
        paramsDiv.empty();
        
        switch(action) {
            case 'assign':
                paramsDiv.html(`
                    <select id="bulk-agent" class="form-control">
                        <option value="">Select Agent</option>
                        @foreach($admins as $admin)
                            <option value="{{ $admin->id }}">{{ $admin->company_name ?: $admin->email }}</option>
                        @endforeach
                    </select>
                `);
                break;
            case 'status':
                paramsDiv.html(`
                    <select id="bulk-status" class="form-control">
                        <option value="">Select Status</option>
                        <option value="open">Open</option>
                        <option value="in_progress">In Progress</option>
                        <option value="on_hold">On Hold</option>
                        <option value="resolved">Resolved</option>
                        <option value="closed">Closed</option>
                    </select>
                `);
                break;
            case 'priority':
                paramsDiv.html(`
                    <select id="bulk-priority" class="form-control">
                        <option value="">Select Priority</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                    </select>
                `);
                break;
        }
        
        if (action) {
            paramsDiv.show();
        } else {
            paramsDiv.hide();
        }
    });

    // Apply bulk action
    $('#apply-bulk-action').click(function() {
        const selectedTickets = [];
        $('.ticket-checkbox:checked').each(function() {
            selectedTickets.push($(this).val());
        });

        if (selectedTickets.length === 0) {
            alert('Please select at least one ticket.');
            return;
        }

        const action = $('#bulk-action').val();
        
        if (!action) {
            alert('Please select an action.');
            return;
        }

        let url, data;
        
        switch(action) {
            case 'assign':
                const agentId = $('#bulk-agent').val();
                if (!agentId) {
                    alert('Please select an agent.');
                    return;
                }
                url = '{{ route("admin.ticket.bulkAssign") }}';
                data = {
                    ticket_ids: selectedTickets,
                    assigned_to: agentId
                };
                break;
                
            case 'status':
                const status = $('#bulk-status').val();
                if (!status) {
                    alert('Please select a status.');
                    return;
                }
                url = '{{ route("admin.ticket.bulkUpdateStatus") }}';
                data = {
                    ticket_ids: selectedTickets,
                    status: status
                };
                break;
                
            case 'priority':
                const priority = $('#bulk-priority').val();
                if (!priority) {
                    alert('Please select a priority.');
                    return;
                }
                // You need to create this route
                url = '{{ route("admin.ticket.bulkUpdatePriority") }}';
                data = {
                    ticket_ids: selectedTickets,
                    priority: priority
                };
                break;
                
            case 'delete':
                if (!confirm('Are you sure you want to delete selected tickets?')) {
                    return;
                }
                url = '{{ route("admin.ticket.bulkDelete") }}';
                data = {
                    ticket_ids: selectedTickets
                };
                break;
        }

        data._token = '{{ csrf_token() }}';
        
        $.ajax({
            url: url,
            method: 'POST',
            data: data,
            success: function(response) {
                alert(response.message);
                location.reload();
            },
            error: function(xhr) {
                alert('Error: ' + (xhr.responseJSON.message || 'Something went wrong.'));
            }
        });
    });

    // Delete single ticket
    $('.delete-ticket').click(function() {
        if (confirm('Are you sure you want to delete this ticket?')) {
            const ticketId = $(this).data('id');
            $.ajax({
                url: '{{ url("admin/support/tickets") }}/' + ticketId,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    alert('Ticket deleted successfully.');
                    location.reload();
                },
                error: function(xhr) {
                    alert('Error deleting ticket.');
                }
            });
        }
    });
});
</script>
@endpush
