<!DOCTYPE html>
<html lang="vi">
<head>
	<title>@yield('title', env('APP_NAME'))</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<!-- Google Fonts - Hỗ trợ tiếng Việt -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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

	<!-- Chat Box -->
    @include('client.partials.chat')

	<!-- Content -->
	@yield('content')

	<!-- Footer -->
    @include('client.partials.footer')

<!--===============================================================================================-->	
@include('client.partials.js.js')

@stack('scripts')

</body>
</html>
<!--===============================================================================================-->
