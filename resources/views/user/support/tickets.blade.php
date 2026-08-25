@extends('user.support.layout')

@section('support_title', 'My Support Tickets')
@section('support_subtitle', 'View and manage your support tickets')
@section('support_ticket', 'active')
@section('support_breadcrumb')
    <li class="active">All Tickets</li>
@endsection

@section('support_content')
    <div class="row">
        <div class="col-xs-12">
            <div class="clearfix">
                <div class="pull-right tableTools-container">
                    <a href="{{ route('user.support.tickets.create') }}" class="btn btn-white btn-primary btn-bold">
                        <i class="ace-icon fa fa-plus bigger-120 blue"></i>
                        Create New Ticket
                    </a>
                </div>
            </div>
            
            <div class="table-header">
                My Support Tickets
            </div>
            
            <div>
                <table id="tickets-table" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Ticket ID</th>
                            <th>Subject</th>
                            <th>Category</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Created Date</th>
                            <th>Last Update</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                            <tr>
                                <td>{{ $ticket->ticket_number }}</td>
                                <td>
                                    <a href="{{ route('user.support.tickets.show', $ticket->id) }}">
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
                                            <span class="label label-danger">Urgent</span>
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
                                <td>{{ $ticket->updated_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <div class="hidden-sm hidden-xs action-buttons">
                                        <a href="{{ route('user.support.tickets.show', $ticket->id) }}" 
                                           class="btn btn-xs btn-info" title="View">
                                            <i class="ace-icon fa fa-eye bigger-120"></i>
                                        </a>
                                        
                                        @if($ticket->status === 'closed')
                                            <form method="POST" action="{{ route('user.support.tickets.reopen', $ticket->id) }}" 
                                                  style="display: inline-block;">
                                                @csrf
                                                <button type="submit" class="btn btn-xs btn-warning" title="Reopen">
                                                    <i class="ace-icon fa fa-refresh bigger-120"></i>
                                                </button>
                                            </form>
                                        @elseif($ticket->status !== 'closed')
                                            <form method="POST" action="{{ route('user.support.tickets.close', $ticket->id) }}" 
                                                  style="display: inline-block;">
                                                @csrf
                                                <button type="submit" class="btn btn-xs btn-danger" title="Close">
                                                    <i class="ace-icon fa fa-times bigger-120"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No support tickets found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                
                @if($tickets->hasPages())
                    <div class="pull-right">
                        {{ $tickets->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#tickets-table').dataTable({
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
