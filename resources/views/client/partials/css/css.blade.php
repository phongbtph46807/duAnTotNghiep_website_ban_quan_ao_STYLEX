<!--===============================================================================================-->	
	<link rel="icon" type="image/png" href="{{ asset('client/images/icons/favicon.png') }}"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{ asset('client/vendor/bootstrap/css/bootstrap.min.css') }}">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{ asset('client/fonts/font-awesome-4.7.0/css/font-awesome.min.css') }}">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{ asset('client/fonts/iconic/css/material-design-iconic-font.min.css') }}">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{ asset('client/fonts/linearicons-v1.0.0/icon-font.min.css') }}">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{ asset('client/vendor/animate/animate.css') }}">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="{{ asset('client/vendor/css-hamburgers/hamburgers.min.css') }}">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{ asset('client/vendor/animsition/css/animsition.min.css') }}">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{ asset('client/vendor/select2/select2.min.css') }}">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="{{ asset('client/vendor/daterangepicker/daterangepicker.css') }}">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{ asset('client/vendor/slick/slick.css') }}">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{ asset('client/vendor/MagnificPopup/magnific-popup.css') }}">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{ asset('client/vendor/perfect-scrollbar/perfect-scrollbar.css') }}">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{ asset('client/css/util.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('client/css/main.css') }}">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
	<!-- Custom CSS for User Dropdown -->
	<style>
		.dropdown {
			position: relative;
			display: inline-block;
		}
		
		.dropdown-toggle {
			text-decoration: none;
			display: flex;
			align-items: center;
			cursor: pointer;
		}
		
		.dropdown-toggle span {
			font-size: 12px;
			font-weight: 500;
			margin-left: 5px;
			color: #333;
		}
		
		.dropdown-menu {
			position: absolute;
			top: 100%;
			right: 0;
			z-index: 9999;
			display: none;
			min-width: 200px;
			padding: 0;
			margin: 5px 0 0;
			font-size: 12px;
			color: #212529;
			text-align: left;
			list-style: none;
			background-color: #fff;
			background-clip: padding-box;
			border: 1px solid rgba(0,0,0,.15);
			border-radius: 8px;
			box-shadow: 0 4px 20px rgba(0,0,0,.15);
			overflow: hidden;
		}
		
		.dropdown-menu.show {
			display: block;
		}
		
		.dropdown-item {
			display: block;
			width: 100%;
			padding: 8px 12px;
			clear: both;
			font-weight: 400;
			font-size: 12px;
			color: #212529;
			text-align: inherit;
			text-decoration: none;
			white-space: nowrap;
			background-color: transparent;
			border: 0;
			cursor: pointer;
			transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out;
		}
		
		.dropdown-item:hover {
			color: #16181b;
			background-color: #f8f9fa;
		}
		
		.dropdown-item.text-danger {
			color: #dc3545;
		}
		
		.dropdown-item.text-danger:hover {
			color: #fff;
			background-color: #dc3545;
		}
		
		.dropdown-divider {
			height: 0;
			margin: 5px 0;
			overflow: hidden;
			border-top: 1px solid #e9ecef;
		}
		
		.dropdown-menu-end {
			right: 0;
			left: auto;
		}
		
		.ml-2 {
			margin-left: 5px;
		}
		
		.me-2 {
			margin-right: 5px;
		}
		
		/* Đảm bảo dropdown có thể click được */
		.dropdown-menu * {
			pointer-events: auto;
		}
		
		.dropdown-item button {
			background: none;
			border: none;
			width: 100%;
			text-align: left;
			padding: 0;
			cursor: pointer;
			font-size: inherit;
			font-family: inherit;
		}
		
		/* Dropdown Header */
		.dropdown-header {
			padding: 12px 16px;
			background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
			color: #333;
			border-bottom: 1px solid #dee2e6;
		}
		
		.user-info {
			text-align: center;
		}
		
		.user-name {
			font-size: 14px;
			font-weight: 600;
			margin-bottom: 2px;
		}
		
		.user-email {
			font-size: 11px;
			opacity: 0.7;
			color: #666;
		}
		
		/* Dropdown Items */
		.dropdown-item {
			padding: 10px 16px;
			font-size: 13px;
			border-bottom: 1px solid #f0f0f0;
		}
		
		.dropdown-item:last-child {
			border-bottom: none;
		}
		
		.dropdown-item i {
			width: 16px;
			text-align: center;
			color: #666;
		}
		
		.dropdown-item:hover i {
			color: #333;
		}
		
		/* Logout Button */
		.logout-btn {
			color: #dc3545 !important;
			font-weight: 500;
		}
		
		.logout-btn:hover {
			background-color: #dc3545 !important;
			color: white !important;
		}
		
		.logout-btn:hover i {
			color: white !important;
		}
		
		/* Admin Link */
		.admin-link {
			background-color: #f8f9fa !important;
			font-weight: 600 !important;
			color: #495057 !important;
		}
		
		.admin-link:hover {
			background-color: #e9ecef !important;
			color: #212529 !important;
		}
		
		.admin-link i {
			color: #6c757d !important;
		}
		
		.admin-link:hover i {
			color: #495057 !important;
		}
		
		/* Divider */
		.dropdown-divider {
			height: 1px;
			margin: 0;
			background-color: #e9ecef;
		}
	</style>
	<style>
		body {
            background-color: #f8f9fa;
        }

        .rating-bar {
            height: 8px;
            border-radius: 4px;
            background-color: #edeef0;
            overflow: hidden;
        }

        .rating-bar-fill {
            height: 100%;
            background-color: #d70018;
            border-radius: 4px 0 0 4px;
        }

        /* Tag nhỏ trong đánh giá */
        .tag {
            font-size: 13px;
            padding: 3px 10px;
            background-color: #f5f6f7;
            border-radius: 8px;
            margin-right: 0.4rem;
            margin-bottom: 0.4rem;
            display: inline-block;
            color: #565656;
        }

        /* Nút màu đỏ */
        .btn-red {
            background-color: #d70018;
            color: white;
            border: none;
            padding: 0.375rem 0.9rem;
            font-size: 14px;
            font-weight: 500;
            border-radius: 4px;
        }

        .btn-red:hover {
            background-color: #b30012;
            color: white;
        }

        /* Avatar tròn */
        .avatar-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            color: white;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }

        .avatar-t {
            background-color: #864cff;
        }

        .avatar-d {
            background-color: #3c72ff;
        }

        .avatar-p {
            background-color: #a03f64;
        }

        .avatar-n {
            background-color: #42617b;
        }

        .avatar-h {
            background-color: #d14343;
        }

        /* Các nút lọc */
        .filter-btn {
            font-size: 13px;
            margin-right: 0.4rem;
            margin-bottom: 0.5rem;
            padding: 0.25rem 0.9rem;
            border-radius: 20px;
            border: 1px solid #ddd;
            background: #fff;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .filter-btn:hover,
        .filter-btn.active {
            background-color: #d70018;
            color: white;
            border-color: #d70018;
        }

        /* Icon ngôi sao SVG nhỏ */
        .star-icon {
            width: 18px;
            height: 18px;
            margin-left: 4px;
            flex-shrink: 0;
            fill: #FFDD55;
            /* Vàng sáng */
            stroke: #F5A623;
            /* Viền vàng đậm */
            stroke-width: 1;
        }

        .stars-inline {
            display: inline-flex;
            align-items: center;
            gap: 1px;
            user-select: none;
        }

        .review-header {
            font-weight: 600;
            font-size: 0.95rem;
            margin-bottom: 0.3rem;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .review-subtitle {
            font-size: 0.8rem;
            color: #888;
        }

        .review-time {
            font-size: 0.75rem;
            color: #999;
            margin-top: 0.3rem;
            user-select: none;
        }

        .review-time i {
            margin-right: 5px;
        }

        .container-custom {
            max-width: 1080px;
        }

        /* Scroll x nếu có nhiều nút lọc */
        .filter-container {
            overflow-x: auto;
            white-space: nowrap;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
        }

        .star-input {
            direction: rtl;
            font-size: 24px;
            unicode-bidi: bidi-override;
            display: inline-flex;
            justify-content: flex-start;
        }

        .star-input input {
            display: none;
        }

        .star-input label {
            color: #ddd;
            cursor: pointer;
            padding: 0 4px;
            transition: color 0.2s ease-in-out;
        }

        .star-input label:hover,
        .star-input label:hover~label,
        .star-input input:checked~label {
            color: #ffcc00;
        }

        .tag-checkbox {
            margin-right: 0.8rem;
            margin-bottom: 0.5rem;
        }

        .form-label {
            font-weight: 600;
        }

        .modal-header-title {
            font-weight: 700;
            font-size: 1.2rem;
        }
	</style>
<!--===============================================================================================-->