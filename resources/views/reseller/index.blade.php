@extends('reseller.master')

@section('dashboard_menu_class','active')
@section('page_location')
<ul class="breadcrumb">
	<li>
		<i class="ace-icon fa fa-home home-icon"></i>
		<a href="{{ route('reseller.index') }}">Dashboard</a>
	</li>
	
</ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
<h1>
	Dashboard
	<small>
		<i class="ace-icon fa fa-angle-double-right"></i>
		overview &amp; stats
	</small>
</h1>
@endsection

@section('main_content')

	<div class="row" id="app" v-cloak>
		<div class="space-6"></div>


		<div class="vspace-12-sm"></div>

		<div class="col-sm-9">
			<div class="widget-box">

                <div class="widget-body">
                    <div class="widget-main">
                        <div id="piechart-placeholder"></div>

                        <div class="clearfix">
                            <div class="grid4">
                                <h3 style="color: rgba(0, 107, 16, 0.5);margin: 0;">@{{ last_week_sms | formatAmount}}</h3>
                                <div class="infobox-content"><h5 style="font-size: 13px;">SMS LAST WEEK</h5></div>
                            </div>

                            <div class="grid4">
                                <h3 style="color: rgba(255, 0, 0, 0.5);margin: 0;">@{{ last_week_cost | formatAmount}}</h3>
                                <div class="infobox-content"><h5 style="font-size: 13px;">COST LAST WEEK</h5></div>
                            </div>

                            <div class="grid4">
                                <h3 style="color: rgba(24, 0, 255, 0.5);margin: 0;">@{{ last_month_sms | formatAmount}}</h3>
                                <div class="infobox-content"><h5 style="text-transform: uppercase;font-size: 13px;">SMS IN @{{ current_month }}</h5></div>
                            </div>

                            <div class="grid4">
                                <h3 style="color: rgb(142, 170, 88);margin: 0;">@{{ last_month_cost | formatAmount}}</h3>
                                <div class="infobox-content"><h5 style="text-transform: uppercase;font-size: 13px;">COST IN @{{ current_month }}</h5></div>
                            </div>
                        </div>
                    </div><!-- /.widget-main -->
                </div><!-- /.widget-body -->
			</div><!-- /.widget-box -->

            <div class="hr hr32 hr-dotted"></div>
            <div class="col-sm-2" style="width: 151px;">
                <div class="widget-box transparent">
                    <div class="widget-header widget-header-flat">
                        <h4 class="widget-title lighter">
                            Month
                        </h4>
                    </div>
    
                    <div class="widget-body">
                        <div class="widget-main no-padding">
                            <table class="table table-bordered table-striped">
                                <thead class="thin-border-bottom">
                                <tr>
                                    <th>
                                        <i class="ace-icon fa fa-caret-right blue"></i>Month
                                    </th>
                                </tr>
                                </thead>
    
                                <tbody>
                                
                                    <tr v-for="monthly in monthly_sms">
                                        
                                        <td style="text-transform: uppercase;">@{{ monthly.month | formatMonth }}, @{{monthly.year}}</td>
                                    </tr>
                                
    
                                </tbody>
                            </table>
                        </div><!-- /.widget-main -->
                    </div><!-- /.widget-body -->
                </div><!-- /.widget-box -->
                </div>
            <div class="col-sm-3" style="width: 230px;">
            <div class="widget-box transparent">
                <div class="widget-header widget-header-flat">
                    <h4 class="widget-title lighter">
                        <i class="ace-icon fa fa-star orange"></i>
                        Your SMS History
                    </h4>
                </div>

                <div class="widget-body">
                    <div class="widget-main no-padding">
                        <table class="table table-bordered table-striped">
                            <thead class="thin-border-bottom">
                            <tr>
                                <th>
                                    <i class="ace-icon fa fa-caret-right blue"></i>Cost
                                </th>

                                <th class="">
                                    <i class="ace-icon fa fa-caret-right blue"></i>SMS Count
                                </th>

                            </tr>
                            </thead>

                            <tbody>
                            
                                <tr v-for="monthly in monthly_sms">
                                    

                                    <td class="hidden-480">
                                        <span class="label label-info arrowed-right arrowed-in">@{{ sms = monthly.total_sms_cost | formatAmount}}</span>
                                    </td>

                                    <td class="hidden-480">
                                        <span class="label label-info arrowed-right arrowed-in">@{{ monthly.total_sms | formatAmount}}</span>
                                    </td>
                                </tr>
                            

                            </tbody>
                        </table>
                    </div><!-- /.widget-main -->
                </div><!-- /.widget-body -->
            </div><!-- /.widget-box -->
            </div>

            <div class="col-sm-3" style="width: 230px;">
                <div class="widget-box transparent">
                    <div class="widget-header widget-header-flat">
                        <h4 class="widget-title lighter">
                            <i class="ace-icon fa fa-star orange"></i>
                            Your Flexi History
                        </h4>
    
                        {{-- <div class="widget-toolbar">
                            <a href="#" data-action="collapse">
                                <i class="ace-icon fa fa-chevron-up"></i>
                            </a>
                        </div> --}}
                    </div>
    
                    <div class="widget-body">
                        <div class="widget-main no-padding">
                            <table class="table table-bordered table-striped">
                                <thead class="thin-border-bottom">
                                <tr>
                                    <th>
                                        <i class="ace-icon fa fa-caret-right blue"></i>Cost
                                    </th>
    
                                    <th class="">
                                        <i class="ace-icon fa fa-caret-right blue"></i>Number
                                    </th>
    
                                </tr>
                                </thead>
    
                                <tbody>
                                
                                    <tr v-for="monthly in monthly_flexiload">
                                        <td class="hidden-480">
                                            <span class="label label-info arrowed-right arrowed-in">@{{ flexi = monthly.total_flexi_amount | formatAmount}}</span>
                                        </td>
    
                                        <td class="hidden-480">
                                            <span class="label label-info arrowed-right arrowed-in">@{{ monthly.total_flexi_number | formatAmount}}</span>
                                        </td>
                                    </tr>
                                
    
                                </tbody>
                            </table>
                        </div><!-- /.widget-main -->
                    </div><!-- /.widget-body -->
                </div><!-- /.widget-box -->
                </div>
                
                
        </div><!-- /.col -->

        <div class="col-sm-3">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 dashboard-leftBalance">
                        <h3>STATISTICS</h3><hr>

                        <p>Balance BDT: @{{ balance_bd | formatAmount}}
                                    <b style="font-size: 15px;">৳</b></span> 
                        </p>

                                    <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th colspan="3" class="text-center">SMS Credit</th>
                                </tr>
                                <tr>
                                    <th>Operator</th>
                                    <th>Masking</th>
                                    <th>Non Masking</th>
                                </tr>
                            </thead>
                            <tbody>
                                    <tr v-for="credit in sms_credit">
                                        <td>@{{ credit.operator.ope_operator_name }}</td>
                                        <td>@{{ (balance_bd/credit.asr_masking).toFixed() }}</td>
                                        <td>@{{ (balance_bd/credit.asr_nonmasking).toFixed() }}</td>
                                    </tr>
                            </tbody>
                        </table>

                        <h3>LAST 5 TRANSACTIONS</h3>
                        <ul>   
                                
                                <li v-for="tran in transaction">
                                    @{{ tran.created_at | formatDate }},@{{ (tran.asb_credit - tran.asb_debit) | formatAmount }} ৳ - @{{ tran.asb_pay_ref }}
                                </li>                     
                        </ul>
                    </div>

                </div>
	</div><!-- /.row -->

	

    

