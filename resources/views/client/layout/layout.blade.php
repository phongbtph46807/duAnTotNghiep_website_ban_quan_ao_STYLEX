<!DOCTYPE html>
<html lang="en">
<head>
	<title>{{ env('APP_NAME') }}</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	@include('client.partials.css.css')
</head>
<body class="animsition">
	
	<!-- Header -->
	<header class="header-v4">
		<!-- Header menu desktop -->
        @include('client.partials.sidebar')

	

        <!-- mobile reponsive -->
         @include('client.partials.mobile')
		
	</header>

	<!-- Cart -->
    @include('client.partials.cart')

	<!-- Footer -->
    @include('client.partials.footer')

	<!-- Content -->
	@yield('content')

</body>
</html>
<!--===============================================================================================-->	
@include('client.partials.js.js')
<!--===============================================================================================-->