@extends('user.master')

@section('load_menu_class','open')
@section('submenu_load','open')
@section('load_make_menu_class','active')

@section('page_location')
<ul class="breadcrumb">
	<li>
		<i class="ace-icon fa fa-home home-icon"></i>
		<a href="{{ route('user.index') }}">Dashboard</a>
	</li>
	<li class="active">Flexiload</li>
</ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
<h1>
	Flexiload
	<small>
		<i class="ace-icon fa fa-angle-double-right"></i>
		Make a load
	</small>
</h1>
@endsection


@section('main_content')
	
	<!-- Buy a package Modal -->
	<div class="modal fade" id="buyPackageModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	        <h2 class="modal-title" id="package_title"></h2>
	      </div>
	      <form action="{{ route('user.create') }}" method="post" onsubmit="return checkForm(this);">
	      	@csrf
	      		<div class="modal-body">
	      		  <p class="h4 text-center text-primary" id="package_acknowledgement">
	      		  	<span style="border-bottom: 1px solid lightgray; padding: 0px; margin: 0px 0px 10px 0px;"></span>
	      		  </p>
	      		  <input type="number" name="package_id" hidden> 

				  <div class="form-group">
				  	<p class="" for="number_type">Select number type</p>
				  	<div class="form-group">
				  		<label class="radio-inline"><input type="radio" value="1" name="number_type" required>Prepaid</label>
				  		<label class="radio-inline"><input type="radio" value="2" name="number_type" required>PostPaid</label>
				  	</div>
				  </div>
					
				  <div class="form-group">
				  	<label class="" for="campaign_name">Campaign name</label>
				  	<input type="text" name="campaign_name" id="campaign_name" class="form-control input-sm" placeholder="Campaign Name">
				  </div>

				  <div class="form-group">
				  	<label class="" for="owner_name">Number Owner Name</label>
				  	<input type="text" name="owner_name" id="owner_name" class="form-control input-sm" placeholder="Number Owner name">
				  </div>

	      		  <div class="form-group">
	      		  	<label class="" for="targeted_number">Mobile Number</label>
	      		  	<input type="text" id="targeted_number" name="targeted_number" class="form-control input-sm" placeholder="01xxxxxxxxx" required>
	      		  </div>

				<div class="form-group">
					<label for="edit_operator">Operator </label>
					<select name="operator" id="edit_operator" class="form-control">
						<option value="">Select One</option>
						<option value="gp">Grameen</option>
						<option value="blink">Banglalink</option>
						<option value="airtel">Airtel</option>
						<option value="robi">Robi</option>
						<option value="teletalk">Teletalk</option>
						<option value="gpst">GP Skitto</option>
					</select>
                </div>

	      		  <div class="form-group">
	      		  	<label class="" for="remarks">Remarks</label>
	      		  	<input type="text" id="remarks" name="remarks" class="form-control input-sm" placeholder="remarks">
	      		  </div>

	      		  <div class="form-group">
	      		  	<label class="" for="flexipin">Your flexiload pin</label>
	      		  	<input type="password" id="flexipin" name="flexipin" class="form-control input-sm" placeholder="your secret flexiload pin">
	      		  </div>
	      		</div>
	      		<div class="modal-footer">
	      		  <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
	      		  <input type="submit" class="btn btn-primary btn-sm" value="Confirm" name="myButton">
	      		</div>
	      </form>
	    </div>
	  </div>
	</div>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		@include('user.partials.session_messages')
		<div class="row">
            <ul class="nav nav-tabs padding-18 tab-size-bigger text-center" id="myTab">
				
				 <li class="in active" id="airtel_btn">
                    <a data-toggle="tab" href="#airtel" onclick="show_packages(1)" >
                        <i class="ace-icon"><img src="{{ asset('assets/images/operator_icon') }}/airtel.png" style="width: 15px; height: 15px;"> </i>
                         Airtel
                    </a>
                    
                </li>

                <li class="" id="banglalink_btn">
                    <a data-toggle="tab" href="#banglalink" onclick="show_packages(2)" >
                        <i class="ace-icon"><img src="{{ asset('assets/images/operator_icon') }}/banglalink.png" style="width: 15px; height: 15px;"> </i>
                        Banglalink
                    </a>
                </li>

                <li class="" id="grameen_btn">
                    <a data-toggle="tab" href="#grameen" onclick="show_packages(3)" >
                        <i class="ace-icon"><img src="{{ asset('assets/images/operator_icon') }}/gp.png" style="width: 15px; height: 15px;"> </i>
                        Grameen
                    </a>
                    
                </li>

                <li class="" id="robi_btn">
                    <a data-toggle="tab" href="#robi" onclick="show_packages(4)" >
                        <i class="ace-icon"><img src="{{ asset('assets/images/operator_icon') }}/robi.png" style="width: 15px; height: 15px;"> </i>
                        Robi
                    </a>
                   
                </li>

               
                <li class="" id="teletalk_btn">
                    <a data-toggle="tab" href="#teletalk" onclick="show_packages(5)" >
                        <i class="ace-icon"><img src="{{ asset('assets/images/operator_icon') }}/teletalk.png" style="width: 15px; height: 15px;"> </i>
                        Teletalk
                    </a>
                    
                </li>
            </ul>
        </div>
	</div>
	<div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ">
                <!-- PAGE CONTENT BEGINSInclude('user.messaging.smspart.send_sms_content') -->
                <div class="tab-content" style="padding: 0px;">
					
					<div id="airtel" class="tab-pane fade in active">
						<div class="row">
				            <ul class="nav nav-tabs padding-18 tab-size-bigger text-center" id="myTab1">
								<li class="">
				                    <a data-toggle="tab" href="#airtel_minute" onclick="show_packages(1, 1)" >
				                         Minute
				                    </a>				                
				                </li>
				                <li  class="">
				                    <a data-toggle="tab" href="#airtel_sms" onclick="show_packages(1, 2)" >
				                        SMS
				                    </a>
				                </li>
				                <li class="">
				                    <a data-toggle="tab" href="#airtel_data" onclick="show_packages(1, 3)" >
				                        Data
				                    </a>
				                </li>
				                <li class="">
				                    <a data-toggle="tab" href="#airtel_combo" onclick="show_packages(1, 4)" >
				                        Combo
				                    </a>
				                </li>
				            </ul>
				        </div>
							
                    </div>

                    <div id="banglalink" class="tab-pane fade">
                    	<div class="row">
				            <ul class="nav nav-tabs padding-18 tab-size-bigger text-center" id="myTab2">
								<li class="">
				                    <a data-toggle="tab" href="#banglalink_minute" onclick="show_packages(2, 1)" >
				                         Minute
				                    </a>				                
				                </li>
				                <li class="">
				                    <a data-toggle="tab" href="#banglalink_sms" onclick="show_packages(2, 2)" >
				                        SMS
				                    </a>
				                </li>
				                <li class="">
				                    <a data-toggle="tab" href="#banglalink_data" onclick="show_packages(2, 3)" >
				                        Data
				                    </a>
				                </li>
				                <li class="">
				                    <a data-toggle="tab" href="#banglalink_combo" onclick="show_packages(2, 4)" >
				                        Combo
				                    </a>
				                </li>
				            </ul>
				        </div>
                    </div>

                    <div id="grameen" class="tab-pane fade">
                    	<div class="row">
				            <ul class="nav nav-tabs padding-18 tab-size-bigger text-center" id="myTab3">
								 <li class="">
				                    <a data-toggle="tab" href="#grameen_minute" onclick="show_packages(3, 1)" >
				                         Minute
				                    </a>				                
				                </li>
				                <li class="">
				                    <a data-toggle="tab" href="#grameen_sms" onclick="show_packages(3, 2)" >
				                        SMS
				                    </a>
				                </li>
				                <li class="">
				                    <a data-toggle="tab" href="#grameen_data" onclick="show_packages(3, 3)" >
				                        Data
				                    </a>
				                </li>
				                <li class="">
				                    <a data-toggle="tab" href="#grameen_combo" onclick="show_packages(3, 4)" >
				                        Combo
				                    </a>
				                </li>
				            </ul>
				        </div>
                    </div>

                    <div id="robi" class="tab-pane fade">
                    	<div class="row">
				            <ul class="nav nav-tabs padding-18 tab-size-bigger text-center" id="myTab4">
								 <li class="">
				                    <a data-toggle="tab" href="#robi_minute" onclick="show_packages(4, 1)" >
				                         Minute
				                    </a>				                
				                </li>
				                <li class="">
				                    <a data-toggle="tab" href="#robi_sms" onclick="show_packages(4, 2)" >
				                        SMS
				                    </a>
				                </li>
				                <li class="">
				                    <a data-toggle="tab" href="#robi_data" onclick="show_packages(4, 3)" >
				                        Data
				                    </a>
				                </li>
				                <li class="">
				                    <a data-toggle="tab" href="#robi_combo" onclick="show_packages(4, 4)" >
				                        Combo
				                    </a>
				                </li>
				            </ul>
				        </div>
                    </div>

                    <div id="teletalk" class="tab-pane fade">
                    	<div class="row">
				            <ul class="nav nav-tabs padding-18 tab-size-bigger text-center" id="myTab5">
								 <li class="">
				                    <a data-toggle="tab" href="#teletalk_minute" onclick="show_packages(5, 1)" >
				                         Minute
				                    </a>				                
				                </li>
				                <li class="">
				                    <a data-toggle="tab" href="#teletalk_sms" onclick="show_packages(5, 2)" >
				                        SMS
				                    </a>
				                </li>
				                <li class="">
				                    <a data-toggle="tab" href="#teletalk_data" onclick="show_packages(5, 3)" >
				                        Data
				                    </a>
				                </li>
				                <li class="">
				                    <a data-toggle="tab" href="#teletalk_combo" onclick="show_packages(5, 4)" >
				                        Combo
				                    </a>
				                </li>
				            </ul>
				        </div>
                    </div>
                </div>

                <table class="table table-striped table-sm" id="packages_table">
					<thead>
						<th>SL</th>
						<th>Price</th>
						<th>Details</th>
						<th>Validity</th>
						<th>Action</th>
					</thead>
					<tbody>
					
					</tbody>
				</table>
            </div>
        </div><!-- /.col -->
    </div><!-- /.row -->
