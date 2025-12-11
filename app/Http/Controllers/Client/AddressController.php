<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AddressController extends Controller
{
    /**
     * Hiển thị danh sách địa chỉ
     */
    public function index()
    {
        $addresses = auth()->user()->addresses()->orderBy('is_default', 'desc')->orderBy('created_at', 'desc')->get();
        return view('client.profile.addresses.index', compact('addresses'));
    }

    /**
     * Hiển thị form tạo địa chỉ mới
     */
    public function create()
    {
        return view('client.profile.addresses.create');
    }

    /**
     * Lưu địa chỉ mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'city' => 'required|string|max:255',
            'district' => 'nullable|string|max:255',
            'ward' => 'nullable|string|max:255',
            'address' => 'required|string',
            'is_default' => 'nullable|boolean',
            'address_type' => 'required|in:home,office,other',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // Nếu đặt làm mặc định, bỏ mặc định của các địa chỉ khác
                if ($request->is_default) {
                    Address::where('user_id', auth()->id())
                        ->update(['is_default' => false]);
                }

                Address::create([
                    'user_id' => auth()->id(),
                    'full_name' => $request->full_name,
                    'phone' => $request->phone,
                    'email' => $request->email,
                    'city' => $request->city,
                    'district' => $request->district,
                    'ward' => $request->ward,
                    'address' => $request->address,
                    'is_default' => $request->is_default ?? false,
                    'address_type' => $request->address_type,
                ]);
            });

            return redirect()->route('client.profile.addresses.index')->with('success', 'Thêm địa chỉ thành công!');
        } catch (\Exception $e) {
            Log::error('Error creating address: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra, vui lòng thử lại sau')->withInput();
        }
    }

    /**
     * Hiển thị form sửa địa chỉ
     */
    public function edit(Address $address)
    {
        // Kiểm tra quyền sở hữu
        if ($address->user_id !== auth()->id()) {
            abort(403);
        }

        return view('client.profile.addresses.edit', compact('address'));
    }

    /**
     * Cập nhật địa chỉ
     */
    public function update(Request $request, Address $address)
    {
        // Kiểm tra quyền sở hữu
        if ($address->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'city' => 'required|string|max:255',
            'district' => 'nullable|string|max:255',
            'ward' => 'nullable|string|max:255',
            'address' => 'required|string',
            'is_default' => 'nullable|boolean',
            'address_type' => 'required|in:home,office,other',
        ]);

        try {
            DB::transaction(function () use ($request, $address) {
                // Nếu đặt làm mặc định, bỏ mặc định của các địa chỉ khác
                if ($request->is_default && !$address->is_default) {
                    Address::where('user_id', auth()->id())
                        ->where('id', '!=', $address->id)
                        ->update(['is_default' => false]);
                }

                $address->update([
                    'full_name' => $request->full_name,
                    'phone' => $request->phone,
                    'email' => $request->email,
                    'city' => $request->city,
                    'district' => $request->district,
                    'ward' => $request->ward,
                    'address' => $request->address,
                    'is_default' => $request->is_default ?? false,
                    'address_type' => $request->address_type,
                ]);
            });

            return redirect()->route('client.profile.addresses.index')->with('success', 'Cập nhật địa chỉ thành công!');
        } catch (\Exception $e) {
            Log::error('Error updating address: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra, vui lòng thử lại sau')->withInput();
        }
    }

    /**
     * Xóa địa chỉ
     */
    public function destroy(Address $address)
    {
        // Kiểm tra quyền sở hữu
        if ($address->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xóa địa chỉ này!'
            ], 403);
        }

        try {
            $address->delete();
            return response()->json([
                'success' => true,
                'message' => 'Xóa địa chỉ thành công!'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting address: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra, vui lòng thử lại sau'
            ], 500);
        }
    }

    /**
     * Đặt địa chỉ làm mặc định
     */
    public function setDefault(Address $address)
    {
        // Kiểm tra quyền sở hữu
        if ($address->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thay đổi địa chỉ này!'
            ], 403);
        }

        try {
            DB::transaction(function () use ($address) {
                // Bỏ mặc định của tất cả địa chỉ
                Address::where('user_id', auth()->id())
                    ->update(['is_default' => false]);

                // Đặt địa chỉ này làm mặc định
                $address->update(['is_default' => true]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Đặt địa chỉ mặc định thành công!'
            ]);
        } catch (\Exception $e) {
            Log::error('Error setting default address: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra, vui lòng thử lại sau'
            ], 500);
        }
    }
}

