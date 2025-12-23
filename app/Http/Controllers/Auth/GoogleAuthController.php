<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Two\InvalidStateException;
use GuzzleHttp\Exception\ClientException;

class GoogleAuthController extends Controller
{
    /**
     * Redirect user to Google OAuth
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback()
    {
        try {
            // Lấy thông tin user từ Google
            $googleUser = Socialite::driver('google')->user();
            
            // Validate email từ Google
            if (!$googleUser->email) {
                Log::error('Google OAuth: No email provided', [
                    'google_user_id' => $googleUser->id ?? 'unknown'
                ]);
                return redirect()->route('loginView')->with('error', 'Google không cung cấp email. Vui lòng thử lại hoặc đăng nhập bằng email/mật khẩu.');
            }

            // Tìm user đã tồn tại theo email (không phân biệt hoa thường)
            // Bao gồm cả user đã bị soft delete để restore nếu cần
            $user = User::withTrashed()
                ->whereRaw('LOWER(email) = ?', [strtolower($googleUser->email)])
                ->first();

            if ($user) {
                // User đã tồn tại - đăng nhập vào tài khoản này
                
                // Restore nếu user đã bị xóa
                if ($user->trashed()) {
                    $user->restore();
                }

                // Cập nhật thông tin từ Google
                $updated = false;
                
                // Cập nhật email_verified_at nếu chưa verify
                if (!$user->email_verified_at) {
                    $user->email_verified_at = now();
                    $updated = true;
                }

                // Cập nhật tên nếu Google có tên mới hơn
                if ($googleUser->name && $googleUser->name !== $user->name) {
                    $user->name = $googleUser->name;
                    $updated = true;
                }

                // Cập nhật avatar từ Google nếu có
                if ($googleUser->avatar) {
                    if (!$user->avatar || $user->avatar !== $googleUser->avatar) {
                        $user->avatar = $googleUser->avatar;
                        $updated = true;
                    }
                }

                // Lưu thay đổi nếu có
                if ($updated) {
                    $user->save();
                }

                // Đăng nhập vào tài khoản này
                Auth::login($user, true);

                // Merge session cart vào DB
                $this->mergeSessionCart($user);

                // Redirect dựa trên role
                if ($user->role == 1) {
                    // Admin - redirect về dashboard
                    return redirect()->route('admin.dashboard')->with('success', 'Đăng nhập thành công!');
                } elseif ($user->role == 2 || $user->role == 3) {
                    // Staff hoặc Warehouse Manager - redirect về trang đơn hàng
                    return redirect()->route('admin.orders.index')->with('success', 'Đăng nhập thành công!');
                }

                return redirect()->route('home')->with('success', 'Đăng nhập thành công!');
            } else {
                // User chưa tồn tại - tạo tài khoản mới
                try {
                    $user = User::create([
                        'name' => $googleUser->name ?? 'User',
                        'email' => $googleUser->email, // Email từ Google (đã được verify)
                        'password' => Hash::make(Str::random(32)), // Random password vì đăng nhập bằng Google
                        'email_verified_at' => now(), // Google đã verify email
                        'avatar' => $googleUser->avatar ?? null,
                        'role' => User::ROLE_USER,
                        'status' => 'active',
                        'is_verified' => 1, // Đã verify qua Google
                    ]);

                    Auth::login($user, true);

                    // Merge session cart vào DB
                    $this->mergeSessionCart($user);

                    return redirect()->route('home')->with('success', 'Đăng ký và đăng nhập thành công!');
                } catch (\Exception $createException) {
                    Log::error('Google OAuth: Error creating user', [
                        'email' => $googleUser->email,
                        'error' => $createException->getMessage(),
                        'trace' => $createException->getTraceAsString()
                    ]);
                    
                    // Nếu lỗi do email đã tồn tại, thử tìm lại
                    $existingUser = User::withTrashed()
                        ->whereRaw('LOWER(email) = ?', [strtolower($googleUser->email)])
                        ->first();
                    
                    if ($existingUser) {
                        // Restore và đăng nhập
                        if ($existingUser->trashed()) {
                            $existingUser->restore();
                        }
                        Auth::login($existingUser, true);
                        return redirect()->route('home')->with('success', 'Đăng nhập thành công!');
                    }
                    
                    return redirect()->route('loginView')->with('error', 'Không thể tạo tài khoản. Email có thể đã được sử dụng. Vui lòng thử đăng nhập bằng email/mật khẩu.');
                }
            }
        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            // Lỗi do session expired hoặc state mismatch
            Log::warning('Google OAuth: InvalidStateException', [
                'error' => $e->getMessage()
            ]);
            return redirect()->route('loginView')->with('error', 'Phiên đăng nhập đã hết hạn. Vui lòng thử lại.');
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            // Lỗi từ Google API
            Log::error('Google OAuth: ClientException', [
                'error' => $e->getMessage(),
                'response' => $e->getResponse()->getBody()->getContents() ?? 'no response'
            ]);
            return redirect()->route('loginView')->with('error', 'Lỗi kết nối với Google. Vui lòng kiểm tra cấu hình OAuth và thử lại.');
        } catch (\Exception $e) {
            Log::error('Google OAuth Error: ' . $e->getMessage(), [
                'email' => $googleUser->email ?? 'unknown',
                'class' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('loginView')->with('error', 'Đăng nhập bằng Google thất bại: ' . $e->getMessage() . '. Vui lòng thử lại hoặc đăng nhập bằng email/mật khẩu.');
        }
    }

    /**
     * Merge session cart vào database cart
     */
    private function mergeSessionCart($user)
    {
        try {
            $sessionItems = session()->get('cart.items', []);
            if (!empty($sessionItems)) {
                foreach ($sessionItems as $item) {
                    $existingCart = Cart::where('user_id', $user->id)
                        ->where('product_id', $item['product_id'])
                        ->where('variant_id', $item['variant_id'] ?? null)
                        ->first();

                    if ($existingCart) {
                        // Cộng số lượng
                        $existingCart->quantity += $item['quantity'];
                        $existingCart->save();
                    } else {
                        // Tạo mới
                        Cart::create([
                            'user_id' => $user->id,
                            'product_id' => $item['product_id'],
                            'variant_id' => $item['variant_id'] ?? null,
                            'quantity' => $item['quantity'],
                        ]);
                    }
                }
                // Clear session cart
                session()->forget('cart.items');
            }
        } catch (\Exception $e) {
            Log::error('Error merging cart: ' . $e->getMessage());
        }
    }
}