</div><!-- /.row -->
@endsection

@section('custom_script')

	<script type="text/javascript">
		$('form').submit(function() {
		  $(this).find("input[type='submit']").prop('disabled',true);
		});
		$( document ).ready(function() {
		    show_packages(1);
		});


		function buyPackage(id, price, details)
		{
			$("#package_acknowledgement").text('Are you sure to pay '+price+' Tk ? ');
			$("input[name='package_id']").val(id);
			$("input[name='package_details']").val(details);

			$("#buyPackageModal").modal();
		}	

		function show_packages(operator, type=null) {
			var form_route = "{{ route('user.show-packages-by-ajax') }}";
		    $.ajaxSetup({
		        headers: {
		            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		        }
		    });

		    $.ajax({
		        type: 'post',
		        url: form_route,
		        data: {operator: operator, type: type},
		        beforeSend: function () {
		            $("#loading").show();
		        },
		        success: function (data) {
		        	if (data.code == 400) {
		        		alert('data missing');
		        	} else if (data.code == 401) {
		        		alert('something went wrong.');
		        	} else {
		        		$('#packages_table tbody').html(data);
		        	}
		        },
		        complete: function () {
		            $("#loading").hide();
		        },
		    });
		}

		$("#airtel_btn").click(function () {
			remove_all_child_active_tab();
		});
		$("#banglalink_btn").click(function () {
			remove_all_child_active_tab();
		});
		$("#grameen_btn").click(function () {
			remove_all_child_active_tab();
		});
		$("#robi_btn").click(function () {
			remove_all_child_active_tab();
		});
		$("#teletalk_btn").click(function () {
			remove_all_child_active_tab();
		});
		function remove_all_child_active_tab() {
			$("#airtel li").removeClass("active");
			$("#banglalink li").removeClass("active");
			$("#grameen li").removeClass("active");
			$("#robi li").removeClass("active");
			$("#teletalk li").removeClass("active");
		}
	</script>
@endsection

@section('custom_style')
	
	<style type="text/css">

		#airtel li a {
 		   background-color: #eb1a23;
 		   color: #fff;
		}	
		#banglalink li a {
		    background-color: #924622;
		    color: #fff;
		}
		#grameen li a {
		    background-color: #05acee;
		    color: #fff;
		}	
		#robi li a {
		    background-color: #f25a60;
		    color: #fff;
		}	
		#teletalk li a {
		    background-color: #8bc8a8;
		    color: #fff;
		}	
	</style>
@endsection