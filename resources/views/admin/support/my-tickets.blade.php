@extends('admin.master')

@section('page_title', 'My Assigned Tickets')
@section('support_menu_class', 'active')
@section('support_my_tickets_class', 'active')

@section('page_location')
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="">Dashboard</a>
        </li>
        <li>
            <a href="{{ route('admin.ticket.tickets') }}">Support Tickets</a>
        </li>
        <li class="active">My Assigned Tickets</li>
    </ul>
@endsection

@section('page_header')
    <h1>
        <i class="ace-icon fa fa-user"></i>
        My Assigned Tickets
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            Tickets assigned to me
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
                            <div class="infobox-content">Total Assigned</div>
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
                            <i class="ace-icon fa fa-gears"></i>
                        </div>
                        <div class="infobox-data">
                            <span class="infobox-data-number">{{ $inProgressCount ?? 0 }}</span>
                            <div class="infobox-content">In Progress</div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-3">
                    <div class="infobox infobox-red">
                        <div class="infobox-icon">
                            <i class="ace-icon fa fa-exclamation-triangle"></i>
                        </div>
                        <div class="infobox-data">
                            <span class="infobox-data-number">{{ $highPriorityCount ?? 0 }}</span>
                            <div class="infobox-content">High Priority</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-6"></div>

            <!-- Tickets Table -->
            <div class="table-header">
                Tickets Assigned to Me
                <div class="pull-right">
                    <span class="badge badge-primary">{{ $tickets->total() }} tickets</span>
                </div>
            </div>
            
            <div>
                <table id="my-tickets-table" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Ticket ID</th>
                            <th>Customer</th>
                            <th>Subject</th>
                            <th>Category</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Last Reply</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                            <tr>
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
                                        <a href="{{ route('admin.ticket.tickets.show', $ticket->id) }}#reply" 
                                           class="btn btn-xs btn-success" title="Reply">
                                            <i class="ace-icon fa fa-reply bigger-120"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">
                                    <div class="alert alert-info">
                                        <i class="ace-icon fa fa-info-circle"></i>
                                        No tickets assigned to you.
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
    $('#my-tickets-table').dataTable({
        "bPaginate": false,
        "bLengthChange": false,
        "bFilter": true,
        "bSort": true,
        "bInfo": false,
        "bAutoWidth": false
    });
});
</script>
@endpush
