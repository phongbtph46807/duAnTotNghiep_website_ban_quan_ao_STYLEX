<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SpinPrize;
use App\Models\Voucher;
use Illuminate\Http\Request;

class SpinPrizeController extends Controller
{
    const OBJECT = 'admin.spin';
    const DOT = '.';

    public function index()
    {
        $spinPrizes = SpinPrize::with('voucher')->get();
        return view(self::OBJECT . self::DOT . __FUNCTION__, compact('spinPrizes'));
    }

    public function create()
    {
        $vouchers = Voucher::all();
        return view(self::OBJECT . self::DOT . __FUNCTION__, compact('vouchers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'probability' => 'required|numeric|min:0|max:1',
            'type' => 'required|in:VOUCHER,LOYALTY_POINTS,NONE',
            'value_reference' => 'nullable|string|max:100',
        ]);

        SpinPrize::create($request->all());

        return redirect()->route('admin.spin.index')->with('success', 'Thêm phần thưởng thành công!');
    }

    public function edit(SpinPrize $spin)
    {
        $vouchers = Voucher::all();
        return view('admin.spin.edit', compact('spin', 'vouchers'));
    }

    public function update(Request $request, SpinPrize $spin)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'probability' => 'required|numeric|min:0|max:1',
            'type' => 'required|in:VOUCHER,LOYALTY_POINTS,NONE',
            'value_reference' => 'nullable|string|max:100',
        ]);

        $spin->update($request->all());

        return redirect()->route('admin.spin.index')->with('success', 'Cập nhật thành công!');
    }

    public function destroy(SpinPrize $spin)
    {
        $spin->delete();
        return redirect()->route('admin.spin.index')->with('success', 'Đã xóa phần thưởng!');
    }

}
