<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * Hiển thị trang hồ sơ cá nhân
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $activeTab = $request->query('tab', 'profile'); // 'profile', 'orders', 'reviews', 'card'
        
        // Đơn hàng
        $orders = collect();
        $statusTabs = [];
        $activeStatus = null;
        
        // Đánh giá
        $reviews = null;

        // Ví
        $walletHistory = [];
        
        if ($activeTab === 'orders') {
            $userId = Auth::id();
            $sessionId = session()->getId();
            $statusTabs = [
                'pending' => 'Chờ xác nhận',
                'processing' => 'Đang xử lý',
                'shipping' => 'Chờ giao hàng',
                'delivered' => 'Đã giao',
                'completed' => 'Hoàn thành',
                'cancelled' => 'Hủy',
                'returned' => 'Trả hàng',
            ];
            $statusFilter = $request->query('status');
            if ($statusFilter && !array_key_exists($statusFilter, $statusTabs)) {
                $statusFilter = null;
            }
            $activeStatus = $statusFilter;

            $orders = \App\Models\Order::query()
                ->when($userId, function ($q) use ($userId, $sessionId) {
                    $q->where(function ($qq) use ($userId, $sessionId) {
                        $qq->where('user_id', $userId);
                        if ($sessionId) {
                            $qq->orWhere(function ($qq2) use ($sessionId) {
                                $qq2->whereNull('user_id')
                                    ->where('session_id', $sessionId);
                            });
                        }
                        $email = Auth::user()->email ?? null;
                        if ($email) {
                            $qq->orWhere(function ($qq3) use ($email) {
                                $qq3->whereNull('user_id')
                                    ->where('email', $email);
                            });
                        }
                    });
                })
                ->when(!$userId, function ($q) use ($sessionId) {
                    $q->where('session_id', $sessionId);
                })
                ->when($statusFilter, function ($q) use ($statusFilter) {
                    if ($statusFilter === 'cancelled') {
                        $q->whereIn('status', ['cancel_request', 'cancelled']);
                    } elseif ($statusFilter === 'returned') {
                        $q->whereIn('status', ['return_request', 'returned']);
                    } else {
                        $q->where('status', $statusFilter);
                    }
                })
                ->orderByDesc('created_at')
                ->with([
                    'items.product.productVariants.texture',
                    'items.product.productImages',
                    'items.product.primaryImage',
                    'items.variant',
                    'items.variant.size',
                    'items.variant.color',
                    'items.variant.texture',
                    'reviews'
                ])
                ->get();

            // Đánh dấu sản phẩm đã được đánh giá
            foreach ($orders as $order) {
                if (in_array($order->status, ['completed', 'delivered'])) {
                    $reviewed = $order->reviews->map(fn($r) => [
                        'p' => $r->product_id,
                        'v' => $r->product_variant_id,
                    ])->toArray();
                    foreach ($order->items as $item) {
                        $item->is_reviewed = collect($reviewed)->contains(function($rev) use ($item) {
                            return $rev['p'] == $item->product_id && $rev['v'] == $item->variant_id;
                        });
                    }
                }
            }
        } elseif ($activeTab === 'reviews') {
            // Load reviews của user với thông tin sản phẩm
            try {
                $reviews = \App\Models\Review::where('user_id', $user->id)
                    ->with([
                        'product' => function($q) {
                            $q->withTrashed(); // Bao gồm cả sản phẩm đã xóa
                        },
                        'productVariant.size',
                        'productVariant.color',
                        'productVariant.texture',
                        'product.primaryImage',
                        'order' => function($q) {
                            $q->select('id', 'code');
                        }
                    ])
                    ->orderByDesc('created_at')
                    ->paginate(10);
            } catch (\Exception $e) {
                Log::error('Error loading reviews: ' . $e->getMessage());
                // Tạo empty paginator khi có lỗi
                $reviews = \App\Models\Review::where('id', 0)->paginate(10);
            }
        } elseif ($activeTab === 'card') {
            // Ví của tôi - chuẩn bị lịch sử giao dịch
        $walletHistory = collect($user->wallet_history ?? [])
            ->sortByDesc(function ($item) {
                // sort theo created_at (string) -> strtotime
                return strtotime($item['created_at'] ?? '1970-01-01 00:00:00');
            })
            ->values()
            ->all();
        }
        
        // Đảm bảo $reviews luôn được khởi tạo
        if ($reviews === null) {
            $reviews = \App\Models\Review::where('id', 0)->paginate(10);
        }
        
        return view('client.profile.index', compact(
            'user',
            'activeTab',
            'orders',
            'statusTabs',
            'activeStatus',
            'reviews',
            'walletHistory'
        ));
    }

    public function card(Request $request)
    {
        // Điều hướng về trang profile với tab = card
        $request->merge(['tab' => 'card']);
        return $this->index($request);
    }

    /**
     * Trang danh sách "Đánh giá của tôi"
     * Tạo route riêng để người dùng bấm từ menu ngoài.
     */
    public function reviews(Request $request)
    {
        // Ép tab = reviews và tái sử dụng logic của index()
        $request->merge(['tab' => 'reviews']);
        return $this->index($request);
    }

    public function withdraw(Request $request)
    {
        $request->validate([
            'bank_code'      => 'required|string|max:20',
            'account_number' => 'required|string|max:50',
            'account_name'   => 'required|string|max:100',
            'amount'         => 'required|integer|min:100000', // tối thiểu 100k
            'note'           => 'nullable|string|max:255',
        ], [
            'amount.min' => 'Số tiền rút tối thiểu là 100.000 ₫.',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $user = User::query()->whereKey(Auth::id())->lockForUpdate()->firstOrFail();

                $amount = (int) $request->amount;

                // check số dư
                if ($amount > (int) $user->wallet_balance) {
                    throw ValidationException::withMessages([
                        'amount' => 'Số tiền rút vượt quá số dư ví.',
                    ]);
                }

                $currentBalance = (int) $user->wallet_balance;

                // Tạo yêu cầu rút tiền trong bảng withdraw_requests
                // KHÔNG trừ tiền ngay - chờ admin duyệt và hoàn thành mới trừ
                $withdrawRequest = \App\Models\WithdrawRequest::create([
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'bank_code' => $request->bank_code,
                    'account_number' => $request->account_number,
                    'account_name' => $request->account_name,
                    'note' => $request->note,
                    'status' => 'pending',
                ]);

                // Thêm vào lịch sử ví (chỉ để ghi nhận yêu cầu, chưa trừ tiền)
                $history = $user->wallet_history ?? [];
                if (!is_array($history)) $history = [];

                $history[] = [
                    'note' => $request->note ?: 'Yêu cầu rút tiền (Chờ duyệt)',
                    'type' => 'withdraw_request',
                    'amount' => $amount,
                    'balance_before' => $currentBalance,
                    'balance_after' => $currentBalance, // Chưa trừ tiền
                    'order_id' => '-',
                    'order_code' => '-',
                    'withdraw_request_id' => $withdrawRequest->id,
                    'status' => 'pending', // Trạng thái yêu cầu
                    'created_at' => now()->toDateTimeString(),
                    'created_by' => $user->id,
                    'created_by_name' => $user->name,
                    'bank_code' => $request->bank_code,
                    'account_number' => $request->account_number,
                    'account_name' => $request->account_name,
                ];

                // KHÔNG trừ tiền - chỉ lưu lịch sử yêu cầu
                $user->wallet_history = $history;
                $user->save();
            });

            return back()->with('success', 'Đã gửi yêu cầu rút tiền. Vui lòng chờ duyệt.');
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('Error creating withdraw request: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'amount' => $request->amount,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Có lỗi khi rút tiền: ' . $e->getMessage());
        }
    }

    /**
     * Cập nhật thông tin hồ sơ
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'current_password' => 'nullable|string',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        try {
            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
            ];

            // Xử lý đổi mật khẩu
            if ($request->filled('current_password') && $request->filled('password')) {
                if (!Hash::check($request->current_password, $user->password)) {
                    return redirect()->back()->with('error', 'Mật khẩu hiện tại không đúng!')->withInput();
                }
                $updateData['password'] = Hash::make($request->password);
            }

            // Xử lý upload avatar
            if ($request->hasFile('avatar')) {
                // Xóa avatar cũ nếu có
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }

                // Lưu avatar mới
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                $updateData['avatar'] = $avatarPath;
            }

            $user->update($updateData);

            return redirect()->route('client.profile.index')->with('success', 'Cập nhật thông tin thành công!');
        } catch (\Exception $e) {
            Log::error('Error updating profile: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra, vui lòng thử lại sau')->withInput();
        }
    }
}