<!DOCTYPE html>
<html lang="en">
<head>
	<title>{{ env('APP_NAME') }}</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->	
	<link rel="icon" type="image/png" href="{{asset('assets/maintain/images/icons/favicon.ico')}}"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{asset('assets/maintain/vendor/bootstrap/css/bootstrap.min.css')}}">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{asset('assets/maintain/fonts/font-awesome-4.7.0/css/font-awesome.min.css')}}">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{asset('assets/maintain/vendor/animate/animate.css')}}">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{asset('assets/maintain/vendor/select2/select2.min.cs')}}s">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{asset('assets/maintain/css/util.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('assets/maintain/css/main.css')}}">
<!--===============================================================================================-->
</head>
<body>
	
	
	<div class="bg-img1 size1 overlay1" style="background-image: url('assets/maintain/images/bg01.jpg');">
		<div class="size1 p-l-15 p-r-15 p-t-30 p-b-50">
			<div class="flex-w flex-sb-m p-l-75 p-r-60 p-b-80 respon1">
				<div class="wrappic1 m-r-30 m-t-10 m-b-10">
					<a href="https://iglweb.com/web/"><img src="{{('assets/maintain/images/icons/IGL_Group_logo.png')}}" alt="LOGO" height="49"></a>
				</div>

				<div class="flex-w m-t-10 m-b-10">
					<a href="https://www.facebook.com/IGLWebLtd" target="_blank" class="size4 flex-c-m how-social trans-04 m-r-5 m-b-3 m-t-3">
						<i class="fa fa-facebook"></i>
					</a>
				</div>
			</div>

			<div class="wsize1 m-lr-auto">
				<p class="txt-center l1-txt1 p-b-60">
					This Application is <span class="l1-txt2">in Maintenance Period</span>, Sorry For The Interruption!<br> We Will be back Within:
				</p>
			</div>
			<div class="flex-w flex-c-m cd100 wsize1 m-lr-auto">
				<div class="flex-col-c-m size2 bor1 m-l-10 m-r-10 m-b-15">
					<span class="l1-txt3 p-b-9 days">35</span>
					<span class="s1-txt2">Days</span>
				</div>

				<div class="flex-col-c-m size2 bor1 m-l-10 m-r-10 m-b-15">
					<span class="l1-txt3 p-b-9 hours">17</span>
					<span class="s1-txt2">Hours</span>
				</div>

				<div class="flex-col-c-m size2 bor1 m-l-10 m-r-10 m-b-15">
					<span class="l1-txt3 p-b-9 minutes">50</span>
					<span class="s1-txt2">Minutes</span>
				</div>

				<div class="flex-col-c-m size2 bor1 m-l-10 m-r-10 m-b-15">
					<span class="l1-txt3 p-b-9 seconds">39</span>
					<span class="s1-txt2">Seconds</span>
				</div>
			</div>
		</div>
	</div>



	

<!--===============================================================================================-->	
	<script src="{{asset('assets/maintain/vendor/jquery/jquery-3.2.1.min.js')}}"></script>
<!--===============================================================================================-->
	<script src="{{asset('assets/maintain/vendor/bootstrap/js/popper.js')}}"></script>
	<script src="{{asset('assets/maintain/vendor/bootstrap/js/bootstrap.min.js')}}"></script>
<!--===============================================================================================-->
	<script src="{{asset('assets/maintain/vendor/select2/select2.min.js')}}"></script>
<!--===============================================================================================-->
	<script src="{{asset('assets/maintain/vendor/countdowntime/moment.min.js')}}"></script>
	<script src="{{asset('assets/maintain/vendor/countdowntime/moment-timezone.min.js')}}"></script>
	<script src="{{asset('assets/maintain/vendor/countdowntime/moment-timezone-with-data.min.js')}}"></script>
	<script src="{{asset('assets/maintain/vendor/countdowntime/countdowntime.js')}}"></script>
	<script>
		$('.cd100').countdown100({
			/*Set Endtime here*/
			/*Endtime must be > current time*/
			endtimeYear: 0,
			endtimeMonth: 0,
			endtimeDate: 0,
			endtimeHours: 01,
			endtimeMinutes: 0,
			endtimeSeconds: 0,
			timeZone: "" 
			// ex:  timeZone: "America/New_York"
			//go to " http://momentjs.com/timezone/ " to get timezone
		});
	</script>
<!--===============================================================================================-->
	<script src="{{asset('assets/maintain/vendor/tilt/tilt.jquery.min.js')}}"></script>
	<script >
		$('.js-tilt').tilt({
			scale: 1.1
		})
	</script>
<!--===============================================================================================-->
	<script src="{{asset('assets/maintain/js/main.js')}}"></script>

</body>
</html>