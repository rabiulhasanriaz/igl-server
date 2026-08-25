<?php $__env->startSection('api_menu','open'); ?>
<?php $__env->startSection('api_progress_menu_class','active'); ?>
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
		Api Details
	</small>
</h1>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('main_content'); ?>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

		<?php echo $__env->make('user.partials.session_messages', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

		
		<div class="widget-box" style="margin-bottom: 20px; background: #fff; border: 1px solid #ccc; border-radius: 4px;">
			<div class="widget-header" style="background: #f5f5f5; padding: 10px 15px; border-bottom: 1px solid #ddd; border-radius: 4px 4px 0 0;">
				<h4 class="widget-title" style="margin: 0; font-size: 16px;">
					<i class="ace-icon fa fa-shield"></i> White Listed IP Settings
				</h4>
				
			</div>

			<div class="widget-body" style="padding: 15px;">
				<?php if(Auth::user()->userDetail->white_listed_ip): ?>
					<div class="alert alert-info" style="margin-bottom: 0;">
						<i class="ace-icon fa fa-info-circle"></i>
						<strong>Current White Listed IP:</strong> 
						<code><?php echo e(Auth::user()->userDetail->white_listed_ip); ?></code>
						<br>
						<small>Only requests from this IP address will be accepted by the API.</small>
					</div>
				<?php else: ?>
					<div class="alert alert-warning" style="margin-bottom: 0;">
						<i class="ace-icon fa fa-warning"></i>
						<strong>No IP White Listed:</strong> 
						API is accessible from all IP addresses.
						<br>
						<small>For security, consider restricting access to specific IP addresses.</small>
					</div>
				<?php endif; ?>
			</div>
		</div>

		
		<form action="<?php echo e(url('/developerApi/change')); ?>" method="POST" enctype="multipart/form-data">
			<?php echo csrf_field(); ?>
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 api-urltitle">
				<h3>Text SMS API</h3>
				<span>Your API Key : <b> <?php echo e(Auth::user()->userDetail->api_key); ?> </b></span> &nbsp;&nbsp;<button class="btn btn-sm btn-default btn-danger" name="apikey_create">Change API Key</button>
			</div><br>
			<div class="space-10"></div>
		</form>

		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 api-urltitle-contain">
			 <strong>API URL (GET & POST) : </strong>
			 <span>http://<?php echo e($domain_url); ?>/api/v1/send?api_key=(API KEY)&contacts=(NUMBER)&senderid=(Approved Sender ID)&msg=(Message Content)</span>
		</div>
	</div>

	<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
		<h4>Credit Balance API</h4>
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
					<td class="abcd">Your API Key (<?php echo e(Auth::user()->userDetail->api_key); ?>)</td>
				</tr>
				<tr>
					<td>contacts</td>
					<td>mobile number</td>
					<td>Exp: 88017XXXXXXXX,88018XXXXXXXX,88019XXXXXXXX...</td>
				</tr>
				<tr>
					<td>msg</td>
					<td>SMS body</td>
					<td class="text-danger">N.B: Please use url encoding to send some special characters like &, $, @ etc</td>
				</tr>
			</tbody>
		</table>
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<h3>Credit Balance API</h3>
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 api-urltitle-bottom">
				<br>
				<strong>API URL :</strong>  http://<?php echo e($domain_url); ?>/api/v1/balance?api_key=(API KEY) <br>
				<strong>API KEY :</strong>  Your API Key (<b> <?php echo e(Auth::user()->userDetail->api_key); ?> </b>)
			</div>
		</div><!-- /.col -->
	</div>

	<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
		<h4>Error Information</h4>
		<table class="table table-responsive table-bordered">
			<tr>
				<th>API Response Code</th>
				<th>Description</th>
			</tr>
			<tr>
				<td>445000</td>
				<td>Message Sent successfully</td>
			</tr>
			<tr>
				<td>445010</td>
				<td>missing api key</td>
			</tr>
			<tr>
				<td>445020</td>
				<td>missing contact number</td>
			</tr>
			<tr>
				<td>445030</td>
				<td>missing sender id</td>
			</tr>
			<tr>
				<td>445040</td>
				<td>Invalid api key</td>
			</tr>
			<tr>
				<td>445050</td>
				<td>Your account was suspended</td>
			</tr>
			<tr>
				<td>445060</td>
				<td>Your account was expired</td>
			</tr>
			<tr>
				<td>445070</td>
				<td>Only a user can send sms</td>
			</tr>
			<tr>
				<td>445080</td>
				<td>Invalid sender id</td>
			</tr>
			<tr>
				<td>445090</td>
				<td>You have no access to this sender id</td>
			</tr>
			<tr>
				<td>445100</td>
				<td>Access Denied,Your IP is not whitelisted</td>
			</tr>
			<tr>
				<td>445110</td>
				<td>All numbers are invalid</td>
			</tr>
			<tr>
				<td>445120</td>
				<td>insufficient balance</td>
			</tr>
			<tr>
				<td>445130</td>
				<td>reseller insufficient balance</td>
			</tr>
			<tr>
				<td>445170</td>
				<td>You are not a user</td>
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
	$(document).ready(function() {
		var table = $('#view_archived_report').DataTable( {
			responsive: true,
		} );
	} );
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('user.master', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>