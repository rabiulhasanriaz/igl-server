@extends('user.support.layout')

@section('support_title', 'Ticket: ' . $ticket->ticket_number)
@section('support_subtitle', $ticket->subject)

@section('support_breadcrumb')
    <li class="active">Ticket Details</li>
@endsection

@section('support_content')
    <div class="row">
        <div class="col-md-12">
            <!-- Ticket Info -->
            <div class="widget-box">
                <div class="widget-header">
                    <h4 class="widget-title">Ticket Information</h4>
                    <div class="widget-toolbar">
                        @if($ticket->status === 'closed')
                            <span class="label label-default">CLOSED</span>
                        @else
                            <span class="label label-success">OPEN</span>
                        @endif
                    </div>
                </div>
                <div class="widget-body">
                    <div class="widget-main">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <td width="30%"><strong>Ticket ID:</strong></td>
                                        <td>{{ $ticket->ticket_number }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Subject:</strong></td>
                                        <td>{{ $ticket->subject }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Category:</strong></td>
                                        <td>{{ $ticket->category }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Priority:</strong></td>
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
                                                    <span class="label label-danger">Urgent</span>
                                                    @break
                                            @endswitch
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <td width="30%"><strong>Status:</strong></td>
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
                                    @if($ticket->assignedAgent)
                                        <tr>
                                            <td><strong>Assigned To:</strong></td>
                                            <td>{{ $ticket->assignedAgent->company_name ?? $ticket->assignedAgent->email }}</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                        
                        <div class="space-6"></div>
                        
                        <div class="form-group">
                            <label><strong>Description:</strong></label>
                            <div class="well well-sm">
                                {!! nl2br(e($ticket->description)) !!}
                            </div>
                        </div>
                        
                        <div class="form-actions center">
                            @if($ticket->status === 'closed')
                                <form method="POST" action="{{ route('user.support.tickets.reopen', $ticket->id) }}" 
                                      style="display: inline-block;">
                                    @csrf
                                    <button type="submit" class="btn btn-warning">
                                        <i class="ace-icon fa fa-refresh"></i>
                                        Reopen Ticket
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('user.support.tickets.close', $ticket->id) }}" 
                                      style="display: inline-block;">
                                    @csrf
                                    <button type="submit" class="btn btn-danger">
                                        <i class="ace-icon fa fa-times"></i>
                                        Close Ticket
                                    </button>
                                </form>
                            @endif
                            
                            <a href="{{ route('user.support.tickets') }}" class="btn btn-default">
                                <i class="ace-icon fa fa-arrow-left"></i>
                                Back to Tickets
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Conversation Thread -->
            <div class="widget-box">
                <div class="widget-header">
                    <h4 class="widget-title">Conversation</h4>
                </div>
                <div class="widget-body">
                    <div class="widget-main">
                        <div class="timeline-container">
                            @foreach($ticket->replies as $reply)
                                <div class="timeline-items">
                                    <div class="timeline-item clearfix">
                                        <div class="timeline-info">
                                            <i class="timeline-indicator {{ $reply->user_id == auth()->id() ? 'btn btn-info no-hover' : 'btn btn-success no-hover' }}"></i>
                                        </div>
                                        <div class="widget-box {{ $reply->user_id == auth()->id() ? 'collapsed' : '' }}">
                                            <div class="widget-header widget-header-small">
                                                <h5 class="widget-title smaller">
                                                    {{ $reply->user->company_name ?? $reply->user->email }}
                                                    @if($reply->is_internal_note)
                                                        <span class="label label-info">Internal Note</span>
                                                    @endif
                                                </h5>
                                                <span class="widget-toolbar">
                                                    <a href="#" data-action="collapse">
                                                        <i class="ace-icon fa fa-chevron-down"></i>
                                                    </a>
                                                </span>
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
            
            <!-- Reply Form (only if ticket is not closed) -->
            @if($ticket->status !== 'closed')
                <div class="widget-box">
                    <div class="widget-header">
                        <h4 class="widget-title">Add Reply</h4>
                    </div>
                    <div class="widget-body">
                        <div class="widget-main">
                            <form method="POST" action="{{ route('user.support.tickets.reply', $ticket->id) }}">
                                @csrf
                                <div class="form-group">
                                    <label for="message">Your Message *</label>
                                    <textarea class="form-control" id="message" name="message" rows="5" 
                                              required placeholder="Type your reply here...">{{ old('message') }}</textarea>
                                    @if($errors->has('message'))
                                        <span class="text-danger">{{ $errors->first('message') }}</span>
                                    @endif
                                </div>
                                
                                <div class="form-actions center">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ace-icon fa fa-reply"></i>
                                        Send Reply
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
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
    .widget-box.collapsed .widget-body {
        display: block !important;
    }
</style>
@endpush
