// Cart Management JavaScript

// Load cart when page loads
function loadCart() {
    console.log('Loading cart...');
    $.ajax({
        url: '/cart/get',
        method: 'GET',
        success: function(response) {
            console.log('Cart data:', response);
            if (response.cart && response.cart.length > 0) {
                console.log('First cart item:', response.cart[0]);
                console.log('Image URL:', response.cart[0].image_url);
                console.log('Image:', response.cart[0].image);
            }
            updateCartUI(response);
            updateCartCount(response.count);
        },
        error: function(xhr, status, error) {
            console.error('Error loading cart:', xhr, status, error);
        }
    });
}

// Update cart UI
function updateCartUI(data) {
    const cartItems = $('#cartItems');
    const cartFooter = $('#cartFooter');
    
    if (!data.cart || data.cart.length === 0) {
        cartItems.html('<li class="header-cart-empty" style="padding: 20px; text-align: center;"><span>Giỏ hàng trống</span></li>');
        cartFooter.hide();
        return;
    }
    
    cartItems.empty();
    
    data.cart.forEach(function(item) {
        // Use image_url from backend or fallback to item.image
        const imagePath = item.image_url || item.image || '/client/images/product/product-01.jpg';
        console.log('Cart item image path:', imagePath);
        console.log('Item data:', item);
        const html = `
            <li class="header-cart-item flex-w flex-t m-b-12">
                <div class="header-cart-item-img">
                    <img src="${imagePath}" alt="${item.name}">
                </div>
                <div class="header-cart-item-txt p-t-8">
                    <a href="/products/${item.product_id || item.id}" class="header-cart-item-name m-b-18 hov-cl1 trans-04">
                        ${item.name}
                    </a>
                    <span class="header-cart-item-info">
                        ${item.quantity} x ${formatCurrency(item.price)}
                    </span>
                </div>
                <button class="delete-item" data-cart-id="${item.id}" style="margin-left: auto; background: none; border: none; cursor: pointer;">
                    <i class="zmdi zmdi-close"></i>
                </button>
            </li>
        `;
        cartItems.append(html);
    });
    
    // Update total
    $('#totalAmount').text(formatCurrency(data.total));
    cartFooter.show();
    
    // Delete button events are handled globally in document ready
}

// Add to cart
function addToCart(productId, quantity = 1, size = null, color = null) {
    console.log('Adding to cart:', {productId, quantity, size, color});
    
    $.ajax({
        url: '/cart/add',
        method: 'POST',
        data: {
            product_id: productId,
            quantity: quantity,
            size: size,
            color: color,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            console.log('Cart add response:', response);
            if (response.success) {
                // Show success notification
                showCartNotification(response.message, 'success');
                
                // Reload cart
                loadCart();
                
                // Update cart count badge
                $('.js-show-cart[data-notify]').attr('data-notify', response.cart_count);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error adding to cart:', xhr, status, error);
            console.error('Response:', xhr.responseJSON);
            showCartNotification(xhr.responseJSON?.message || "Có lỗi xảy ra. Vui lòng thử lại", 'error');
        }
    });
}

// Remove from cart - Direct delete without confirmation
function removeFromCart(cartItemId) {
    $.ajax({
        url: '/cart/' + cartItemId,
        method: 'DELETE',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                // Reload cart without showing success message
                loadCart();
            }
        },
        error: function(error) {
            console.error('Error removing from cart:', error);
            loadCart();
        }
    });
}

// Update cart count display
function updateCartCount(count) {
    // Update all cart count displays
    $('.icon-header-noti[data-notify]').each(function() {
        $(this).attr('data-notify', count);
        if (count > 0) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
    
    // Also update mobile menu
    $('.mobile-menu-cart[data-notify]').each(function() {
        $(this).attr('data-notify', count);
        if (count > 0) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
}

// Format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(amount);
}

// Initialize on page load
$(document).ready(function() {
    loadCart();
    
    // Global event delegation for delete buttons
    $(document).on('click', '.delete-item', function(e) {
        e.preventDefault();
        const cartItemId = $(this).data('cart-id') || $(this).data('id');
        console.log('Delete button clicked for cart item:', cartItemId);
        removeFromCart(cartItemId);
    });
    
    // Watch for cart panel opening via mutation observer
    const cartPanel = $('.js-panel-cart');
    if (cartPanel.length > 0) {
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                    if ($(mutation.target).hasClass('show-header-cart')) {
                        // Panel is now open, load cart
                        loadCart();
                    }
                }
            });
        });
        
        observer.observe(cartPanel[0], {
            attributes: true,
            attributeFilter: ['class']
        });
    }
    
    // Override default add to cart button behavior
    $(document).on('click', '.js-addcart-detail', function(e) {
        e.preventDefault();
        
        // Get product ID from button
        const productId = $(this).data('product-id') || 
                          $(this).closest('[data-product-id]').data('product-id');
        
        // Get quantity from input
        const quantity = $(this).closest('.flex-w').find('.num-product').val() || 
                         $('.num-product').val() || 
                         1;
        
        // Get size and color if available
        const size = $(this).closest('.flex-w').find('.js-select2[name="size"]').val() || 
                     $('.js-select2[name="size"]').val();
        const color = $(this).closest('.flex-w').find('.js-select2[name="color"]').val() || 
                      $('.js-select2[name="color"]').val();
        
        if (productId) {
            addToCart(productId, quantity, size, color);
        } else {
            swal("Có lỗi xảy ra", "Không thể thêm sản phẩm vào giỏ hàng", "error");
        }
    });
    
    // Add to cart from product card (icon button)
    $(document).on('click', '.js-addcart-b2', function(e) {
        e.preventDefault();
        
        const productId = $(this).data('product-id');
        
        if (productId) {
            addToCart(productId, 1);
        } else {
            swal("Có lỗi xảy ra", "Không thể thêm sản phẩm vào giỏ hàng", "error");
        }
    });
    
    // Update cart count in icon badges periodically
    setInterval(function() {
        updateCartCount();
    }, 2000);
});

// Update cart count function
function updateCartCount() {
    $.ajax({
        url: '/cart/get',
        method: 'GET',
        success: function(response) {
            // Update all cart count badges
            $('.js-show-cart[data-notify]').attr('data-notify', response.count);
        },
        error: function(error) {
            // Silently fail
        }
    });
}

// Show cart notification in top right corner
function showCartNotification(message, type) {
    type = type || 'success';
    
    // Remove any existing notification
    $('.cart-notification').remove();
    
    // Create notification with better icons
    const icon = type === 'success' 
        ? '<i class="zmdi zmdi-check-circle"></i>' 
        : '<i class="zmdi zmdi-alert-circle"></i>';
    
    const notification = $(`
        <div class="cart-notification ${type}">
            ${icon}
            <span>${message}</span>
        </div>
    `);
    
    $('body').append(notification);
    
    // Animate in with delay
    setTimeout(function() {
        notification.addClass('show');
    }, 10);
    
    // Remove after 3 seconds with smooth animation
    setTimeout(function() {
        notification.removeClass('show');
        setTimeout(function() {
            notification.remove();
        }, 400);
    }, 3000);
}

