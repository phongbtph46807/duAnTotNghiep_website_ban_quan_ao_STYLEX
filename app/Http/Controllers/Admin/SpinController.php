<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Spin;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SpinController extends Controller
{
    public function index(Request $request)
    {
        $query = Spin::with('voucher');

        // Filter by name
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active' ? 1 : 0);
        }

        $items = $query->orderBy('id', 'desc')->paginate(10);

        // Statistics
        $spinCounts = (object) [
            'total_spins' => Spin::count(),
            'active_spins' => Spin::where('is_active', 1)->count(),
            'inactive_spins' => Spin::where('is_active', 0)->count(),
            'total_spun' => DB::table('spin_users')->count(),
        ];

        return view('admin.spins.index', compact('items', 'spinCounts'));
    }

    public function create()
    {
        $vouchers = Voucher::where('is_active', 1)->get();
        return view('admin.spins.create', compact('vouchers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'voucher_id' => 'nullable|exists:vouchers,id',
            'probability' => 'required|integer|min:1|max:100',
            'is_active' => 'boolean',
        ]);

        Spin::create([
            'name' => $request->name,
            'voucher_id' => $request->voucher_id,
            'probability' => $request->probability,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('admin.spins.index')->with('success', 'Thêm phần thưởng thành công!');
    }

    public function show(Spin $spin)
    {
        $spin->load(['voucher', 'spinUsers.user']);
        return view('admin.spins.show', compact('spin'));
    }

    public function edit(Spin $spin)
    {
        $vouchers = Voucher::where('is_active', 1)->get();
        return view('admin.spins.edit', compact('spin', 'vouchers'));
    }

    public function update(Request $request, Spin $spin)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'voucher_id' => 'nullable|exists:vouchers,id',
            'probability' => 'required|integer|min:1|max:100',
            'is_active' => 'boolean',
        ]);

        $spin->update([
            'name' => $request->name,
            'voucher_id' => $request->voucher_id,
            'probability' => $request->probability,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('admin.spins.index')->with('success', 'Cập nhật phần thưởng thành công!');
    }

    public function destroy(Spin $spin)
    {
        $spin->delete();
        return redirect()->route('admin.spins.index')->with('success', 'Xóa phần thưởng thành công!');
    }

    public function toggleStatus(Request $request, Spin $spin)
    {
        $spin->update(['is_active' => $request->is_active]);
        return response()->json(['message' => 'Cập nhật trạng thái thành công!']);
    }
}
