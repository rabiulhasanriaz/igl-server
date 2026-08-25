@extends('admin.master')

@section('sms_flexi_report_class','open')
@section('operator_class','active')

@section('page_location')
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="{{ route('admin.index') }}">Dashboard</a>
        </li>
        <li class="active">{{ $days }} Days Reports</li>
    </ul><!-- /.breadcrumb -->
@endsection

@section('main_content')

    <div class="space-6"></div>
    <form class="form-inline" action="" method="get">
        <input type="hidden" name="_token" value="" id="_token">
        <div class="form-group">
          <label for="email">Starting Date</label>
          <input type="text" value="{{ $start }}" data-date-format="yyyy-mm-dd" name="start_date" class="form-control" id="start">
        </div>
        <div class="form-group">
          <label for="pwd">Ending Date</label>
          <input type="text" value="{{ $end }}" data-date-format="yyyy-mm-dd" name="end_date" class="form-control" id="end">
        </div>
        <button type="submit" class="btn btn-info btn-sm" name="searchbtn">Search</button>
    </form>

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

            @include('admin.partials.session_messages')
            @include('admin.partials.all_error_messages')
            <div class="tabs-nav-wrap">
                <ul class="tabs-nav">
                    <li class="tab-nav-link" data-target="#tab-one">Masking/Non-Masking</li>
                    <li class="tab-nav-link" data-target="#tab-two">Flexiload</li>		
                 </ul>
               <div style="clear:both;"></div>
             </div><!-- ends tabs-nav-wrap -->	  
  
  
	     <div class="tabs-main-content">
		 
              <div id="tab-one" class="tab-content"><!-- Begins tab-one -->
                 <div class="tab-inner">
                    <div class="row">
                        <div class="col-sm-6">
                            <ul class="list-group">
                                <li class="list-group-item active text-center">Last {{ $days }}Days Masking SMS Reports</li>
                            </ul>
                            <table id="masking" class="display nowrap" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Sl</th>
                                        <th>Operator</th>
                                        <th>Total Masking SMS</th>
                                        <th>Total Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php($sl=0)
                                    @php($total_op_sms=0)
                                    @php($total_op_cost=0)
                                    @foreach ($sms_report as $sms)
                                    @php($total_op_sms += $sms->total)
                                    @php($total_op_cost += $sms->total_cost)
                                    <tr>
                                        <td>{{ ++$sl }}</td>
                                        <td title="">{{ $sms->operator->ope_operator_name }}</td>
                                        <td class="text-center">{{ $sms->total }}</td>
                                        <td class="text-right">{{ number_format($sms->total_cost,2) }}</td>
                                    </tr>  
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2">Total</th>
                                        <th class="text-center">{{ $total_op_sms }}</th>
                                        <th class="text-right">{{ number_format($total_op_cost,2) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="col-sm-6">
                            <ul class="list-group">
                                <li class="list-group-item active text-center">Last {{ $days }}Days Non-Maskings SMS Reports</li>
                            </ul>
                            <table id="non-masking" class="display nowrap" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Sl</th>
                                        <th>Operator</th>
                                        <th>Total Non-Masking SMS</th>
                                        <th>Total Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php($sl=0)
                                    @php($total_op_sms=0)
                                    @php($total_op_cost=0)
                                    @foreach ($nonMaskingReport as $sms)
                                    @php($total_op_sms += $sms->total)
                                    @php($total_op_cost += $sms->total_cost)
                                    <tr>
                                        <td>{{ ++$sl }}</td>
                                        <td title="">
                                            @if ($sms->sci_sender_operator == 1)
                                                Robi/Airtel Non-Masking
                                            @elseif($sms->sci_sender_operator == 2)
                                                GP Non-Masking
                                            @elseif($sms->sci_sender_operator == 3)
                                                Banglalink Non-Masking
                                            @elseif($sms->sci_sender_operator == 4)
                                                Teletalk Non-Masking
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $sms->total }}</td>
                                        <td class="text-right">{{ number_format($sms->total_cost,2) }}</td>
                                    </tr>  
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2">Total</th>
                                        <th class="text-center">{{ $total_op_sms }}</th>
                                        <th class="text-right">{{ number_format($total_op_cost,2) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
			     </div>
              </div>
      
              <div id="tab-two" class="tab-content"><!-- Begins tab-two -->
                 <div class="tab-inner">
                    <div class="row">
                        <div class="col-sm-6">
                            <ul class="list-group">
                                <li class="list-group-item active text-center">Last {{ $days }}Days Flexi Reports</li>
                            </ul>
                            <table id="flexi" class="display nowrap" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Sl</th>
                                        <th>CellPhone</th>
                                        <th>Total Number</th>
                                        <th>Total Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   @php($sl=0)
                                   @php($total_op_flexi=0)
                                   @php($total_op_cost=0)
                                   @foreach ($flexi_report as $flexi)
                                   @php($total_op_flexi += $flexi->total)
                                   @php($total_op_cost += $flexi->total_cost)
                                   <tr>
                                        <td>{{ ++$sl }}</td>
                                        <td title="">
                                            @if ($flexi->operator_id == 'airtel')
                                                Airtel
                                            @elseif($flexi->operator_id == 'blink')
                                                Banglalink
                                            @elseif($flexi->operator_id == 'gp')
                                                GrameenPhone
                                            @elseif($flexi->operator_id == 'gpst')
                                                Skitto
                                            @elseif($flexi->operator_id == 'robi')
                                                Robi
                                            @elseif($flexi->operator_id == 'teletalk')
                                                Teletalk 
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $flexi->total }}</td>
                                        <td class="text-right">{{ number_format($flexi->total_cost,2) }}</td>
                                    </tr>
                                   @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2">Total</th>
                                        <th class="text-center">{{ $total_op_flexi }}</th>
                                        <th class="text-right">{{ number_format($total_op_cost,2) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
			     </div>
              </div>
	        <div style="clear:both;"></div>
	      </div><!-- # tabs-main-content -->	

            

        </div><!-- /.col -->
    </div><!-- /.row -->


@endsection
@section('custom_style')
<link rel="stylesheet" href="{{ asset('assets') }}/css/bootstrap-datepicker3.min.css"/>
<link href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" rel="stylesheet" />
<link href="https://cdn.datatables.net/rowreorder/1.2.7/css/rowReorder.dataTables.min.css" rel="stylesheet" />
<link href="https://cdn.datatables.net/responsive/2.2.5/css/responsive.dataTables.min.css" rel="stylesheet" />
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
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/rowreorder/1.2.7/js/dataTables.rowReorder.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.5/js/dataTables.responsive.min.js"></script>
<script src="{{ asset('assets') }}/js/bootstrap-datepicker.min.js"></script>
    
    <script type="text/javascript">
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

     $(document).ready(function() {
            var table = $('#masking').DataTable( {
                
                responsive: true
            } );
        } );
        $(document).ready(function() {
            var table = $('#non-masking').DataTable( {
                
                responsive: true
            } );
        } );
        $(document).ready(function() {
            var table = $('#flexi').DataTable( {
                
                responsive: true
            } );
        } );
        function submitLimitForm(formName){
            if(confirm('Are you Sure')) {
                $("#" + formName).submit();
            }
        }

        function copy(that){
        var inp =document.createElement('input');
        document.body.appendChild(inp);
        inp.value =that;
        inp.select();
        document.execCommand('copy',false);
        inp.remove();
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
