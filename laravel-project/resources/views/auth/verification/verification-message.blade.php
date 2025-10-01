<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác thực tài khoản | {{ config('app.name') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9f9f9;
            padding: 30px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            text-align: center;
        }
        .status {
            font-size: 18px;
            margin: 20px 0;
            color: #333;
        }
        .success {
            color: #28a745;
            font-weight: bold;
        }
        .error {
            color: #dc3545;
            font-weight: bold;
        }
        a.btn {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 20px;
            background: #111;
            color: #fff;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
        }
        a.btn:hover {
            background: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>{{ config('app.name') }}</h2>
        
        <div class="status {{ Str::contains($msg, 'thành công') ? 'success' : 'error' }}">
            {{ $msg }}
        </div>

        <a href="{{ url('/') }}" class="btn">Quay về trang chủ</a>
    </div>
</body>
</html>
