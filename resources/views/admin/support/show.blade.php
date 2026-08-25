@extends('admin.master')

@section('page_title', 'Ticket Details')
@section('support_menu', 'active')

@section('page_location')
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="">Dashboard</a>
        </li>
        <li>
            <a href="{{ route('admin.ticket.tickets') }}">Support Tickets</a>
        </li>
        <li class="active">Ticket Details</li>
    </ul>
@endsection

@section('page_header')
    <h1>
        <i class="ace-icon fa fa-ticket"></i>
        Ticket: {{ $ticket->ticket_number }}
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            {{ $ticket->subject }}
        </small>
    </h1>
@endsection

@section('main_content')
    <div class="row">
        <div class="col-md-8">
            
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

            <!-- Ticket Information -->
            <div class="widget-box">
                <div class="widget-header">
                    <h4 class="widget-title">Ticket Information</h4>
                    <div class="widget-toolbar">
                        <span class="badge {{ $ticket->status == 'closed' ? 'badge-secondary' : 'badge-success' }}">
                            {{ strtoupper($ticket->status) }}
                        </span>
                    </div>
                </div>
                <div class="widget-body">
                    <div class="widget-main">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <td width="40%"><strong>Ticket ID:</strong></td>
                                        <td>{{ $ticket->ticket_number }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Customer:</strong></td>
                                        <td>
                                            @if($ticket->user)
                                                <div>
                                                    <strong>{{ $ticket->user->company_name ?: $ticket->user->email }}</strong>
                                                </div>
                                                <small class="text-muted">{{ $ticket->user->email }}</small>
                                                <br>
                                                <small class="text-muted">{{ $ticket->user->cellphone }}</small>
                                            @else
                                                <span class="label label-danger">User Deleted</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Subject:</strong></td>
                                        <td>{{ $ticket->subject }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Category:</strong></td>
                                        <td>{{ $ticket->category }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <td width="40%"><strong>Priority:</strong></td>
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
                                    </tr>
                                    <tr>
                                        <td><strong>Status:</strong></td>
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
                                    </tr>
                                    <tr>
                                        <td><strong>Created:</strong></td>
                                        <td>{{ $ticket->created_at->format('Y-m-d H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Last Updated:</strong></td>
                                        <td>{{ $ticket->updated_at->format('Y-m-d H:i') }}</td>
                                    </tr>
                                    @if($ticket->last_replied_at)
                                        <tr>
                                            <td><strong>Last Reply:</strong></td>
                                            <td>{{ $ticket->last_replied_at->format('Y-m-d H:i') }}</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                        
                        <div class="space-6"></div>
                        
                        <div class="form-group">
                            <label><strong>Description:</strong></label>
                            <div class="well well-lg" style="background-color: #f8f9fa;">
                                {!! nl2br(e($ticket->description)) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ticket Actions -->
            <div class="widget-box">
                <div class="widget-header">
                    <h4 class="widget-title">Ticket Actions</h4>
                </div>
                <div class="widget-body">
                    <div class="widget-main">
                        <div class="row">
                            <div class="col-md-6">
                                <form method="POST" action="{{ route('admin.ticket.tickets.updateStatus', $ticket->id) }}">
                                    @csrf
                                    <div class="form-group">
                                        <label for="status">Update Status</label>
                                        <div class="input-group">
                                            <select name="status" class="form-control" required>
                                                <option value="open" {{ $ticket->status == 'open' ? 'selected' : '' }}>Open</option>
                                                <option value="in_progress" {{ $ticket->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                <option value="on_hold" {{ $ticket->status == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                                                <option value="resolved" {{ $ticket->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                                <option value="closed" {{ $ticket->status == 'closed' ? 'selected' : '' }}>Closed</option>
                                            </select>
                                            <span class="input-group-btn">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="ace-icon fa fa-refresh"></i>
                                                    Update
                                                </button>
                                            </span>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <form method="POST" action="{{ route('admin.ticket.tickets.updatePriority', $ticket->id) }}">
                                    @csrf
                                    <div class="form-group">
                                        <label for="priority">Update Priority</label>
                                        <div class="input-group">
                                            <select name="priority" class="form-control" required>
                                                <option value="low" {{ $ticket->priority == 'low' ? 'selected' : '' }}>Low</option>
                                                <option value="medium" {{ $ticket->priority == 'medium' ? 'selected' : '' }}>Medium</option>
                                                <option value="high" {{ $ticket->priority == 'high' ? 'selected' : '' }}>High</option>
                                                <option value="urgent" {{ $ticket->priority == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                            </select>
                                            <span class="input-group-btn">
                                                <button type="submit" class="btn btn-warning">
                                                    <i class="ace-icon fa fa-exclamation-triangle"></i>
                                                    Update
                                                </button>
                                            </span>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <form method="POST" action="{{ route('admin.ticket.tickets.assign', $ticket->id) }}">
                                    @csrf
                                    <div class="form-group">
                                        <label for="assigned_to">Assign to Agent</label>
                                        <div class="input-group">
                                            <select name="assigned_to" class="form-control" required>
                                                <option value="">Select Agent</option>
                                                @foreach($admins as $admin)
                                                    <option value="{{ $admin->id }}" {{ $ticket->assigned_to == $admin->id ? 'selected' : '' }}>
                                                        {{ $admin->company_name ?: $admin->email }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <span class="input-group-btn">
                                                <button type="submit" class="btn btn-info">
                                                    <i class="ace-icon fa fa-user"></i>
                                                    Assign
                                                </button>
                                            </span>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Conversation -->
            <div class="widget-box">
                <div class="widget-header">
                    <h4 class="widget-title">Conversation</h4>
                </div>
                <div class="widget-body">
                    <div class="widget-main">
                        <div class="timeline-container">
                            <!-- Original Ticket -->
                            <div class="timeline-items">
                                <div class="timeline-item clearfix">
                                    <div class="timeline-info">
                                        <i class="timeline-indicator btn btn-primary no-hover"></i>
                                    </div>
                                    <div class="widget-box">
                                        <div class="widget-header widget-header-small">
                                            <h5 class="widget-title smaller">
                                                <i class="ace-icon fa fa-user"></i>
                                                {{ $ticket->user->company_name ?? $ticket->user->email ?? 'User' }}
                                                <span class="label label-info">Ticket Created</span>
                                            </h5>
                                            <span class="widget-toolbar no-border">
                                                <span class="grey">{{ $ticket->created_at->format('Y-m-d H:i') }}</span>
                                            </span>
                                        </div>
                                        <div class="widget-body">
                                            <div class="widget-main">
                                                <div class="well well-sm">
                                                    {!! nl2br(e($ticket->description)) !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Replies -->
                            @foreach($ticket->replies as $reply)
                                <div class="timeline-items">
                                    <div class="timeline-item clearfix">
                                        <div class="timeline-info">
                                            <i class="timeline-indicator btn {{ $reply->user->role == 1 ? 'btn-success' : 'btn-info' }} no-hover"></i>
                                        </div>
                                        <div class="widget-box">
                                            <div class="widget-header widget-header-small">
                                                <h5 class="widget-title smaller">
                                                    <i class="ace-icon {{ $reply->user->role == 1 ? 'fa fa-headphones' : 'fa fa-user' }}"></i>
                                                    {{ $reply->user->company_name ?? $reply->user->email }}
                                                    @if($reply->is_internal_note)
                                                        <span class="label label-warning">Internal Note</span>
                                                    @elseif($reply->user->role == 1)
                                                        <span class="label label-success">Support Agent</span>
                                                    @else
                                                        <span class="label label-info">Customer</span>
                                                    @endif
                                                </h5>
                                                <span class="widget-toolbar no-border">
                                                    <span class="grey">{{ $reply->created_at->format('Y-m-d H:i') }}</span>
                                                </span>
                                            </div>
                                            <div class="widget-body">
                                                <div class="widget-main">
                                                    {!! nl2br(e($reply->message)) !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Reply -->
            <div class="widget-box">
                <div class="widget-header">
                    <h4 class="widget-title">Add Reply</h4>
                </div>
                <div class="widget-body">
                    <div class="widget-main">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        <form method="POST" action="{{ route('admin.ticket.tickets.reply', $ticket->id) }}">
                            @csrf
                            <div class="form-group">
                                <label for="message">Your Message *</label>
                                <textarea class="form-control" id="message" name="message" rows="6" 
                                          required placeholder="Type your reply here...">{{ old('message') }}</textarea>
                            </div>
                            
                            <div class="form-group">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="is_internal_note" value="1" {{ old('is_internal_note') ? 'checked' : '' }}>
                                        <span class="lbl"> This is an internal note (not visible to customer)</span>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="form-actions center">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ace-icon fa fa-reply"></i>
                                    Send Reply
                                </button>
                                <a href="{{ route('admin.ticket.tickets') }}" class="btn btn-default">
                                    <i class="ace-icon fa fa-arrow-left"></i>
                                    Back to Tickets
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Sidebar -->
        <div class="col-md-4">
            <!-- Customer Info -->
            @if($ticket->user)
                <div class="widget-box">
                    <div class="widget-header">
                        <h4 class="widget-title">Customer Information</h4>
                    </div>
                    <div class="widget-body">
                        <div class="widget-main">
                            <div class="text-center">
                                <div class="profile-user-info">
                                    <div class="profile-info-row">
                                        <div class="profile-info-name">Company</div>
                                        <div class="profile-info-value">
                                            <span>{{ $ticket->user->company_name ?: 'N/A' }}</span>
                                        </div>
                                    </div>
                                    <div class="profile-info-row">
                                        <div class="profile-info-name">Email</div>
                                        <div class="profile-info-value">
                                            <span>{{ $ticket->user->email }}</span>
                                        </div>
                                    </div>
                                    <div class="profile-info-row">
                                        <div class="profile-info-name">Phone</div>
                                        <div class="profile-info-value">
                                            <span>{{ $ticket->user->cellphone ?: 'N/A' }}</span>
                                        </div>
                                    </div>
                                    <div class="profile-info-row">
                                        <div class="profile-info-name">Status</div>
                                        <div class="profile-info-value">
                                            <span class="label label-{{ $ticket->user->status == 1 ? 'success' : 'danger' }}">
                                                {{ $ticket->user->status == 1 ? 'Active' : 'Inactive' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="profile-info-row">
                                        <div class="profile-info-name">Joined</div>
                                        <div class="profile-info-value">
                                            <span>{{ $ticket->user->created_at->format('Y-m-d') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <a href="" class="btn btn-sm btn-primary">
                                    <i class="ace-icon fa fa-eye"></i>
                                    View Customer
                                </a>
                                <a href="mailto:{{ $ticket->user->email }}" class="btn btn-sm btn-success">
                                    <i class="ace-icon fa fa-envelope"></i>
                                    Email Customer
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Assigned Agent -->
            <div class="widget-box">
                <div class="widget-header">
                    <h4 class="widget-title">Assigned Agent</h4>
                </div>
                <div class="widget-body">
                    <div class="widget-main">
                        @if($ticket->assignedAgent)
                            <div class="text-center">
                                <h4>{{ $ticket->assignedAgent->company_name ?: $ticket->assignedAgent->email }}</h4>
                                <p class="text-muted">{{ $ticket->assignedAgent->email }}</p>
                                <span class="badge badge-info">{{ $ticket->assignedAgent->position ?: 'Support Agent' }}</span>
                            </div>
                        @else
                            <div class="alert alert-warning text-center">
                                <i class="ace-icon fa fa-exclamation-triangle"></i>
                                <strong>Not Assigned</strong>
                                <p>This ticket is not assigned to any agent.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="widget-box">
                <div class="widget-header">
                    <h4 class="widget-title">Customer Ticket History</h4>
                </div>
                <div class="widget-body">
                    <div class="widget-main">
                        @if($ticket->user)
                            @php
                                $customerTickets = $ticket->user->supportTickets()->where('id', '!=', $ticket->id)->get();
                            @endphp
                            <ul class="list-unstyled">
                                <li>
                                    <strong>Total Tickets:</strong>
                                    <span class="pull-right badge badge-primary">{{ $customerTickets->count() + 1 }}</span>
                                </li>
                                <li>
                                    <strong>Open Tickets:</strong>
                                    <span class="pull-right badge badge-success">
                                        {{ $customerTickets->where('status', 'open')->count() + ($ticket->status == 'open' ? 1 : 0) }}
                                    </span>
                                </li>
                                <li>
                                    <strong>Closed Tickets:</strong>
                                    <span class="pull-right badge badge-default">
                                        {{ $customerTickets->where('status', 'closed')->count() + ($ticket->status == 'closed' ? 1 : 0) }}
                                    </span>
                                </li>
                            </ul>
                            @if($customerTickets->count() > 0)
                                <hr>
                                <h5>Recent Tickets:</h5>
                                <div class="scrollable" style="max-height: 200px; overflow-y: auto;">
                                    @foreach($customerTickets->take(5) as $customerTicket)
                                        <div class="media">
                                            <div class="media-body">
                                                <a href="{{ route('admin.ticket.tickets.show', $customerTicket->id) }}" 
                                                   class="media-heading blue">
                                                    {{ $customerTicket->ticket_number }}
                                                </a>
                                                <div class="small">
                                                    {{ strlen($customerTicket->subject) > 40 ? substr($customerTicket->subject, 0, 40) . '...' : $customerTicket->subject }}
                                                </div>
                                                <small class="text-muted">{{ $customerTicket->created_at->format('Y-m-d') }}</small>
                                                <span class="badge badge-{{ $customerTicket->status == 'closed' ? 'default' : 'success' }} pull-right">
                                                    {{ $customerTicket->status }}
                                                </span>
                                            </div>
                                        </div>
                                        @if(!$loop->last)<hr class="hr-xs">@endif
                                    @endforeach
                                </div>
                            @endif
                        @else
                            <div class="alert alert-info">
                                <i class="ace-icon fa fa-info-circle"></i>
                                Customer account not found.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
.timeline-container {
    position: relative;
}
.timeline-item {
    position: relative;
    padding-bottom: 20px;
}
.timeline-info {
    float: left;
    width: 60px;
    text-align: center;
}
.timeline-indicator {
    position: relative;
    width: 16px;
    height: 16px;
    display: block;
    margin: 0 auto;
    border-radius: 100%;
    border: 2px solid #FFF;
}
.scrollable {
    scrollbar-width: thin;
    scrollbar-color: #888 #f1f1f1;
}
.scrollable::-webkit-scrollbar {
    width: 6px;
}
.scrollable::-webkit-scrollbar-track {
    background: #f1f1f1;
}
.scrollable::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}
.scrollable::-webkit-scrollbar-thumb:hover {
    background: #555;
}
.profile-user-info {
    margin: 0;
    padding: 0;
}
.profile-info-row {
    display: flex;
    justify-content: space-between;
    padding: 6px 0;
    border-bottom: 1px solid #eee;
}
.profile-info-row:last-child {
    border-bottom: none;
}
.profile-info-name {
    font-weight: bold;
    color: #666;
}
.profile-info-value {
    text-align: right;
}
</style>
@endpush