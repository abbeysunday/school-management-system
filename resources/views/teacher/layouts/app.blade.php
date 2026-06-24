<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Truelysell</title>

	<!-- Favicon -->
	<link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.svg">

    <!-- Themescript -->
	 <script src="{{ asset('teacher_backend/assets/js/theme-script.js') }}"></script>

	<!-- Aos -->
	<link rel="stylesheet" href="{{ asset('teacher_backend/assets/css/animate.css') }}">
	<link rel="stylesheet" href="{{ asset('teacher_backend/assets/css/bootstrap.min.css') }}">

	<!-- Tabler Icon CSS -->
	<link rel="stylesheet" href="{{ asset('teacher_backend/assets/plugins/tabler-icons/tabler-icons.css') }}">

    <!-- Datetimepicker CSS -->
	<link rel="stylesheet" href="{{ asset('teacher_backend/assets/css/bootstrap-datetimepicker.min.css') }}">

	<!-- select CSS -->
	<link rel="stylesheet" href="{{ asset('teacher_backend/assets/plugins/select2/css/select2.min.css') }}">

	<!-- Fontawesome Icon CSS -->
	<link rel="stylesheet" href="{{ asset('teacher_backend/assets/plugins/fontawesome/css/fontawesome.min.css') }}">
	<link rel="stylesheet" href="{{ asset('teacher_backend/assets/plugins/fontawesome/css/all.min.css') }}">

	<!-- Owlcarousel CSS -->
	<link rel="stylesheet" href="{{ asset('teacher_backend/assets/plugins/owlcarousel/owl.carousel.min.css') }}">

    <!--Feather CSS -->
	<link rel="stylesheet" href="{{ asset('teacher_backend/assets/css/feather.css') }}">

	<!-- Mobile CSS-->
	<link rel="stylesheet" href="{{ asset('teacher_backend/assets/plugins/intltelinput/css/intlTelInput.css') }}">
	<link rel="stylesheet" href="{{ asset('teacher_backend/assets/plugins/intltelinput/css/demo.css') }}">

	<!-- Tagsinput CSS -->
	<link rel="stylesheet" href="{{ asset('teacher_backend/assets/plugins/bootstrap-tagsinput/css/bootstrap-tagsinput.css') }}">

    <!-- Calendar CSS -->
	<link rel="stylesheet" href="{{ asset('teacher_backend/assets/plugins/simple-calendar/simple-calendar.css') }}">

	<!-- Mobile Input -->
	<script src="{{ asset('teacher_backend/assets/plugins/intltelinput/js/intlTelInput.js') }}"></script>

	<!-- Style CSS -->
	<link rel="stylesheet" href="{{ asset('teacher_backend/assets/css/style.css') }}">

</head>

<body class="provider-page">

    <div class="main-wrapper">

        <!-- Header -->
        @include('teacher.layouts.partials.header')
        <!-- /Header -->

        <!-- Sidebar -->
        @include('teacher.layouts.partials.sidebar')
        <!-- /Sidebar -->

        <!-- Page Wrapper -->
        {{-- <div class="page-wrapper"> --}}
            @yield('content')
        {{-- </div> --}}
        <!-- /Page Wrapper -->



    </div>

	<!-- Jquery JS -->
	<script src="{{ asset('teacher_backend/assets/js/jquery-3.7.1.min.js') }}"></script>

    <!-- Slimscroll JS -->
	<script src="{{ asset('teacher_backend/assets/js/jquery.slimscroll.min.js') }}"></script>

	<!-- Bootstrap JS -->
	<script src="{{ asset('teacher_backend/assets/js/bootstrap.bundle.min.js') }}"></script>

	<!-- Wow JS -->
	<script src="{{ asset('teacher_backend/assets/js/wow.min.js') }}"></script>

	<!-- select JS -->
	<script src="{{ asset('teacher_backend/assets/plugins/select2/js/select2.min.js') }}"></script>

	<!-- Owlcarousel Js -->
	<script src="{{ asset('teacher_backend/assets/plugins/owlcarousel/owl.carousel.min.js') }}"></script>

	<!-- cursor JS -->
	<script src="{{ asset('teacher_backend/assets/js/cursor.js') }}"></script>

    <!-- Datetimepicker JS -->
	<script src="{{ asset('teacher_backend/assets/js/moment.min.js') }}"></script>
	<script src="{{ asset('teacher_backend/assets/js/bootstrap-datetimepicker.min.js') }}"></script>

    <!-- Counter JS -->
	<script src="{{ asset('teacher_backend/assets/plugins/countup/jquery.counterup.min.js') }}"></script>
	<script src="{{ asset('teacher_backend/assets/plugins/countup/jquery.waypoints.min.js') }}">	</script>

    <!-- Apexchart JS -->
    <script src="{{ asset('teacher_backend/assets/plugins/apexchart/apexcharts.min.js') }}" ></script>
    <script src="{{ asset('teacher_backend/assets/plugins/apexchart/chart-data.js') }}" ></script>

	<!-- Tagsinput JS -->
	<script src="{{ asset('teacher_backend/assets/plugins/bootstrap-tagsinput/js/bootstrap-tagsinput.js') }}"></script>

    <!-- Calendar Js -->
    <script src="{{ asset('teacher_backend/assets/plugins/simple-calendar/jquery.simple-calendar.min.js') }}"></script>
    <script src="{{ asset('teacher_backend/assets/js/calender.js') }}"></script>

	<!-- Script JS -->
	<script src="{{ asset('teacher_backend/assets/js/script.js') }}"></script>
</body>

</html>
