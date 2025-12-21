    <!-- JAVASCRIPT -->
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('assets/libs/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>
    <script src="{{ asset('assets/js/plugins.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- apexcharts -->
    <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>

    <!-- Vector map-->
    <script src="{{ asset('assets/libs/jsvectormap/js/jsvectormap.min.js') }}"></script>
    <script src="{{ asset('assets/libs/jsvectormap/maps/world-merc.js') }}"></script>

    <!--Swiper slider js-->
    <script src="{{ asset('assets/libs/swiper/swiper-bundle.min.js') }}"></script>

    <!-- Dashboard init -->
    <script src="{{ asset('assets/js/pages/dashboard-ecommerce.init.js') }}"></script>

    <!-- ckeditor -->
    <script src="{{ asset('assets/libs/@ckeditor/ckeditor5-build-classic/build/ckeditor.js') }}"></script>

    <!-- dropzone js -->
    <script src="{{ asset('assets/libs/dropzone/dropzone-min.js') }}"></script>

    <script src="{{ asset('assets/js/pages/ecommerce-product-create.init.js') }}"></script>
    <!-- App js -->
    <script src="{{ asset('assets/js/app.js') }}"></script>
        <script>
        // Hiển thị lỗi validation //
        @if ($errors->any())
            @foreach ($errors->all() as $error)
                toastr.error("{{ $error }}");
            @endforeach
        @endif

        // Hiển thị flash messages //
        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @endif

        @if (session('error'))
            toastr.error("{{ session('error') }}");
        @endif

        @if (session('warning'))
            toastr.warning("{{ session('warning') }}");
        @endif

        @if (session('info'))
            toastr.info("{{ session('info') }}");
        @endif
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function handleAction(btnSelector, config) {
                document.querySelectorAll(btnSelector).forEach(function(btn) {
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        const name = btn.getAttribute('data-name') || 'mục này';
                        const form = btn.closest('form');

                        Swal.fire({
                            title: config.title,
                            html: `
                        <div class="swal-custom-content">
                            <div class="swal-icon-wrapper">
                                <i class="${config.iconClass}"></i>
                            </div>
                            <p class="swal-message">
                                ${config.message} 
                                <span class="swal-item-name">"${name}"</span>?
                            </p>
                            ${config.warning ? `<p class="swal-warning">${config.warning}</p>` : ''}
                        </div>
                    `,
                            showCancelButton: true,
                            confirmButtonText: config.confirmText,
                            cancelButtonText: '<i class="fa fa-times"></i> Hủy bỏ',
                            reverseButtons: true,
                            buttonsStyling: false,
                            allowOutsideClick: false,
                            showClass: {
                                popup: 'animate__animated animate__fadeInDown'
                            },
                            hideClass: {
                                popup: 'animate__animated animate__fadeOutUp'
                            },
                            customClass: {
                                popup: 'swal-custom-popup',
                                title: 'swal-custom-title',
                                htmlContainer: 'swal-custom-html',
                                confirmButton: config.confirmClass + ' swal-custom-confirm',
                                cancelButton: 'swal-custom-cancel',
                                actions: 'swal-custom-actions'
                            },
                            didOpen: () => {
                                // Thêm hiệu ứng cho icon
                                const icon = Swal.getPopup().querySelector(
                                    '.swal-icon-wrapper i');
                                if (icon) {
                                    icon.classList.add('animate__animated',
                                        'animate__heartBeat');
                                }
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Hiển thị loading
                                Swal.fire({
                                    title: 'Đang xử lý...',
                                    html: '<div class="swal-loading"><div class="spinner"></div></div>',
                                    allowOutsideClick: false,
                                    showConfirmButton: false,
                                    customClass: {
                                        popup: 'swal-loading-popup'
                                    }
                                });

                                // Submit form
                                setTimeout(() => {
                                    form.submit();
                                }, 500);
                            }
                        });
                    });
                });
            }

            // Cấu hình cho từng loại action
            const actionConfigs = {
                softDelete: {
                    title: '⚠️ Xác nhận xóa',
                    message: 'Bạn có chắc chắn muốn xóa',
                    warning: 'Dữ liệu sẽ được chuyển vào thùng rác và có thể khôi phục lại.',
                    iconClass: 'fas fa-trash-alt text-warning',
                    confirmText: '<i class="fas fa-trash"></i> Xóa ngay',
                    confirmClass: 'btn-gradient-warning'
                },
                restore: {
                    title: '♻️ Khôi phục dữ liệu',
                    message: 'Bạn muốn khôi phục lại',
                    iconClass: 'fas fa-undo-alt text-info',
                    confirmText: '<i class="fas fa-undo"></i> Khôi phục',
                    confirmClass: 'btn-gradient-info'
                },
                forceDelete: {
                    title: '🚨 Cảnh báo nghiêm trọng',
                    message: 'Bạn thực sự muốn xóa vĩnh viễn',
                    warning: '⚠️ Hành động này KHÔNG THỂ hoàn tác! Dữ liệu sẽ bị xóa hoàn toàn khỏi hệ thống.',
                    iconClass: 'fas fa-exclamation-triangle text-danger',
                    confirmText: '<i class="fas fa-trash-alt"></i> Xóa vĩnh viễn',
                    confirmClass: 'btn-gradient-danger'
                }
            };

            // Áp dụng cho các button
            handleAction('.btn-delete', actionConfigs.softDelete);
            handleAction('.btn-remove', actionConfigs.restore);
            handleAction('.btn-forcedelete', actionConfigs.forceDelete);
        });
    </script>
    
    {{-- Realtime update cho badge yêu cầu hủy/trả hàng --}}
    <script>
        // Function để load và hiển thị thông báo
        function loadNotifications() {
            fetch("{{ route('admin.orders.notifications') }}", {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Accept': 'application/json',
                },
            })
            .then(res => res.json())
            .then(data => {
                const content = document.getElementById('notificationsContent');
                if (!content) return;
                
                const newOrders = data.new_orders || [];
                const pendingRequests = data.pending_requests || [];
                const totalCount = (data.new_orders_count || 0) + (data.pending_requests_count || 0);
                
                // Cập nhật badge
                const navbarBadge = document.getElementById('navbarPendingRequestsBadge');
                if (navbarBadge) {
                    if (totalCount > 0) {
                        navbarBadge.textContent = totalCount;
                        navbarBadge.classList.remove('d-none');
                    } else {
                        navbarBadge.classList.add('d-none');
                    }
                }
                
                // Cập nhật badge trong sidebar
                const sidebarBadge = document.getElementById('pendingRequestsBadge');
                if (sidebarBadge) {
                    const requestsCount = data.pending_requests_count || 0;
                    if (requestsCount > 0) {
                        sidebarBadge.textContent = requestsCount;
                        sidebarBadge.classList.remove('d-none');
                    } else {
                        sidebarBadge.classList.add('d-none');
                    }
                }
                
                // Hiển thị nội dung thông báo
                if (newOrders.length === 0 && pendingRequests.length === 0) {
                    content.innerHTML = '<div class="text-center p-4 text-muted">Không có thông báo mới</div>';
                    return;
                }
                
                let html = '';
                
                // Đơn hàng mới
                if (newOrders.length > 0) {
                    html += '<div class="p-2 border-bottom bg-light"><small class="text-muted fw-semibold"><i class="ri-shopping-bag-3-line me-1"></i>Đơn hàng mới</small></div>';
                    newOrders.forEach(order => {
                        html += `
                            <a href="${order.url}" class="dropdown-item p-3 border-bottom text-decoration-none" style="transition: background 0.2s;">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-xs">
                                            <span class="avatar-title bg-success-subtle text-success rounded-circle">
                                                <i class="ri-shopping-bag-3-line"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        <h6 class="mb-1" style="font-size: 13px; color: #212529;">Đơn hàng #${order.code}</h6>
                                        <p class="mb-0 text-muted" style="font-size: 12px;">${order.customer} - ${order.total}</p>
                                        <small class="text-muted">${order.created_at}</small>
                                    </div>
                                </div>
                            </a>
                        `;
                    });
                }
                
                // Yêu cầu hủy/trả hàng
                if (pendingRequests.length > 0) {
                    html += '<div class="p-2 border-bottom bg-light"><small class="text-muted fw-semibold"><i class="ri-error-warning-line me-1"></i>Yêu cầu cần xử lý</small></div>';
                    pendingRequests.forEach(request => {
                        const statusClass = request.status === 'cancel_request' ? 'danger' : 'warning';
                        const statusIcon = request.status === 'cancel_request' ? 'ri-close-circle-line' : 'ri-refund-2-line';
                        html += `
                            <a href="${request.url}" class="dropdown-item p-3 border-bottom text-decoration-none" style="transition: background 0.2s;">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-xs">
                                            <span class="avatar-title bg-${statusClass}-subtle text-${statusClass} rounded-circle">
                                                <i class="${statusIcon}"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        <h6 class="mb-1" style="font-size: 13px; color: #212529;">${request.status_label} - #${request.code}</h6>
                                        <p class="mb-0 text-muted" style="font-size: 12px;">${request.customer}</p>
                                        <small class="text-muted">${request.created_at}</small>
                                    </div>
                                </div>
                            </a>
                        `;
                    });
                }
                
                content.innerHTML = html;
                
                // Thêm hover effect
                content.querySelectorAll('.dropdown-item').forEach(item => {
                    item.addEventListener('mouseenter', function() {
                        this.style.backgroundColor = '#f8f9fa';
                    });
                    item.addEventListener('mouseleave', function() {
                        this.style.backgroundColor = '';
                    });
                });
            })
            .catch(err => {
                console.error('Error loading notifications:', err);
                const content = document.getElementById('notificationsContent');
                if (content) {
                    content.innerHTML = '<div class="text-center p-4 text-danger">Lỗi khi tải thông báo</div>';
                }
            });
        }
        
        // Load thông báo khi dropdown được mở
        const notificationsDropdown = document.getElementById('orderNotificationsDropdown');
        if (notificationsDropdown) {
            notificationsDropdown.addEventListener('shown.bs.dropdown', function() {
                loadNotifications();
            });
            
            // Lắng nghe khi dropdown đóng để reset
            notificationsDropdown.addEventListener('hidden.bs.dropdown', function() {
                // Có thể thêm logic reset nếu cần
            });
        }
        
        // Function để kiểm tra dropdown có đang mở không
        function isNotificationsDropdownOpen() {
            const dropdown = document.getElementById('orderNotificationsDropdown');
            if (!dropdown) return false;
            const dropdownMenu = dropdown.nextElementSibling;
            return dropdownMenu && dropdownMenu.classList.contains('show');
        }
        
        // Function để cập nhật badge số lượng yêu cầu
        function updatePendingRequestsBadge() {
            loadNotifications();
        }
        
        // Lắng nghe event realtime khi có yêu cầu mới hoặc duyệt yêu cầu
        if (typeof window.Echo !== 'undefined') {
            // Lắng nghe khi có đơn hàng mới
            window.Echo.channel('orders')
                .listen('.order.created', (e) => {
                    console.log('🆕 New order created (realtime):', e);
                    // Cập nhật badge ngay lập tức
                    updatePendingRequestsBadge();
                    // Nếu dropdown đang mở, reload thông báo để hiển thị đơn hàng mới
                    if (isNotificationsDropdownOpen()) {
                        loadNotifications();
                    }
                })
                .listen('.order.status.updated', (e) => {
                    console.log('🔄 Order status updated (realtime):', e);
                    // Nếu status là cancel_request, return_request, pending -> cập nhật
                    // Nếu status là cancelled, returned -> cập nhật (giảm yêu cầu)
                    if (['cancel_request', 'return_request', 'pending', 'cancelled', 'returned', 'delivered', 'completed'].includes(e.status)) {
                        // Cập nhật badge ngay lập tức
                        updatePendingRequestsBadge();
                        // Nếu dropdown đang mở, reload thông báo
                        if (isNotificationsDropdownOpen()) {
                            loadNotifications();
                        }
                    }
                });
            
            console.log('✅ Realtime notifications enabled');
        } else {
            console.warn('⚠️ Laravel Echo not loaded. Realtime notifications disabled.');
        }
        
        // Cập nhật badge mỗi 30 giây để đảm bảo đồng bộ (fallback)
        setInterval(updatePendingRequestsBadge, 30000);
        
        // Load thông báo lần đầu khi trang load (chỉ cập nhật badge, không load dropdown content)
        updatePendingRequestsBadge();
    </script>