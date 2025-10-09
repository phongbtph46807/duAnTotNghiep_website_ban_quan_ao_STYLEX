<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ShippingCarrierRequest;
use App\Models\ShippingCarrier;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class ShippingCarrierController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');
        $carriers = ShippingCarrier::query()
            ->when($q, fn($x)=>$x->where('name','like',"%$q%"))
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.shipping_carriers.index', compact('carriers','q'));
    }

    public function create()
    {
        return view('admin.shipping_carriers.create');
    }

    public function store(ShippingCarrierRequest $request)
    {
        ShippingCarrier::create($request->validated());
        return redirect()->route('admin.shipping_carriers.index')
            ->with('success','Tạo đơn vị vận chuyển thành công');
    }

    public function edit(ShippingCarrier $shipping_carrier)
    {
        return view('admin.shipping_carriers.edit', compact('shipping_carrier'));
    }

    public function update(ShippingCarrierRequest $request, ShippingCarrier $shipping_carrier)
    {
        $shipping_carrier->update($request->validated());
        return redirect()->route('admin.shipping_carriers.index')
            ->with('success','Cập nhật đơn vị vận chuyển thành công');
    }

    public function destroy(ShippingCarrier $shipping_carrier)
    {
        try {
            // shipments.carrier_id dùng ON DELETE RESTRICT → nếu đã dùng, xóa sẽ lỗi.
            $shipping_carrier->delete();
            return back()->with('success','Đã xóa đơn vị vận chuyển');
        } catch (QueryException $e) {
            return back()->with('error','Không thể xóa vì đã phát sinh đơn hàng dùng đơn vị này.');
        }
    }
}
