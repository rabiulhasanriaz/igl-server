<?php $__env->startSection('api_menu','open'); ?>
<?php $__env->startSection('flexiload_api_progress_menu_class','active'); ?>

<?php $__env->startSection('page_location'); ?>
<ul class="breadcrumb">
	<li>
		<i class="ace-icon fa fa-home home-icon"></i>
		<a href="<?php echo e(route('user.index')); ?>">Dashboard</a>
	</li>
	<li class="active">Application Programming Interface</li>
</ul><!-- /.breadcrumb -->
<?php $__env->stopSection(); ?>


<?php $__env->startSection('page_header'); ?>
<h1>
	Api
	<small>
		<i class="ace-icon fa fa-angle-double-right"></i>
		Flexiload Api Details
	</small>
</h1>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('main_content'); ?>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

		<?php echo $__env->make('user.partials.session_messages', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

		<form action="<?php echo e(route('user.flexiload.change-api')); ?>" method="POST" enctype="multipart/form-data">
			<?php echo csrf_field(); ?>
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 api-urltitle">
				<h3>Flexiload API</h3>
				<span>Your API Key : <b> <?php echo e(Auth::user()->userDetail->flexi_api_key); ?> </b></span> &nbsp;&nbsp;<button class="btn btn-sm btn-default btn-danger" name="apikey_create">Change API Key</button>
			</div><br>
			<div class="space-10"></div>
		</form>

		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 api-urltitle-contain">
            <table class="table">
                <tr>
                    <td style="width: 155px;"><strong>API URL (GET & POST) : </strong></td>
                    <td><span>http://<?php echo e($domain_url); ?>/api/v1/send-load?api_key=(API KEY)&pin=(Your Secret Pin)&number=(NUMBER)&amount=(Amount)&number_type=(Prepaid/Postpaid)&operator=(Number Operator)</span></td>
                </tr>
            </table>


		</div>
	</div>

	<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
		<h4>Details of API Data</h4>
		<table class="table table-responsive table-bordered" id="">
			<thead>
			<tr>
				<th>Parameter Name</th>
				<th>Meaning/Value</th>
				<th>Description</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>api_key</td>
				<td>API Key</td>
				<td>Your API Key (<?php echo e(Auth::user()->userDetail->flexi_api_key); ?>)</td>
			</tr>
			<tr>
				<td>pin</td>
				<td>Your Secret Pin</td>
				<td>Exp: ****</td>
			</tr>
			<tr>
				<td>number</td>
				<td>Mobile Number</td>
				<td>Exp: 88017XXXXXXXX</td>
			</tr>
			<tr>
				<td>amount</td>
				<td>Amount</td>
				<td>
                    Min-Amount : 10, <br>
                    Max-Amount : 50000
                </td>
			</tr>
			<tr>
				<td>number_type</td>
				<td>Number Type</td>
				<td>
                    1 = Prepaid, <br>
                    2 = Postpaid
                </td>
			</tr>
			<tr>
				<td>operator</td>
				<td>Number Operator</td>
				<td>
                    gp = Grameen, <br>
                    gpst = Grameen-Skitto, <br>
                    blink = Banglalink ,<br>
                    robi = Robi ,<br>
                    airtel = Airtel ,<br>
                    teletalk = Teletalk
                </td>
			</tr>
		</tbody>
		</table>
	</div>

	<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
		<h4>Error Information</h4>
		<table class="table table-responsive table-bordered">

			<tr>
				<th>API Response Code</th>
				<th>Description</th>
			</tr>

			<tr>
				<td>445900</td>
				<td>Load Request Received Successfully</td>
			</tr>
			<tr>
				<td>445901</td>
				<td>Missing api key</td>
			</tr>
			<tr>
				<td>445902</td>
				<td>Missing phone number</td>
			</tr>
			<tr>
				<td>445903</td>
				<td>Missing number type</td>
			</tr>
			<tr>
				<td>445904</td>
				<td>Missing operator</td>
			</tr>
			<tr>
				<td>445905</td>
				<td>Missing flexipin</td>
			</tr>
			<tr>
				<td>445906</td>
				<td>Missing Amount</td>
			</tr>
			<tr>
				<td>445907</td>
				<td>Amount should be in 10 to 50000</td>
			</tr>
			<tr>
				<td>445908</td>
				<td>Invalid amount</td>
			</tr>
			<tr>
				<td>445909</td>
				<td>Invalid operator</td>
			</tr>
			<tr>
				<td>445910</td>
				<td>Invalid number</td>
			</tr>
			<tr>
				<td>445911</td>
				<td>Invalid api key</td>
			</tr>
			<tr>
				<td>445912</td>
				<td>Invalid flexipin</td>
			</tr>
			<tr>
				<td>445913</td>
				<td>Insufficient balance</td>
			</tr>
			<tr>
				<td>445914</td>
				<td>Insufficient reseller balance</td>
			</tr>
			<tr>
				<td>445915</td>
				<td>something went wrong / server error</td>
			</tr>

		</table>
	</div>


</div><!-- /.row -->

<?php $__env->stopSection(); ?>
<?php $__env->startSection('custom_style'); ?>
<link href="<?php echo e(asset('assets/datatable/jquery.dataTables.min.css')); ?>" rel="stylesheet" type="text/css">
    <link href="<?php echo e(asset('assets/datatable/rowReorder.dataTables.min.css')); ?>" rel="stylesheet" type="text/css">
    <link href="<?php echo e(asset('assets/datatable/responsive.dataTables.min.css')); ?>" rel="stylesheet" type="text/css">
	<style>
		#view_archived_report_length{
			display: none;
		}
		#view_archived_report_filter{
			display: none;
		}
		#view_archived_report_info{
			display: none;
		}
		#view_archived_report_paginate{
			display: none;
		}
	</style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('custom_script'); ?>
<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/rowreorder/1.2.6/js/dataTables.rowReorder.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
<script type="text/javascript">
	// $('#reseller_list').DataTable();
	$(document).ready(function() {
	var table = $('#view_archived_report').DataTable( {
		responsive: true,
		
	} );
} );
</script>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('user.master', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>