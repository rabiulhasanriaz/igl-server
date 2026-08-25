<div class="footer">
	<div class="footer-inner">
		<div class="footer-content">
			<span class="bigger-120">
				<span class="blue bolder">
					{{ App\Model\User::where('id', Auth::guard('employee')->user()->create_by)->first()->company_name }}
				</span>
				 &copy; 2019
			</span>

			&nbsp; &nbsp;
			<span class="action-buttons">
				<a href="{{ App\Model\User::where('id', Auth::guard('employee')->user()->create_by)->first()->userDetail['facebookid'] }}" target="_blank">
					<i class="ace-icon fa fa-facebook-square text-primary bigger-150"></i>
				</a>

			</span>
		</div>
	</div>
</div>
