<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function index()
    {
        $vouchers = Voucher::orderByDesc('created_at')->paginate(12);

        $now = now();
        $stats = [
            'total' => Voucher::count(),
            'active' => Voucher::where('is_active', true)->count(),
            'valid_now' => Voucher::where('is_active', true)
                ->where(function ($q) use ($now) {
                    $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
                })
                ->where(function ($q) use ($now) {
                    $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
                })
                ->count(),
            'percent_type' => Voucher::where('type', 'percent')->count(),
            'fixed_type' => Voucher::where('type', 'fixed')->count(),
            'used_total' => (int) Voucher::sum('used_count'),
        ];

        return view('admin.vouchers.index', compact('vouchers', 'stats'));
    }

    public function create()
    {
        return view('admin.vouchers.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        Voucher::create($data);
        return redirect()->route('admin.vouchers.index')->with('success', 'Tạo voucher thành công');
    }

    public function edit(Voucher $voucher)
    {
        return view('admin.vouchers.edit', compact('voucher'));
    }

    public function update(Request $request, Voucher $voucher)
    {
        $data = $this->validateData($request, $voucher->id);
        $voucher->update($data);
        return redirect()->route('admin.vouchers.index')->with('success', 'Cập nhật voucher thành công');
    }

    public function destroy(Voucher $voucher)
    {
        $voucher->delete();
        return redirect()->route('admin.vouchers.index')->with('success', 'Đã xóa voucher');
    }

    private function validateData(Request $request, ?int $ignoreId = null): array
    {
        $uniqueRule = 'unique:vouchers,code';
        if ($ignoreId) {
            $uniqueRule .= ',' . $ignoreId;
        }

        return $request->validate([
            'code' => ['required', 'string', 'max:50', $uniqueRule],
            'description' => ['nullable', 'string', 'max:255'],
            'type' => ['required', 'in:percent,fixed'],
            'value' => ['required', 'numeric', 'min:0'],
            'max_discount_amount' => ['nullable', 'numeric', 'min:0'],
            'min_order_amount' => ['nullable', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['sometimes', 'boolean'],
        ]) + [
            'is_active' => $request->boolean('is_active'),
        ];
    }
}


