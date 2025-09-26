<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ env('APP_NAME') }}</title>

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('backend/assets/css/bootstrap.css') }}">

    <link rel="stylesheet" href="{{ asset('backend/assets/vendors/iconly/bold.css') }}">

    <link rel="stylesheet" href="{{ asset('backend/assets/vendors/perfect-scrollbar/perfect-scrollbar.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/assets/vendors/bootstrap-icons/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/assets/css/app.css') }}">
    <link rel="shortcut icon" href="{{ asset('backend/assets/images/favicon.svg') }}" type="image/x-icon">
</head>

<body>
        
    <div id="app">
        <!-- header -->
         @include('layouts.menu-admin')
        <!-- content -->


            @yield('content')


   
        <!-- footer -->
        @include('layouts.footer-admin')
        </div>
    </div>
    <script src="{{ asset('backend/assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('backend/assets/js/bootstrap.bundle.min.js') }}"></script>

    <script src="{{ asset('backend/assets/vendors/apexcharts/apexcharts.js') }}"></script>
    <script src="{{ asset('backend/assets/js/pages/dashboard.js') }}"></script>

    <script src="{{ asset('backend/assets/js/main.js') }}"></script>
</body>

</html>