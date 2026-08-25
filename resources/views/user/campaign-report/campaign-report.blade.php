@extends('user.master')
@section('campaign_report_menu','active')
@section('page_location')
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="{{ route('employee.index') }}">Dashboard</a>
        </li>
        <li class="active">Reports SMS</li>
    </ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
    <h1>
        Reports & Statistics
        <i class="ace-icon fa fa-angle-double-right"></i>
        View DLR
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            Archived SMS
        </small>
    </h1>
@endsection


@section('main_content')

<form action="" method="get">
        <input type="hidden" name="_token" value="" id="_token">
        
            <div class="col-xs-3">
                <input type="text" value="{{ $start_date }}" class="form-control" id="start" readonly title="This report shows today only">
            </div>
            <div class="col-xs-3">
                <input type="text" value="Today only" class="form-control" id="end" readonly>
            </div>
            <div class="col-xs-3">
                <input type="text" name="camp_title" id="camp_title"  autocomplete="off" value="{{ request()->camp_title }}" class="form-control" placeholder="Enter Campaign Title" >
            </div>
		
		  <div class="col-xs-3">
			<button type="submit" class="btn btn-info btn-sm" name="searchbtn">Search</button>
              
		  </div>
    	  </form>
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    @if (Auth::user()->userDetail->dynamic_permission == 1)
                        @php($dynamic_permission = true)
                    @else
                        @php($dynamic_permission = false)
                    @endif
                    
                    <div class="tabs-nav-wrap">
                        <ul class="tabs-nav">
                            <li class="tab-nav-link" data-target="#tab-one">Route 1</li>
                            @if ($dynamic_permission)
                            <li class="tab-nav-link" data-target="#tab-two">Route 2</li>
                            @endif
                                        
                         </ul>
                       <div style="clear:both;"></div>
                     </div><!-- ends tabs-nav-wrap -->	  
          
          
                 <div class="tabs-main-content">
                 
                      <div id="tab-one" class="tab-content"><!-- Begins tab-one -->
                        <div class="tab-inner">
                            <button onclick="downloadReport()" class="btn btn-danger btn-sm">Download(without Content)</button>
                            <table class="table table-striped table-bordered table-hover" id="reseller_list">
                                <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>Campaign Title</th>
                                        <th>Source</th>
                                        <th>Submit time</th>
                                        <th>SMS Quantity</th>
                                        <th>Charge</th>
                                    </tr>
                                </thead>
                                
                                    @php($total = 0)
                                    @foreach ($transactions as $date => $data)
                                    <tbody>
                                        <tr>
                                            <td colspan="6">{{ $date }}</td>
                                        </tr>
                                        @foreach ($data as $item)
                                        
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->sci_campaign_title ?? $item->sci_campaign_id }}</td>
                                            <td>{{ (int) $item->sci_from_api === 1 ? 'API' : 'Panel' }}</td>
                                            <td>{{ $item->sci_targeted_time }}</td>
                                            <td class="text-center">
                                                {{ optional($item->creditHistory)->uch_sms_count ?? $item->sci_total_submitted }}
                                            </td>
                                            <td class="text-right">{{ number_format($item->sci_total_cost,2) }}</td>
                                        </tr> 
                                        @php($total += $item->sci_total_cost)
                                        
                                        
                                        @endforeach
                                    </tbody>
                                    
                                    @endforeach
                                    
                                    
                    
                                
                                
                            </table>
                         </div>
                      </div>
              
                      <div id="tab-two" class="tab-content">
                        <button onclick="downloadReportRoute2()" class="btn btn-danger btn-sm">Download(with Content)</button>
                        <div class="tab-inner">
                            <table class="table table-striped table-bordered table-hover" id="reseller_list">
                                <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>Campaign Title</th>
                                        <th>Submit time</th>
                                        <th>SMS Quantity</th>
                                        <th>Content</th>
                                        <th>Charge</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php($total = 0)
                                    @foreach ($route2transactions as $date => $data)
                                        <tr>
                                            <td colspan="5">{{ $date }}</td>
                                        </tr>
                                        @foreach ($data as $item)
                                        
                                        
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->sdci_campaign_title ?? $item->sdci_campaign_id }}</td>
                                            <td>{{ $item->sdci_targeted_time }}</td>
                                            <td class="text-center">
                                                {{ $item->sdci_total_submitted }}
                                            </td>
                                            <td>
                                                @if(count($item->campaignData) > 0)
                                                    <pre style="width:400px !important; height: 100px;">{!! $item->campaignData->first()->sd_message !!}</pre>
                                                @endif
                                            </td>
                                            <td class="text-right">{{ number_format($item->sdci_total_cost,2) }}</td>
                                        </tr> 
                                        @php($total += $item->sdci_total_cost)
                                        
                                        @endforeach
                                    @endforeach
                    
                                </tbody>
                                
                                
                            </table>
                         </div>
                      </div>
            
                      <div id="tab-three" class="tab-content"><!-- Begins tab-three -->
                         
                         
                      </div>
                      
                      <div id="tab-four" class="tab-content"><!-- Begins tab-one -->
                        
                     </div>
        
                     <div id="tab-five" class="tab-content"><!-- Begins tab-one -->
                        
                     </div>
        
                     <div id="tab-six" class="tab-content"><!-- Begins tab-one -->
                        
                     </div>
        
                     <div id="tab-seven" class="tab-content"><!-- Begins tab-one -->
                        
                     </div>
        
                     <div id="tab-eight" class="tab-content"><!-- Begins tab-one -->
                        
                     </div>
        
                     
        
                      
                    
                    <div style="clear:both;"></div>
                  </div><!-- # tabs-main-content -->	
        
                    
        
                </div><!-- /.col -->
            </div>
            
            {{-- {{ $balancebd }} --}}
            <!-- ------model view start-->
           

            

        </div><!-- /.col -->
    </div><!-- /.row -->

