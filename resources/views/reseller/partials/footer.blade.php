<div class="footer">
	<div class="footer-inner">
		<div class="footer-content">
			<span class="bigger-120">
				<span class="blue bolder">{{ OtherHelpers::user_creator_info('company_name') }}</span>
				 &copy; {{ \Carbon\Carbon::now()->format('Y') }}
			</span>

			&nbsp; &nbsp;
			<span class="action-buttons">
				<a href="{{ OtherHelpers::user_creator_info('fb_id') }}" target="_blank">
					<i class="ace-icon fa fa-facebook-square text-primary bigger-150"></i>
				</a>
			</span>
		</div>
	</div>
</div>
