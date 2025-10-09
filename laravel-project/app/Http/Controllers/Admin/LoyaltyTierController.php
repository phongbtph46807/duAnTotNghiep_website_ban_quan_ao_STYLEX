<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoyaltyTier;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\StoreLoyaltyTierRequest;
use App\Http\Requests\Admin\UpdateLoyaltyTierRequest;

class LoyaltyTierController extends Controller
{

    public function index()
    {
        $tiers = LoyaltyTier::all();
        return view('admin.loyalty-tiers.index', compact('tiers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.loyalty-tiers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLoyaltyTierRequest $request)
    {
        $data = $request->validated();
        LoyaltyTier::create($data);
        return redirect()->route('admin.loyalty-tiers.index')->with('success', 'Loyalty tier created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LoyaltyTier $loyaltyTier)
    {
        return view('admin.loyalty-tiers.edit', compact('loyaltyTier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLoyaltyTierRequest $request, LoyaltyTier $loyaltyTier)
    {
        $data = $request->validated();
        $loyaltyTier->update($data);
        return redirect()->route('admin.loyalty-tiers.index')->with('success', 'Loyalty tier updated successfully.');
    }   

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LoyaltyTier $loyaltyTier)
    {
        $loyaltyTier->delete();
        return redirect()->route('admin.loyalty-tiers.index')->with('success', 'Loyalty tier deleted successfully.');
    }
}

