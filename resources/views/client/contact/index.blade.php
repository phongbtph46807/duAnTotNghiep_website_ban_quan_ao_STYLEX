@extends('client.layouts.app')

@section('title', 'Liên hệ - ' . env('APP_NAME'))

@section('content')
<div class="container">
    <div class="row">

        <!-- Nội dung liên hệ -->
        <div class="col-md-8 col-lg-9 p-b-80">
            <div class="p-r-45 p-r-0-lg">

                <h3 class="mtext-111 cl2 p-b-16">
                    Liên hệ với chúng tôi
                </h3>

                <p class="stext-113 cl6 p-b-26">
                    Nếu bạn có bất kỳ câu hỏi nào, vui lòng gửi thông tin cho chúng tôi.
                    Đội ngũ hỗ trợ sẽ phản hồi trong thời gian sớm nhất.
                </p>

                <!-- Form liên hệ -->
                <form action="" method="POST">
                    @csrf

                    <div class="row p-b-25">
                        <div class="col-sm-6 p-b-25">
                            <input
                                class="stext-111 cl2 plh3 size-116 p-l-20"
                                type="text"
                                name="name"
                                placeholder="Họ và tên"
                                required>
                        </div>

                        <div class="col-sm-6 p-b-25">
                            <input
                                class="stext-111 cl2 plh3 size-116 p-l-20"
                                type="email"
                                name="email"
                                placeholder="Email"
                                required>
                        </div>

                        <div class="col-12 p-b-25">
                            <input
                                class="stext-111 cl2 plh3 size-116 p-l-20"
                                type="text"
                                name="subject"
                                placeholder="Tiêu đề">
                        </div>

                        <div class="col-12 p-b-25">
                            <textarea
                                class="stext-111 cl2 plh3 size-120 p-l-20 p-t-15"
                                name="message"
                                placeholder="Nội dung liên hệ"
                                required></textarea>
                        </div>
                    </div>

                    <button class="flex-c-m stext-101 cl0 size-121 bg3 bor1 hov-btn3 p-lr-15 trans-04">
                        Gửi liên hệ
                    </button>
                </form>

                <!-- Bản đồ -->
                <div class="p-t-60">
                    <h4 class="mtext-112 cl2 p-b-20">Vị trí cửa hàng</h4>
                    <div class="bor10 of-hidden">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3723.8639324316478!2d105.74239088598995!3d21.03812973062704!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x313455e940879933%3A0xcf10b34e9f1a03df!2zVHLGsOG7nW5nIENhbyDEkeG6s25nIEZQVCBQb2x5dGVjaG5pYw!5e0!3m2!1svi!2s!4v1766309711598!5m2!1svi!2s"
                            width="100%"
                            height="300"
                            style="border:0;"
                            allowfullscreen
                            loading="lazy">
                        </iframe>
                        
                    </div>
                </div>

            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4 col-lg-3 p-b-80">
            <div class="side-menu">

                <!-- Thông tin liên hệ -->
                <div class="p-t-55">
                    <h4 class="mtext-112 cl2 p-b-20">Thông tin</h4>

                    <p class="stext-115 cl6 p-b-10">
                        <strong>Địa chỉ:</strong><br>
                        123 Đường ABC, Quận 1, TP.HCM
                    </p>

                    <p class="stext-115 cl6 p-b-10">
                        <strong>Email:</strong><br>
                        support@example.com
                    </p>

                    <p class="stext-115 cl6 p-b-10">
                        <strong>Hotline:</strong><br>
                        0123 456 789
                    </p>
                </div>

                <!-- Giờ làm việc -->
                <div class="p-t-50">
                    <h4 class="mtext-112 cl2 p-b-20">Giờ làm việc</h4>
                    <p class="stext-115 cl6">
                        Thứ 2 - Thứ 6: 8:00 - 17:30<br>
                        Thứ 7: 8:00 - 12:00<br>
                        Chủ nhật: Nghỉ
                    </p>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection
