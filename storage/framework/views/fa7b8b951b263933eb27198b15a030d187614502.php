<div class="footer">
	<div class="footer-inner">
		<div class="footer-content">
			<span class="bigger-120">
				<span class="blue bolder"><?php echo e(OtherHelpers::company_name()); ?></span>
				 &copy; 2019
			</span>

			&nbsp; &nbsp;
			<span class="action-buttons">
				<a href="<?php echo e(Auth::user()->userDetail->facebookid); ?>" target="_blank">
					<i class="ace-icon fa fa-facebook-square text-primary bigger-150"></i>
				</a>

			</span>
		</div>
	</div>
</div>
