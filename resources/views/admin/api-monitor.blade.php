@extends('admin.master')

@section('api_comission_class','active')

@section('page_location')
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="{{ route('admin.index') }}">Dashboard</a>
        </li>
        <li class="active">API Monitor</li>
    </ul>
@endsection

@section('page_header')
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session()->get('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <h1>
        API Monitor
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            Live Process & Errors
        </small>
    </h1>
@endsection

@section('main_content')

    <div class="space-6"></div>

    <div class="row">
        <div class="col-xs-12">

            @include('admin.partials.session_messages')
            @include('admin.partials.all_error_messages')

            <div class="row">
                <div class="col-sm-4">
                    <div class="infobox infobox-blue">
                        <div class="infobox-icon">
                            <i class="ace-icon fa fa-database"></i>
                        </div>
                        <div class="infobox-data">
                            <span class="infobox-data-number">
                                {{ isset($threadsConnected) ? $threadsConnected : 0 }}
                            </span>
                            <div class="infobox-content">DB Connected Threads</div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="infobox infobox-green">
                        <div class="infobox-icon">
                            <i class="ace-icon fa fa-server"></i>
                        </div>
                        <div class="infobox-data">
                            <span class="infobox-data-number">
                                {{ isset($maxConnections) ? $maxConnections : 0 }}
                            </span>
                            <div class="infobox-content">Max DB Connections</div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="infobox infobox-red">
                        <div class="infobox-icon">
                            <i class="ace-icon fa fa-exclamation-triangle"></i>
                        </div>
                        <div class="infobox-data">
                            <span class="infobox-data-number">
                                {{ isset($errorCount) ? $errorCount : 0 }}
                            </span>
                            <div class="infobox-content">Today Errors</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-12"></div>

            <div class="clearfix">
                <div class="pull-left">
                    <h4 class="header blue">
                        <i class="ace-icon fa fa-refresh"></i>
                        Latest API Activity
                    </h4>
                </div>

                <div class="pull-right">
                    <a href="{{ url()->current() }}" class="btn btn-sm btn-primary">
                        <i class="ace-icon fa fa-refresh"></i>
                        Refresh
                    </a>

                    <a href="{{ route('admin.api-monitor-delete-all') }}"
                       onclick="return confirm('Are you sure you want to delete all API logs?')"
                       class="btn btn-sm btn-danger">
                        <i class="ace-icon fa fa-trash"></i>
                        Delete All
                    </a>
                </div>
            </div>

            <table class="table table-striped table-bordered table-hover" id="api_monitor_table">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Time</th>
                        <th>API</th>
                        <th>Company Name</th>
                        <th>IP Address</th>
                        <th>Contacts</th>
                        <th>Status</th>
                        <th>Code</th>
                        <th>Process Time</th>
                        <th>Error</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @php($sl = 0)

                    @forelse($logs as $log)
                        <tr>
                            <td>{{ ++$sl }}</td>

                            <td>
                                {{ $log->created_at ? date('d-m-Y h:i:s A', strtotime($log->created_at)) : '' }}
                            </td>

                            <td>{{ $log->api_name }}</td>

                            <td>
                                @if($log->user)
                                    {{ $log->user->company_name }}
                                @else
                                    N/A
                                @endif
                            </td>

                            <td>{{ $log->ip_address }}</td>

                            <td>{{ $log->contacts_count }}</td>

                            <td>
                                @if($log->status == 'success')
                                    <span class="label label-success">Success</span>
                                @elseif($log->status == 'error')
                                    <span class="label label-danger">Error</span>
                                @elseif($log->status == 'processing')
                                    <span class="label label-warning">Processing</span>
                                @else
                                    <span class="label label-default">{{ $log->status }}</span>
                                @endif
                            </td>

                            <td>{{ $log->response_code }}</td>

                            <td>
                                @if($log->processing_time_ms)
                                    {{ $log->processing_time_ms }} ms
                                @else
                                    -
                                @endif
                            </td>

                            <td>
                                @if($log->error_message)
                                    <button type="button"
                                            class="btn btn-xs btn-danger"
                                            data-toggle="modal"
                                            data-target="#errorModal{{ $log->id }}">
                                        View
                                    </button>

                                    <div class="modal fade" id="errorModal{{ $log->id }}" tabindex="-1" role="dialog">
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal">
                                                        <span>&times;</span>
                                                    </button>
                                                    <h4 class="modal-title">API Error Details</h4>
                                                </div>

                                                <div class="modal-body">
                                                    <p><strong>API:</strong> {{ $log->api_name }}</p>
                                                    <p><strong>Company:</strong>
                                                        @if($log->user)
                                                            {{ $log->user->company_name }}
                                                        @else
                                                            N/A
                                                        @endif
                                                    </p>
                                                    <p><strong>IP:</strong> {{ $log->ip_address }}</p>
                                                    <p><strong>Time:</strong> {{ $log->created_at }}</p>
                                                    <hr>
                                                    <pre style="white-space: pre-wrap;">{{ $log->error_message }}</pre>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">
                                                        Close
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    -
                                @endif
                            </td>

                            <td>
                                <a href="{{ route('admin.api-monitor-delete', $log->id) }}"
                                   onclick="return confirm('Are you sure you want to delete this log?')"
                                   class="btn btn-xs btn-danger">
                                    <i class="ace-icon fa fa-trash"></i>
                                    Delete
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted">
                                No API activity found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </div>

@endsection

@section('custom_script')
    <script src="{{ asset('assets') }}/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('assets') }}/js/jquery.dataTables.bootstrap.min.js"></script>

    <script type="text/javascript">
        $('#api_monitor_table').DataTable({
            "order": [[0, "asc"]],
            "pageLength": 25
        });

        setTimeout(function () {
            window.location.reload();
        }, 10000);
    </script>
@endsection