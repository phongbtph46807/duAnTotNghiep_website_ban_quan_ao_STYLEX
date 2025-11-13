<?php $__env->startSection('title', 'Vòng quay may mắn'); ?>

    <style>
        .spin-page {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 60px 0;
            position: relative;
            overflow: hidden;
        }

        .spin-page::before,
        .spin-page::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float 8s ease-in-out infinite;
        }

        .spin-page::before {
            width: 400px;
            height: 400px;
            top: -100px;
            right: -100px;
        }

        .spin-page::after {
            width: 300px;
            height: 300px;
            bottom: -80px;
            left: -80px;
            animation-delay: 2s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) scale(1); }
            50% { transform: translateY(-30px) scale(1.05); }
        }

        .page-header {
            text-align: center;
            color: white;
            margin-bottom: 40px;
            position: relative;
            z-index: 1;
        }

        .page-header h1 {
            font-size: 3rem;
            font-weight: 900;
            text-shadow: 0 4px 12px rgba(0,0,0,0.3);
            margin-bottom: 10px;
        }

        .page-header .subtitle {
            font-size: 1.1rem;
            opacity: 0.95;
        }

        .spin-card {
            background: white;
            border-radius: 30px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            position: relative;
            z-index: 1;
        }

        .wheel-wrapper {
            position: relative;
            padding: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .wheel-container {
            position: relative;
            width: 450px;
            height: 450px;
        }

        /* Canvas Wheel */
        #wheelCanvas {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            box-shadow:
                0 0 0 12px #fff,
                0 0 0 16px #ffd700,
                0 0 0 20px #fff,
                0 10px 30px rgba(0,0,0,0.25);
            transition: transform 5s cubic-bezier(0.25, 0.1, 0.25, 1);
        }

        #wheelCanvas.spinning {
            box-shadow:
                0 0 0 12px #fff,
                0 0 0 16px #ffd700,
                0 0 0 20px #fff,
                0 10px 30px rgba(0,0,0,0.25),
                0 0 40px rgba(255, 215, 0, 0.6);
        }

        .wheel-pointer {
            position: absolute;
            top: -20px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            border-left: 20px solid transparent;
            border-right: 20px solid transparent;
            border-top: 40px solid #ff0000;
            z-index: 20;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.3));
            animation: pointerBounce 1s ease-in-out infinite;
        }

        @keyframes pointerBounce {
            0%, 100% { transform: translateX(-50%) translateY(0); }
            50% { transform: translateX(-50%) translateY(8px); }
        }

        .wheel-center {
            position: absolute;
            width: 100px;
            height: 100px;
            background: linear-gradient(145deg, #ffd700, #ffed4e);
            border-radius: 50%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            font-weight: 900;
            color: #333;
            box-shadow:
                0 5px 15px rgba(0,0,0,0.3),
                inset 0 2px 8px rgba(255,255,255,0.5);
            z-index: 15;
            border: 5px solid #fff;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .wheel-center:hover {
            transform: translate(-50%, -50%) scale(1.05);
        }

        .wheel-center-icon {
            font-size: 2rem;
        }

        .wheel-center-text {
            font-size: 0.9rem;
            margin-top: 2px;
        }

        .spin-controls {
            padding: 30px 40px 40px;
            text-align: center;
            border-top: 2px solid #f0f0f0;
        }

        .btn-spin {
            background: linear-gradient(135deg, #ff6b6b, #ee5a6f);
            border: none;
            color: white;
            font-size: 1.3rem;
            font-weight: 800;
            padding: 18px 60px;
            border-radius: 50px;
            box-shadow: 0 8px 20px rgba(238, 90, 111, 0.4);
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .btn-spin::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255,255,255,0.3);
            transform: translate(-50%, -50%);
            transition: width 0.5s, height 0.5s;
        }

        .btn-spin:hover:not(:disabled)::before {
            width: 300px;
            height: 300px;
        }

        .btn-spin:hover:not(:disabled) {
            transform: translateY(-3px);
            box-shadow: 0 12px 28px rgba(238, 90, 111, 0.5);
        }

        .btn-spin:disabled {
            background: linear-gradient(135deg, #ccc, #999);
            cursor: not-allowed;
            box-shadow: none;
        }

        .alert-spin {
            background: linear-gradient(135deg, rgba(255,215,0,0.15), rgba(255,237,78,0.15));
            border: 2px solid rgba(255,215,0,0.4);
            border-radius: 15px;
            padding: 12px 20px;
            color: #333;
            font-weight: 600;
        }

        .prize-list-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            height: 100%;
        }

        .prize-list-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #f0f0f0;
        }

        .prize-list-header h3 {
            margin: 0;
            font-size: 1.4rem;
            font-weight: 800;
            color: #333;
        }

        .prize-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px;
            margin-bottom: 12px;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 12px;
            border: 2px solid transparent;
            transition: all 0.3s;
        }

        .prize-item:hover {
            border-color: #667eea;
            transform: translateX(5px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
        }

        .prize-icon-text {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .prize-icon-text .icon {
            font-size: 2rem;
        }

        .prize-icon-text .text {
            font-weight: 700;
            color: #333;
            font-size: 1rem;
        }

        .prize-rate {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 6px 16px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .history-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            margin-top: 20px;
        }

        .history-list {
            max-height: 300px;
            overflow-y: auto;
            padding-right: 8px;
        }

        .history-list::-webkit-scrollbar {
            width: 5px;
        }

        .history-list::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 10px;
        }

        .history-item {
            background: #f8f9fa;
            padding: 12px 15px;
            border-radius: 10px;
            border-left: 4px solid #667eea;
            margin-bottom: 10px;
        }

        .history-prize {
            font-weight: 700;
            color: #333;
            margin-bottom: 4px;
        }

        .history-time {
            font-size: 0.85rem;
            color: #666;
        }

        .badge-claimed {
            background: #d4edda;
            color: #155724;
            padding: 3px 10px;
            border-radius: 10px;
            font-size: 0.75rem;
            font-weight: 700;
            margin-left: 8px;
        }

        .badge-unclaimed {
            background: #fff3cd;
            color: #856404;
            padding: 3px 10px;
            border-radius: 10px;
            font-size: 0.75rem;
            font-weight: 700;
            margin-left: 8px;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #999;
        }

        .empty-state .icon {
            font-size: 4rem;
            opacity: 0.5;
            margin-bottom: 10px;
        }

        .result-modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.85);
            backdrop-filter: blur(8px);
        }

        .result-modal.show {
            display: flex !important;
            align-items: center;
            justify-content: center;
        }

        .result-modal-content {
            background: white;
            padding: 50px;
            border-radius: 25px;
            text-align: center;
            max-width: 500px;
            animation: modalZoom 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        @keyframes modalZoom {
            from {
                transform: scale(0.5);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .result-icon {
            font-size: 5rem;
            margin-bottom: 20px;
        }

        .result-title {
            font-size: 2.2rem;
            font-weight: 900;
            margin-bottom: 15px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .result-desc {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 25px;
        }

        .result-voucher {
            background: linear-gradient(135deg, #ffd700, #ffed4e);
            padding: 15px 30px;
            border-radius: 15px;
            font-size: 1.5rem;
            font-weight: 900;
            color: #333;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 2rem;
            }

            .wheel-container {
                width: 320px;
                height: 320px;
            }

            .wheel-wrapper {
                padding: 20px;
            }

            .btn-spin {
                font-size: 1.1rem;
                padding: 14px 40px;
            }
        }
    </style>

<?php $__env->startSection('content'); ?>
    <div class="spin-page">
        <div class="container position-relative">
            <div class="page-header">
                <h1>🎯 VÒNG QUAY MAY MẮN</h1>
                <p class="subtitle">✨ Quay ngay để nhận quà tặng hấp dẫn mỗi ngày ✨</p>
            </div>

            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="spin-card">
                        <div class="wheel-wrapper">
                            <div class="wheel-container">
                                <div class="wheel-pointer"></div>
                                <canvas id="wheelCanvas" width="450" height="450"></canvas>
                                <div class="wheel-center">
                                    <div class="wheel-center-icon">🎁</div>
                                    <div class="wheel-center-text">SPIN</div>
                                </div>
                            </div>
                        </div>

                        <div class="spin-controls">
                            <?php if(auth()->guard()->check()): ?>
                                <?php if($canSpin): ?>
                                    <button class="btn btn-spin" id="spinBtn">
                                        🚀 QUAY NGAY
                                    </button>
                                    <div class="spin-info mt-3">
                                        <div class="alert alert-spin mb-0">
                                            ⚡ Bạn còn <strong>1 lượt</strong> quay miễn phí hôm nay!
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <button class="btn btn-spin" disabled>
                                        ❌ Đã hết lượt quay hôm nay
                                    </button>
                                    <div class="spin-info mt-3">
                                        <div class="alert alert-warning mb-0">
                                            ⏰ Quay lại vào ngày mai để nhận lượt mới!
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <button class="btn btn-spin" onclick="window.location.href='<?php echo e(route('login')); ?>'">
                                    🔐 Đăng nhập để quay
                                </button>
                                <div class="spin-info mt-3">
                                    <div class="alert alert-info mb-0">
                                        💎 Đăng nhập ngay để nhận ưu đãi!
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="prize-list-card">
                        <div class="prize-list-header">
                            <span style="font-size: 2rem;">🎁</span>
                            <h3>Giải thưởng</h3>
                        </div>
                        <div class="prize-list">
                            <?php
                                $icons = ['🎁', '💎', '🎉', '⭐', '🏆', '💰', '🎊', '✨'];
                            ?>
                            <?php $__currentLoopData = $spins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $spin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="prize-item">
                                    <div class="prize-icon-text">
                                        <span class="icon"><?php echo e($icons[$index % count($icons)]); ?></span>
                                        <span class="text"><?php echo e($spin->name); ?></span>
                                    </div>
                                    <span class="prize-rate"><?php echo e($spin->probability); ?>%</span>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="result-modal" id="resultModal">
        <div class="result-modal-content">
            <div class="result-icon" id="resultIcon">🎉</div>
            <h2 class="result-title" id="resultTitle">Chúc mừng!</h2>
            <p class="result-desc" id="resultDesc">Bạn đã trúng thưởng</p>
            <div class="result-voucher" id="resultVoucher" style="display:none;"></div>
            <button class="btn btn-primary btn-lg px-5" onclick="closeModal()">Đóng</button>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        // Data từ Laravel
        const spinsData = <?php echo json_encode($spins, 15, 512) ?>;
        const icons = ['🎁', '💎', '🎉', '⭐', '🏆', '💰', '🎊', '✨'];
        const colors = ['#FF6B6B', '#4ECDC4', '#45B7D1', '#FFA07A', '#98D8C8', '#F7DC6F', '#BB8FCE', '#85C1E2'];

        // Vẽ vòng quay bằng Canvas
        const canvas = document.getElementById('wheelCanvas');
        const ctx = canvas.getContext('2d');
        const centerX = canvas.width / 2;
        const centerY = canvas.height / 2;
        const radius = canvas.width / 2;

        function drawWheel() {
            const segments = spinsData.length;
            const anglePerSegment = (Math.PI * 2) / segments;

            spinsData.forEach((spin, index) => {
                const startAngle = anglePerSegment * index - Math.PI / 2;
                const endAngle = startAngle + anglePerSegment;

                // Vẽ segment
                ctx.beginPath();
                ctx.moveTo(centerX, centerY);
                ctx.arc(centerX, centerY, radius, startAngle, endAngle);
                ctx.closePath();
                ctx.fillStyle = colors[index % colors.length];
                ctx.fill();

                // Viền segment
                ctx.strokeStyle = '#fff';
                ctx.lineWidth = 3;
                ctx.stroke();

                // Vẽ icon
                const textAngle = startAngle + anglePerSegment / 2;
                const textX = centerX + Math.cos(textAngle) * (radius * 0.65);
                const textY = centerY + Math.sin(textAngle) * (radius * 0.65);

                ctx.save();
                ctx.translate(textX, textY);
                ctx.rotate(textAngle + Math.PI / 2);
                ctx.font = 'bold 40px Arial';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillStyle = '#fff';
                ctx.shadowColor = 'rgba(0,0,0,0.5)';
                ctx.shadowBlur = 4;
                ctx.shadowOffsetX = 2;
                ctx.shadowOffsetY = 2;
                ctx.fillText(icons[index % icons.length], 0, 0);
                ctx.restore();
            });
        }

        drawWheel();

        // Xử lý quay
        let isSpinning = false;
        let currentRotation = 0;

        document.getElementById('spinBtn')?.addEventListener('click', function() {
            if (isSpinning) return;

            isSpinning = true;
            this.disabled = true;
            this.textContent = '⏳ Đang quay...';

            canvas.classList.add('spinning');

            fetch('<?php echo e(route('spin.play')); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                }
            })
                .then(res => res.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                        isSpinning = false;
                        this.disabled = false;
                        this.textContent = '🚀 QUAY NGAY';
                        canvas.classList.remove('spinning');
                        return;
                    }

                    const selectedIndex = spinsData.findIndex(s => s.id === data.spin.id);
                    const segmentAngle = 360 / spinsData.length;
                    const targetAngle = selectedIndex * segmentAngle;
                    const randomOffset = Math.random() * segmentAngle * 0.8 + segmentAngle * 0.1;
                    const totalRotation = currentRotation + 360 * 8 + (360 - targetAngle - segmentAngle/2) + randomOffset;

                    canvas.style.transform = `rotate(${totalRotation}deg)`;
                    currentRotation = totalRotation % 360;

                    setTimeout(() => {
                        canvas.classList.remove('spinning');
                        showResult(data);

                        setTimeout(() => {
                            window.location.reload();
                        }, 4000);
                    }, 5000);
                })
                .catch(err => {
                    console.error(err);
                    alert('Có lỗi xảy ra!');
                    isSpinning = false;
                    this.disabled = false;
                    this.textContent = '🚀 QUAY NGAY';
                    canvas.classList.remove('spinning');
                });
        });

        function showResult(data) {
            const modal = document.getElementById('resultModal');
            const icon = document.getElementById('resultIcon');
            const title = document.getElementById('resultTitle');
            const desc = document.getElementById('resultDesc');
            const voucherDiv = document.getElementById('resultVoucher');

            if (data.voucher) {
                icon.textContent = '🎉';
                title.textContent = 'CHÚC MỪNG!';
                desc.innerHTML = `Bạn đã trúng: <strong>${data.spin.name}</strong>`;
                voucherDiv.textContent = `Mã: ${data.voucher.code}`;
                voucherDiv.style.display = 'block';
            } else {
                icon.textContent = '😊';
                title.textContent = 'Chúc bạn may mắn lần sau!';
                desc.textContent = 'Hãy quay lại vào ngày mai nhé!';
                voucherDiv.style.display = 'none';
            }

            modal.classList.add('show');
        }

        function closeModal() {
            document.getElementById('resultModal').classList.remove('show');
        }
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('client.layout.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/client/spins/index.blade.php ENDPATH**/ ?>