@endsection
@section('custom_style')
    <style>
        [v-cloak] { display: none; }
        .label {
            display: unset;
        }
    </style>
    
@endsection
@section('custom_script')

<!-- <script src="https://cdn.jsdelivr.net/npm/vue"></script> -->
<script type="text/javascript" src="{{ asset('assets/vue/vue.min.js') }}"></script>
<script src="{{ asset('assets/moment/moment.js') }}"></script>
<script>
   Vue.filter('formatDate', function(value) {
    if (value) {
        return moment(String(value)).format('MMM D,yyyy');
    }
    });
    Vue.filter('formatMonth', function(value) {
        if (value) {
            return moment(String(value)).format('MMMM');
        }
    });
    Vue.filter('formatAmount', function(value) {
        if (value) {
            return new Intl.NumberFormat().format(value);
        }
    });
    let app = new Vue({
        el: '#app',
        data: {
            balance_bd: {!! json_encode($balance_bd) !!},
            sms_credit: {!! json_encode($data['sms_credit']) !!},
            transaction: {!! json_encode($data['transactions']) !!},
            monthly_sms: {!! json_encode($data['monthly_sms']) !!},
            monthly_flexiload: {!! json_encode($data['monthly_flexiload']) !!},
            last_week_sms: {!! json_encode($data['last_week_sms']) !!},
            last_week_cost: {!! json_encode($data['last_week_cost']) !!},
            last_month_sms: {!! json_encode($data['last_month_sms']) !!},
            last_month_cost: {!! json_encode($data['last_month_cost']) !!},
        },
        computed: {
            current_month: function(){
                var d = new Date();
                var month = new Array();
                month[0] = "January";
                month[1] = "February";
                month[2] = "March";
                month[3] = "April";
                month[4] = "May";
                month[5] = "June";
                month[6] = "July";
                month[7] = "August";
                month[8] = "September";
                month[9] = "October";
                month[10] = "November";
                month[11] = "December";
                var name = month[d.getMonth()];
                return name;
            }
        }
    })
</script>

@endsection