@endsection
@section('custom_style')
<link rel="stylesheet" href="{{ asset('assets') }}/css/bootstrap-datepicker3.min.css"/>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.datatables.net/1.10.23/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css">
<link href="https://cdn.datatables.net/rowreorder/1.2.7/css/rowReorder.dataTables.min.css" rel="stylesheet" type="text/css">
<link href="https://cdn.datatables.net/responsive/2.2.7/css/responsive.dataTables.min.css" rel="stylesheet" type="text/css">
<style>
    .tabs-nav-wrap {padding:10px 0; text-align: center;}
    .tabs-nav-wrap ul {display: inline-block; list-style: none;}

    .tabs-nav-wrap ul li {
    list-style: none;
    display: inline-block;

    margin:0 3px;
    }
    .tabs-nav li {
    list-style: none;
    display: block;
    padding:10px 20px;
    background: #736565;
    font-size: 16px;
    font-weight: 300;
    cursor: pointer;
    color: #fff;
    text-align: left;
    text-decoration: none;
    border:1px solid #615656;
    }

    .tabs-nav .tab-nav-link.current,
    .tabs-nav .tab-nav-link:hover {
    border:1px solid #5eaace;
    background: #74c1e4;
    }

    .tab-content {
        padding:20px 0; 
        /* text-align: center;  */
        display:none;
    }
</style>
@endsection
@section('custom_script')
{{-- <script src="https://code.jquery.com/jquery-3.5.1.js"></script> --}}
<script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/rowreorder/1.2.7/js/dataTables.rowReorder.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.7/js/dataTables.responsive.min.js"></script>
<script src="{{ asset('assets') }}/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
<script>
    // $('#view_archived_report').DataTable();
    $(document).ready(function() {
    var table = $('#example').DataTable( {
        rowReorder: {
            selector: 'td:nth-child(2)'
        },
        responsive: true
    } );
    } );
    $(document).ready(function() {
            $('.select2').select2();
    });
    $(document).ready(function () {
        $('#start').datepicker({
            autoclose: true,
            todayHighlight: true
        });
        $('#end').datepicker({
            autoclose: true,
            todayHighlight: true
        });
    });

    function downloadReport() {
            let startDate = $("#start").val();
            let endDate = $("#end").val();
            let title = $("#camp_title").val();
            let route = "{!! route('user.campaign-report.campaign-report-download') !!}?start_date="+startDate+"&end_date="+endDate+"&camp_title="+title;
            window.open(route, '_blank');
    }

    function downloadReportRoute2() {
            let startDate = $("#start").val();
            let endDate = $("#end").val();
            let title = $("#camp_title").val();
            let route = "{!! route('user.campaign-report.campaign-desktop-report-download') !!}?start_date="+startDate+"&end_date="+endDate+"&camp_title="+title;
            window.open(route, '_blank');
    }

    $(function() {
        $('.tab-content:first-child').show();
        $('.tab-nav-link').bind('click', function(e) {
            $this = $(this);
            $tabs = $this.parent().parent().next();
            $target = $($this.data("target")); // get the target from data attribute
            $this.siblings().removeClass('current');
            $target.siblings().css("display", "none")
            $this.addClass('current');
            $target.fadeIn("fast");
        
        });
        $('.tab-nav-link:first-child').trigger('click');
        });
</script>
@endsection
